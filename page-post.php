<?php
/*
Template Name: Post
*/

/**
 * 1) 로그인/권한 체크 + 글쓰기 로직(리다이렉트 등)은
 *    get_header() 호출 "이전"에 처리해야 합니다.
 */

// 현재 사용자 정보
$current_user = wp_get_current_user();
$is_admin  = current_user_can('administrator'); // Admin 권한 확인
$is_editor = current_user_can('editor');        // Editor 권한 확인

// 로그인되지 않은 사용자는 로그인 페이지로 리디렉트
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( home_url('/') ) );
    exit;
}

// 게시판별 글쓰기 권한
$allowed_categories = array(
    'blog'      => $is_admin,               // Blog (Admin만)
    'novel'     => $is_admin,               // Novel (Admin만)
    'spinoff'   => $is_editor || $is_admin, // Spinoff (Editor, Admin)
    'community' => true                     // Community (누구나)
);

// 폼 제출 시 글 저장 처리
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post']) ) {
    $post_section = sanitize_text_field( $_POST['post_section'] );

    // 권한 확인
    if ( ! isset($allowed_categories[$post_section]) || ! $allowed_categories[$post_section] ) {
        wp_die('🚫 해당 게시판에 글을 작성할 권한이 없습니다.');
    }

    $post_title   = sanitize_text_field( $_POST['post_title'] );
    $post_content = wp_kses_post( $_POST['post_content'] );
    $post_category = isset($_POST['post_category']) ? array_map('intval', $_POST['post_category']) : array();
    $post_tags    = isset($_POST['post_tags']) ? sanitize_text_field($_POST['post_tags']) : '';

    // 새 글 정보
    $new_post = array(
        'post_title'    => $post_title,
        'post_content'  => $post_content,
        'post_status'   => 'publish', // 검토 후 게시
        'post_author'   => get_current_user_id(),
        'post_category' => $post_category,
        'tags_input'    => explode(',', $post_tags),
    );

    // 글 작성
    $post_id = wp_insert_post( $new_post );

    // 작성 성공 시 해당 게시판 페이지로 이동
    if ( $post_id ) {
        $redirect_url = home_url( "/{$post_section}/?success=1" );
        wp_redirect( $redirect_url );
        exit;
    }
}

// -----------------------------------------------------------------
// 여기까지가 "화면 출력 전" 처리(리다이렉트/권한/저장 로직)

// 게시판별 카테고리 목록
$category_options = array(
    'blog'      => get_categories( array('include' => array(8, 9, 10, 11, 12), 'hide_empty' => false) ),
    'novel'     => get_categories( array('include' => array(17, 18),         'hide_empty' => false) ),
    'spinoff'   => get_categories( array('include' => array(20),             'hide_empty' => false) ),
    'community' => get_categories( array('include' => array(22),             'hide_empty' => false) )
);

// 에디터 설정 (Classic Editor + Advanced Editor Tools 플러그인 필요)
$editor_settings = array(
    'textarea_name' => 'post_content',
    'media_buttons' => true,  // "미디어 추가" 버튼
    'teeny'         => false, // 전체 기능
    'tinymce'       => array(
        'height'  => 300,
        // 필요한 TinyMCE 플러그인 (표, 폰트 크기, 색상 등)
        'plugins' => 'lists,link,image,code,table,advlist,fullscreen,charmap,hr',
        // 툴바 설정 (예시)
        'toolbar1' => 'formatselect,fontselect,fontsizeselect,bold,italic,underline,'.
                      'alignleft,aligncenter,alignright,bullist,numlist,link,image,table,code,undo,redo',
    ),
    'quicktags' => true, // HTML 모드
);

// 이제 헤더(HTML) 출력 시작
get_header();
?>

<div class="max-w-3xl mx-auto p-6 bg-base-100 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4">📝 새 글 작성</h2>

    <form method="post">
        <!-- 어디에 게시할지 선택 -->
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

        <!-- 카테고리 선택 (동적으로 변경) -->
        <div id="category_section" class="hidden mt-4">
            <label class="block mb-2 text-lg font-semibold">카테고리 선택</label>
            <div id="category_options" class="flex flex-wrap gap-2"></div>
        </div>

        <label class="block mt-4 mb-2 text-lg font-semibold">제목</label>
        <input type="text" name="post_title" class="w-full p-2 border rounded-md" required>

        <!-- 내용 (wp_editor) -->
        <label class="block mt-4 mb-2 text-lg font-semibold">내용</label>
        <?php
        // 기존 textarea 대신 wp_editor 사용
        wp_editor( '', 'post_content_editor_id', $editor_settings );
        ?>

        <!-- 태그 입력 -->
        <label class="block mt-4 mb-2 text-lg font-semibold">태그</label>
        <div id="tag-container" class="flex flex-wrap gap-2"></div>
        <input type="text" id="tag-input" class="w-full p-2 border rounded-md" placeholder="태그 입력 후 Enter" autocomplete="off">
        <input type="hidden" name="post_tags" id="post_tags">

        <button type="submit" name="submit_post" class="mt-6 w-full p-3 bg-primary text-white rounded-md">
            작성 완료
        </button>
    </form>
</div>

<script>
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
            label.innerHTML = `<input type="checkbox" name="post_category[]" value="${cat.term_id}" class="form-checkbox"> ${cat.name}`;
            categoryOptions.appendChild(label);
        });
    } else {
        document.getElementById('category_section').classList.add('hidden');
    }
});

// 태그 추가 기능
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

<?php get_footer(); ?>