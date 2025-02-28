<?php
/**
 * Template Name: Post
 */

// ------------------------------
// (1) 로그인/권한 체크 + 글쓰기 로직
// ------------------------------
$current_user = wp_get_current_user();
$is_admin  = current_user_can('administrator');
$is_editor = current_user_can('editor');

// 로그인 안 됐다면 로그인 페이지로 이동
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( home_url('/') ) );
    exit;
}

// 게시판별 글쓰기 권한
$allowed_categories = array(
    'blog'      => $is_admin,               // Admin만
    'novel'     => $is_admin,               // Admin만
    'spinoff'   => $is_editor || $is_admin, // Editor, Admin
    'community' => true                     // 누구나
);

// 폼 제출 시(POST) 글 작성 처리
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post']) ) {
    $post_section = sanitize_text_field( $_POST['post_section'] );
    if ( ! isset($allowed_categories[$post_section]) || ! $allowed_categories[$post_section] ) {
        wp_die('🚫 해당 게시판에 글을 작성할 권한이 없습니다.');
    }

    // 제목
    $post_title   = sanitize_text_field( $_POST['post_title'] );
    // 내용(Quill에서 넘어온 HTML)
    $post_content = wp_kses_post( $_POST['post_content'] );

    // 카테고리, 태그
    $post_category = isset($_POST['post_category']) ? array_map('intval', $_POST['post_category']) : array();
    $post_tags    = isset($_POST['post_tags']) ? sanitize_text_field($_POST['post_tags']) : '';

    // 새 글 정보
    $new_post = array(
        'post_title'    => $post_title,
        'post_content'  => $post_content,
        'post_status'   => 'publish', // 바로 게시
        'post_author'   => get_current_user_id(),
        'post_category' => $post_category,
        'tags_input'    => explode(',', $post_tags),
    );

    // 글 작성
    $post_id = wp_insert_post( $new_post );
    if ( $post_id ) {
        // 작성 성공 시 이동
        $redirect_url = home_url( "/{$post_section}/?success=1" );
        wp_redirect( $redirect_url );
        exit;
    }
}

// ------------------------------
// (2) 카테고리 목록
// ------------------------------
$category_options = array(
    'blog'      => get_categories( array('include' => array(8, 9, 10, 11, 12), 'hide_empty' => false) ),
    'novel'     => get_categories( array('include' => array(17, 18),         'hide_empty' => false) ),
    'spinoff'   => get_categories( array('include' => array(20),             'hide_empty' => false) ),
    'community' => get_categories( array('include' => array(22),             'hide_empty' => false) )
);

// ------------------------------
// (3) 헤더 출력
// ------------------------------
get_header();
?>

<!-- Quill CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">

<div class="max-w-3xl mx-auto p-6 bg-base-100 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4">📝 새 글 작성 (Quill)</h2>

    <form method="post">
        <!-- 게시판 선택 -->
        <label class="block mb-2 text-lg font-semibold">게시할 곳 선택</label>
        <select name="post_section" id="post_section" class="w-full p-2 border rounded-md" required>
            <option value="">-- 선택 --</option>
            <?php foreach ($allowed_categories as $section => $allowed): ?>
                <?php if ($allowed): ?>
                    <option value="<?php echo esc_attr($section); ?>">
                        <?php echo ucfirst($section); ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

        <!-- 카테고리 선택(동적으로 변경) -->
        <div id="category_section" class="hidden mt-4">
            <label class="block mb-2 text-lg font-semibold">카테고리 선택</label>
            <div id="category_options" class="flex flex-wrap gap-2"></div>
        </div>

        <!-- 제목 -->
        <label class="block mt-4 mb-2 text-lg font-semibold">제목</label>
        <input type="text" name="post_title" class="w-full p-2 border rounded-md" required>

        <!-- 내용(Quill) -->
        <label class="block mt-4 mb-2 text-lg font-semibold">내용</label>
        <!-- Quill 에디터 영역 -->
        <div id="quill-editor" style="height: 300px;"></div>
        <!-- 실제 폼 전송용 hidden input -->
        <input type="hidden" name="post_content" id="post_content_input" />

        <!-- 태그 -->
        <label class="block mt-4 mb-2 text-lg font-semibold">태그</label>
        <div id="tag-container" class="flex flex-wrap gap-2"></div>
        <input type="text" id="tag-input" class="w-full p-2 border rounded-md" 
               placeholder="태그 입력 후 Enter" autocomplete="off">
        <input type="hidden" name="post_tags" id="post_tags">

        <button type="submit" name="submit_post" class="mt-6 w-full p-3 bg-primary text-white rounded-md">
            작성 완료
        </button>
    </form>
</div>

<script>
// ------------------------------
// 카테고리 선택
// ------------------------------
document.getElementById('post_section').addEventListener('change', function () {
    let section = this.value;
    let categories = {
        'blog': <?php echo json_encode($category_options['blog']); ?>,
        'novel': <?php echo json_encode($category_options['novel']); ?>,
        'spinoff': <?php echo json_encode($category_options['spinoff']); ?>,
        'community': <?php echo json_encode($category_options['community']); ?>
    };

    let categoryOptions = document.getElementById('category_options');
    categoryOptions.innerHTML = '';

    if (categories[section]) {
        document.getElementById('category_section').classList.remove('hidden');
        categories[section].forEach(cat => {
            let label = document.createElement('label');
            label.innerHTML = `
                <input type="checkbox" name="post_category[]" 
                       value="${cat.term_id}" class="form-checkbox"> ${cat.name}
            `;
            categoryOptions.appendChild(label);
        });
    } else {
        document.getElementById('category_section').classList.add('hidden');
    }
});

// ------------------------------
// 태그 입력
// ------------------------------
let tagContainer = document.getElementById('tag-container');
let tagInput = document.getElementById('tag-input');
let hiddenTags = document.getElementById('post_tags');
let tags = [];

tagInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter' && tagInput.value.trim() !== '') {
        e.preventDefault();
        let tagText = tagInput.value.trim();
        if (!tags.includes(tagText)) {
            tags.push(tagText);
            let span = document.createElement('span');
            span.classList.add('bg-gray-200', 'px-2', 'py-1', 'rounded-md');
            span.textContent = `#${tagText}`;
            tagContainer.appendChild(span);
            hiddenTags.value = tags.join(',');
        }
        tagInput.value = '';
    }
});
</script>

<!-- Quill JS -->
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

<script>
// ------------------------------
// Quill 초기화
// ------------------------------
document.addEventListener('DOMContentLoaded', function() {
  // 툴바 옵션 예시
  var toolbarOptions = [
    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
    ['link', 'image', 'video'],                       // 링크, 이미지, 비디오
    [{ 'header': 1 }, { 'header': 2 }],               // custom button values
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    [{ 'align': [] }],
    ['clean']                                         // remove formatting
  ];

  // Quill 에디터 생성
  var quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
      toolbar: toolbarOptions
      // 이미지 업로드 등 고급 기능은 별도 플러그인 필요
    }
  });

  // 폼 전송 시, quill 내용을 hidden input에 넣음
  var form = document.querySelector('form');
  form.addEventListener('submit', function(e) {
    var html = quill.root.innerHTML;
    document.getElementById('post_content_input').value = html;
  });
});
</script>

<?php get_footer(); ?>