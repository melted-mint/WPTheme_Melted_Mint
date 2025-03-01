<?php
/*
Template Name: Post
*/

$current_user = wp_get_current_user();
$is_admin  = current_user_can('administrator');
$is_contributor = current_user_can('contributor');

// (1) 비로그인 시 로그인 페이지로 이동
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( home_url('/') ) );
    exit;
}

// (2) CPT(기존 "페이지" 슬러그)별 접근 권한 설정
$allowed_pages = array(
    'blog'      => ($is_admin),
    'novel'     => ($is_admin),
    'spinoff'   => ($is_contributor || $is_admin),
    'community' => true
);

// (3) CPT별로 사용할 "카테고리 ID 세트" (기존 로직 유지)
$page_category_map = array(
    'blog'      => array(8, 9, 10, 11, 12),
    'novel'     => array(38, 18),
    'spinoff'   => array(20),
    'community' => array(9, 10, 11, 12, 34)
);

// 각 카테고리 ID -> (id, name) 형태로 매핑 (UI용)
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

// (4) 모든 태그 가져오기
$all_tags = get_tags(array('hide_empty' => false));

// (5) 폼 전송 처리
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post']) ) {
    // 사용자가 선택한 CPT(기존엔 "page"라 부르던 값)
    $page = sanitize_text_field($_POST['post_page']);
    if ( empty($allowed_pages[$page]) ) {
        wp_die('권한 없음');
    }

    // 입력값
    $post_title   = sanitize_text_field($_POST['post_title']);
    $post_desc    = sanitize_text_field($_POST['post_description']);
    $post_content = wp_kses_post($_POST['post_content']);
    $selected_cat = intval($_POST['selected_category']);  // 선택한 카테고리
    $selected_tags = isset($_POST['selected_tags']) ? sanitize_text_field($_POST['selected_tags']) : '';

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

    // (★ 중요) 'post_type' => $page 로 설정하여, 선택된 CPT에 글을 작성
    // 카테고리/태그는 CPT가 taxonomies=>array('category','post_tag')일 때만 유효
    $new_post = array(
        'post_type'    => $page,  // ← 변경: 'post' 대신 $page
        'post_title'   => $post_title,
        'post_content' => $post_content,
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
        'post_category'=> array($selected_cat), // 카테고리
        'tags_input'   => explode(',', $selected_tags), // 태그
    );

    $post_id = wp_insert_post($new_post);
    if ($post_id) {
        // description 메타
        update_post_meta($post_id, 'description', $post_desc);

        // 썸네일 설정
        if ($thumb_id) {
            set_post_thumbnail($post_id, $thumb_id);
        }

        // 작성 후 리다이렉트 (CPT slug에 맞춰 목록 페이지 등으로 이동)
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
    <h2 class="text-2xl font-bold mb-4">CPT 기반 글 작성</h2>
    <form method="post" enctype="multipart/form-data">
        <!-- 1) 제목 -->
        <label class="block mb-2 font-semibold">제목 <span class="text-red-500">*</span></label>
        <input type="text" name="post_title" required class="w-full p-2 border rounded-md">

        <!-- 2) Description (옵션) -->
        <label class="block mt-4 mb-2 font-semibold">Description</label>
        <textarea name="post_description" class="w-full p-2 border rounded-md" placeholder="글 설명 (선택)"></textarea>

        <!-- 3) 썸네일 업로드 (옵션) -->
        <label class="block mt-4 mb-2 font-semibold">썸네일(대표이미지)</label>
        <input type="file" name="thumbnail" accept="image/*">

        <!-- 4) 게시할 CPT 선택 (기존 page-select-btn 로직 활용) -->
        <label class="block mt-4 mb-2 font-semibold">게시할 유형 선택</label>
        <div class="flex space-x-4">
            <?php foreach($allowed_pages as $page_slug => $allowed): ?>
                <?php if($allowed): ?>
                    <button type="button" class="page-select-btn px-4 py-2 border rounded-md" data-page="<?php echo esc_attr($page_slug); ?>">
                        <?php echo ucfirst($page_slug); ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <!-- 선택된 CPT slug -->
        <input type="hidden" name="post_page" id="selected_page" required>

        <!-- 5) 카테고리 버튼 -->
        <label class="block mt-4 mb-2 font-semibold">카테고리 선택 (하나만)</label>
        <div id="category-buttons" class="flex flex-wrap gap-2"></div>
        <input type="hidden" name="selected_category" id="selected_category" required>

        <!-- 6) 태그 선택 -->
        <label class="block mt-4 mb-2 font-semibold">태그 선택</label>
        <div id="tag-buttons" class="flex flex-wrap gap-2">
            <?php foreach($all_tags as $tag): ?>
                <button type="button" class="tag-btn px-3 py-1 border rounded-md" data-tag="<?php echo esc_attr($tag->name); ?>">
                    <?php echo esc_html($tag->name); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="selected_tags" id="selected_tags">

        <!-- 새 태그 추가 -->
        <label class="block mt-4 mb-2 font-semibold">새 태그 추가</label>
        <input type="text" id="new_tag_input" class="w-full p-2 border rounded-md" placeholder="새 태그 입력 후 엔터">

        <!-- 7) 본문 (TinyMCE) -->
        <label class="block mt-4 mb-2 font-semibold">내용</label>
        <?php wp_editor('', 'post_content_editor_id', $editor_settings); ?>

        <!-- 8) 제출/취소 -->
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
const pageCategoryMap = <?php echo json_encode($page_category_detailed); ?>;

// (A) CPT(기존 "페이지") 선택 로직
document.querySelectorAll('.page-select-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.page-select-btn').forEach(b => b.classList.remove('bg-primary','text-white'));
        this.classList.add('bg-primary','text-white');
        const selectedPage = this.getAttribute('data-page');
        document.getElementById('selected_page').value = selectedPage;
        
        // (B) 카테고리 버튼 업데이트
        const catContainer = document.getElementById('category-buttons');
        catContainer.innerHTML = '';
        if (pageCategoryMap[selectedPage]) {
            pageCategoryMap[selectedPage].forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'cat-btn px-3 py-1 border rounded-md';
                btn.setAttribute('data-cat-id', item.id);
                btn.textContent = item.name;
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('bg-primary','text-white'));
                    this.classList.add('bg-primary','text-white');
                    document.getElementById('selected_category').value = this.getAttribute('data-cat-id');
                });
                catContainer.appendChild(btn);
            });
        }
    });
});

// (C) 태그 선택 로직
let selectedTags = [];

// 1) 기존 태그 버튼 클릭
document.querySelectorAll('.tag-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tag = this.getAttribute('data-tag');
        if (this.classList.contains('bg-primary')) {
            // 이미 선택된 태그 -> 해제
            this.classList.remove('bg-primary','text-white');
            selectedTags = selectedTags.filter(t => t !== tag);
        } else {
            // 새로 선택
            this.classList.add('bg-primary','text-white');
            selectedTags.push(tag);
        }
        document.getElementById('selected_tags').value = selectedTags.join(',');
    });
});

// 2) 새 태그 입력
document.getElementById('new_tag_input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const tag = this.value.trim();
        if (!tag) return;
        if (tag.includes(' ')) {
            alert('공백은 허용되지 않습니다.');
            this.value = '';
            return;
        }
        if (selectedTags.includes(tag)) {
            alert('이미 선택된 태그입니다.');
            this.value = '';
            return;
        }
        // 기존 태그 버튼이 있는지 확인
        const existingBtn = document.querySelector(`.tag-btn[data-tag="${tag}"]`);
        if (existingBtn) {
            if (!existingBtn.classList.contains('bg-primary')) {
                existingBtn.classList.add('bg-primary','text-white');
                selectedTags.push(tag);
                document.getElementById('selected_tags').value = selectedTags.join(',');
            }
            this.value = '';
            return;
        }
        // 새 태그 버튼 생성
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'tag-btn px-3 py-1 border rounded-md bg-primary text-white';
        btn.setAttribute('data-tag', tag);
        btn.setAttribute('data-new', 'true');
        btn.textContent = tag;
        btn.addEventListener('click', function() {
            const t = this.getAttribute('data-tag');
            if (this.classList.contains('bg-primary')) {
                // 선택 해제
                this.classList.remove('bg-primary','text-white');
                selectedTags = selectedTags.filter(item => item !== t);
                document.getElementById('selected_tags').value = selectedTags.join(',');
                // 새로 만든 태그 버튼이면 DOM에서 제거
                if (this.getAttribute('data-new') === 'true') {
                    this.remove();
                }
            } else {
                // 다시 선택
                this.classList.add('bg-primary','text-white');
                selectedTags.push(t);
                document.getElementById('selected_tags').value = selectedTags.join(',');
            }
        });
        // 추가
        selectedTags.push(tag);
        document.getElementById('selected_tags').value = selectedTags.join(',');
        document.getElementById('tag-buttons').appendChild(btn);
        this.value = '';
    }
});

// 임시저장 버튼 (추후 구현)
document.getElementById('save-draft').addEventListener('click', function() {
    alert('임시저장 기능은 추후 구현 예정입니다.');
});
</script>

<?php get_footer(); ?>