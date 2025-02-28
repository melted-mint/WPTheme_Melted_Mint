<?php
/*
Template Name: Post
*/

// 1) 로그인/권한 체크
$current_user = wp_get_current_user();
$is_admin  = current_user_can('administrator');
$is_contributor = current_user_can('contributor');
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( home_url('/') ) );
    exit;
}

// 2) 페이지 선택 권한 (슬러그 기준)
$allowed_pages = array(
    'blog'      => ($is_admin),
    'novel'     => ($is_admin),
    'spinoff'   => ($is_contributor || $is_admin),
    'community' => true
);

// 3) 페이지별 카테고리 매핑 (동적 표시용)
$page_category_map = array(
    'blog'      => array(8, 9, 10, 11, 12),
    'novel'     => array(38, 18),
    'spinoff'   => array(20),
    'community' => array(9, 10, 11, 12, 34)
);
// 각 카테고리의 이름도 가져와서 매핑 (버튼 표시용)
$page_category_detailed = array();
foreach ($page_category_map as $page_slug => $cat_ids) {
    $page_category_detailed[$page_slug] = array();
    foreach ($cat_ids as $cid) {
        $term = get_category($cid);
        $page_category_detailed[$page_slug][] = array(
            'id'   => $cid,
            'name' => ($term && !is_wp_error($term)) ? $term->name : $cid
        );
    }
}

// 4) 기존 태그 가져오기
$all_tags = get_tags(array('hide_empty' => false));

// 5) 폼 전송 처리
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post']) ) {
    $page = sanitize_text_field($_POST['post_page']);
    if ( empty($allowed_pages[$page]) ) wp_die('권한 없음');

    $post_title   = sanitize_text_field($_POST['post_title']);
    $post_desc    = sanitize_text_field($_POST['post_description']);
    $post_content = wp_kses_post($_POST['post_content']);

    // 선택한 카테고리 (동적 버튼에서 선택한 값)
    $selected_cat = intval($_POST['selected_category']);

    // 태그 선택 처리 (선택된 태그 버튼들과 새로 추가한 태그)
    $selected_tags = isset($_POST['selected_tags']) ? sanitize_text_field($_POST['selected_tags']) : '';
    $post_tags     = $selected_tags;
    
    // 썸네일 업로드
    $thumb_id = 0;
    if ( ! empty($_FILES['thumbnail']['name']) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_id = media_handle_upload('thumbnail', 0);
        if ( ! is_wp_error($attach_id) ) {
            $thumb_id = $attach_id;
        }
    }

    // 글 작성 (카테고리는 선택한 카테고리 값)
    $new_post = array(
        'post_title'   => $post_title,
        'post_content' => $post_content,
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
        'post_category'=> array($selected_cat),
        'tags_input'   => explode(',', $post_tags),
    );
    $post_id = wp_insert_post($new_post);
    if ($post_id) {
        update_post_meta($post_id, 'description', $post_desc);
        if ($thumb_id) set_post_thumbnail($post_id, $thumb_id);

        // 작성 후 리다이렉션
        $redir = home_url("/{$page}/?success=1");
        wp_redirect($redir);
        exit;
    }
}

// TinyMCE 설정
$editor_settings = array(
    'textarea_name' => 'post_content',
    'media_buttons' => true,
    'teeny'         => false,
    'tinymce'       => array(
        'height'  => 300,
        'plugins' => 'lists,link,image,media,code,table,advlist,fullscreen,charmap,hr',
        'toolbar1'=> 'formatselect,bold,italic,underline,alignleft,aligncenter,alignright,bullist,numlist,link,image,media,table,code,undo,redo'
    ),
    'quicktags' => true
);

wp_enqueue_media();
get_header();
?>

<div class="max-w-3xl mx-auto p-6 bg-base-100 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4">페이지 기반 글 작성</h2>
    <form method="post" enctype="multipart/form-data">
        <!-- 1) 제목 (필수) -->
        <label class="block mb-2 font-semibold">제목 <span class="text-red-500">*</span></label>
        <input type="text" name="post_title" required class="w-full p-2 border rounded-md">

        <!-- 2) Description (옵션) -->
        <label class="block mt-4 mb-2 font-semibold">Description</label>
        <textarea name="post_description" class="w-full p-2 border rounded-md" placeholder="글 설명 (선택)"></textarea>

        <!-- 3) 썸네일 업로드 (옵션) -->
        <label class="block mt-4 mb-2 font-semibold">썸네일(대표이미지)</label>
        <input type="file" name="thumbnail" accept="image/*">

        <!-- 4) 게시할 페이지(슬러그) 선택 - 버튼 형식 -->
        <label class="block mt-4 mb-2 font-semibold">게시할 페이지 선택</label>
        <div class="flex space-x-4">
            <?php foreach($allowed_pages as $page_slug => $allowed): ?>
                <?php if($allowed): ?>
                    <button type="button" class="page-select-btn px-4 py-2 border rounded-md" data-page="<?php echo esc_attr($page_slug); ?>">
                        <?php echo ucfirst($page_slug); ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <!-- 선택된 페이지값을 저장할 hidden 필드 -->
        <input type="hidden" name="post_page" id="selected_page" required>

        <!-- 5) 페이지 선택 후, 동적으로 표시되는 카테고리 버튼 -->
        <label class="block mt-4 mb-2 font-semibold">카테고리 선택 (하나만)</label>
        <div id="category-buttons" class="flex flex-wrap gap-2"></div>
        <input type="hidden" name="selected_category" id="selected_category" required>

        <!-- 6) 기존 태그 선택 (버튼 나열) -->
        <label class="block mt-4 mb-2 font-semibold">태그 선택</label>
        <div id="tag-buttons" class="flex flex-wrap gap-2">
            <?php foreach($all_tags as $tag): ?>
                <button type="button" class="tag-btn px-3 py-1 border rounded-md" data-tag="<?php echo esc_attr($tag->name); ?>">
                    <?php echo esc_html($tag->name); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <!-- 선택된 태그들을 저장할 hidden 필드 -->
        <input type="hidden" name="selected_tags" id="selected_tags">

        <!-- 7) 새 태그 추가 (엔터키 입력 시 버튼 생성) -->
        <label class="block mt-4 mb-2 font-semibold">새 태그 추가</label>
        <input type="text" id="new_tag_input" class="w-full p-2 border rounded-md" placeholder="새 태그 입력 후 엔터">

        <!-- 8) 에디터 (글 내용 작성) -->
        <label class="block mt-4 mb-2 font-semibold">내용</label>
        <?php wp_editor('', 'post_content_editor_id', $editor_settings); ?>

        <!-- 9) 버튼들 (입력 완료, 임시저장, 취소) -->
        <div class="flex justify-end gap-3 mt-6">
            <button type="submit" name="submit_post" class="p-3 bg-primary text-white rounded-md">
                입력 완료
            </button>
            <button type="button" id="save-draft" class="p-3 bg-secondary text-white rounded-md">
                임시저장
            </button>
            <a href="<?php echo home_url(); ?>" class="p-3 bg-gray-500 text-white rounded-md">
                취소
            </a>
        </div>
    </form>
</div>

<script>
// 페이지 선택 시 동작 및 카테고리 동적 업데이트
const pageCategoryMap = <?php echo json_encode($page_category_detailed); ?>;

document.querySelectorAll('.page-select-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // 페이지 버튼 active 스타일 처리 (단일 선택)
        document.querySelectorAll('.page-select-btn').forEach(b => b.classList.remove('bg-primary','text-white'));
        this.classList.add('bg-primary','text-white');
        // 선택된 페이지값 저장
        const selectedPage = this.getAttribute('data-page');
        document.getElementById('selected_page').value = selectedPage;
        
        // 동적으로 카테고리 버튼 생성
        const catContainer = document.getElementById('category-buttons');
        catContainer.innerHTML = '';
        if (pageCategoryMap[selectedPage]) {
            pageCategoryMap[selectedPage].forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'cat-btn px-3 py-1 border rounded-md';
                btn.setAttribute('data-cat-id', item.id);
                // 버튼에 이름만 표기하도록 수정
                btn.textContent = item.name;
                btn.addEventListener('click', function() {
                    // 단일 선택: 다른 버튼의 active 스타일 제거
                    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('bg-primary','text-white'));
                    this.classList.add('bg-primary','text-white');
                    document.getElementById('selected_category').value = this.getAttribute('data-cat-id');
                });
                catContainer.appendChild(btn);
            });
        }
    });
});

// 이미 선택된 태그들을 담는 배열
let selectedTags = [];

// 1) 기존 태그 버튼(페이지 로딩 시 생성된 것) 선택/해제 로직
document.querySelectorAll('.tag-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tag = this.getAttribute('data-tag');
        if (this.classList.contains('bg-primary')) {
            // 이미 선택된 상태 -> 선택 해제
            this.classList.remove('bg-primary','text-white');
            selectedTags = selectedTags.filter(t => t !== tag);
        } else {
            // 선택되지 않은 상태 -> 선택
            this.classList.add('bg-primary','text-white');
            selectedTags.push(tag);
        }
        document.getElementById('selected_tags').value = selectedTags.join(',');
    });
});

// 2) 새 태그 추가: 엔터키 입력 시 버튼 생성 & 자동 선택
document.getElementById('new_tag_input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();  // 엔터로 인한 폼 제출 방지

        const tag = this.value.trim();
        if (!tag) return;  // 빈 문자열이면 중단

        // (1) 공백이 있는지 체크
        if (tag.includes(' ')) {
            alert('공백은 허용되지 않습니다.');
            this.value = '';
            return;
        }

        // (2) 이미 선택된 태그인지 확인
        if (selectedTags.includes(tag)) {
            alert('이미 선택된 태그입니다.');
            this.value = '';
            return;
        }

        // (3) 기존 태그 버튼이 있는지 확인 (data-tag 값 비교)
        const existingBtn = document.querySelector(`.tag-btn[data-tag="${tag}"]`);
        if (existingBtn) {
            // 이미 존재하는 태그 버튼이 있을 경우, 그 버튼을 선택 상태로 전환
            if (!existingBtn.classList.contains('bg-primary')) {
                existingBtn.classList.add('bg-primary','text-white');
                selectedTags.push(tag);
                document.getElementById('selected_tags').value = selectedTags.join(',');
            }
            this.value = '';
            return;
        }

        // (4) 여기까지 왔다면 중복 없음 -> 새 태그 버튼 생성
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'tag-btn px-3 py-1 border rounded-md bg-primary text-white';
        btn.setAttribute('data-tag', tag);
        // 새로 생성된 태그임을 표시 (토글 off 시 삭제용)
        btn.setAttribute('data-new', 'true');
        btn.textContent = tag;

        // 새 버튼 클릭 시 토글 처리
        btn.addEventListener('click', function() {
            const t = this.getAttribute('data-tag');
            if (this.classList.contains('bg-primary')) {
                // 현재 선택 상태 -> 선택 해제
                this.classList.remove('bg-primary','text-white');
                selectedTags = selectedTags.filter(item => item !== t);
                document.getElementById('selected_tags').value = selectedTags.join(',');

                // 만약 새로 만든 태그(data-new="true")라면 DOM에서 제거
                if (this.getAttribute('data-new') === 'true') {
                    this.remove();
                }
            } else {
                // 선택되지 않은 상태 -> 선택
                this.classList.add('bg-primary','text-white');
                selectedTags.push(t);
                document.getElementById('selected_tags').value = selectedTags.join(',');
            }
        });

        // 자동 추가 및 active 처리
        selectedTags.push(tag);
        document.getElementById('selected_tags').value = selectedTags.join(',');

        // 태그 버튼 영역에 삽입
        document.getElementById('tag-buttons').appendChild(btn);

        // 입력 필드 초기화
        this.value = '';
    }
});

// 3) 임시저장 버튼 (추후 draft 저장 로직 구현)
document.getElementById('save-draft').addEventListener('click', function() {
    alert('임시저장 기능은 추후 구현 예정입니다.');
});
</script>
<?php get_footer(); ?>