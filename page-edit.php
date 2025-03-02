<?php
/*
Template Name: Edit
*/

////////////////////////////////////////////////////////////////////////////////
// 1) 로그인/권한 체크
////////////////////////////////////////////////////////////////////////////////
$current_user = wp_get_current_user();
$is_admin       = current_user_can('administrator');
$is_editor      = current_user_can('editor');
$is_contributor = current_user_can('contributor');

if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( home_url('/') ) );
    exit;
}

// 수정할 글 ID 받기 (예: ?post_id=123)
$post_id = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;
if ( ! $post_id ) {
    wp_die('잘못된 접근입니다. (post_id 없음)');
}

// 글 객체 가져오기
$post = get_post($post_id);
if ( ! $post ) {
    wp_die('존재하지 않는 글입니다.');
}

// 권한 체크 (예: 작성자 본인이거나, 어드민/에디터만 수정 가능)
if ( $post->post_author != get_current_user_id() && ! $is_admin && ! $is_editor ) {
    wp_die('이 글을 수정할 권한이 없습니다.');
}

////////////////////////////////////////////////////////////////////////////////
// 2) "페이지(슬러그)" -> "카테고리 ID" 매핑 (동적 버튼)
////////////////////////////////////////////////////////////////////////////////
$allowed_pages = array(
    'blog'      => ($is_admin),
    'novel'     => ($is_admin),
    'spinoff'   => ($is_contributor || $is_admin),
    'community' => true
);

$page_category_map = array(
    'blog'      => array(8, 9, 10, 11, 12),
    'novel'     => array(38, 18),
    'spinoff'   => array(20),
    'community' => array(9, 10, 11, 12, 34)
);

// 카테고리 정보 로드해서 버튼 표시용 구조로 만들기
$page_category_detailed = array();
foreach ($page_category_map as $page_slug => $cat_ids) {
    $page_category_detailed[$page_slug] = array();
    foreach ($cat_ids as $cid) {
        $term = get_category($cid);
        $page_category_detailed[$page_slug][] = array(
            'id'   => $cid,
            'name' => ($term && !is_wp_error($term)) ? $term->name : "cat-$cid"
        );
    }
}

////////////////////////////////////////////////////////////////////////////////
// 3) 기존 글 정보 로드: 제목, 내용, 썸네일, 태그, 카테고리, 메타(description 등)
////////////////////////////////////////////////////////////////////////////////

// 제목, 내용
$old_title   = $post->post_title;
$old_content = $post->post_content;

// description (예: 커스텀 메타)
$old_desc = get_post_meta($post_id, 'description', true);

// 썸네일
$old_thumb_id = get_post_thumbnail_id($post_id);

// 기존 카테고리들
$old_cats = wp_get_post_categories($post_id); // 배열
// 하나만 선택한다고 가정할 때, 첫 번째 카테고리만 쓰거나, 
// 혹은 사이트 구조에 맞게 처리하세요.
$selected_cat = !empty($old_cats) ? $old_cats[0] : 0;

// 기존 태그들
$old_tags = wp_get_post_tags($post_id, array('fields' => 'names')); // ['tag1','tag2'...]
$old_tags_csv = implode(',', $old_tags); // 'tag1,tag2,...'

// "어느 페이지(슬러그)에 속하는지" 역추적
//   -> $selected_cat가 page_category_map 중 어디에 들어있는지 찾아보기
function get_page_slug_by_category($cat_id, $page_map) {
    foreach($page_map as $slug => $cat_ids) {
        if (in_array($cat_id, $cat_ids)) {
            return $slug;
        }
    }
    return ''; // 찾지 못한 경우
}
$old_page_slug = get_page_slug_by_category($selected_cat, $page_category_map);

////////////////////////////////////////////////////////////////////////////////
// 4) 폼 전송 처리
////////////////////////////////////////////////////////////////////////////////
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_edit']) ) {
    // 4-1) 권한 체크
    $page = sanitize_text_field($_POST['post_page']);
    if ( empty($allowed_pages[$page]) ) wp_die('권한 없음 (page)');

    // 4-2) 입력값 처리
    $new_title   = sanitize_text_field($_POST['post_title']);
    $new_desc    = sanitize_text_field($_POST['post_description']);
    $new_content = wp_kses_post($_POST['post_content']);

    // 선택한 카테고리
    $new_cat_id = intval($_POST['selected_category']);

    // 태그
    $selected_tags = isset($_POST['selected_tags']) ? sanitize_text_field($_POST['selected_tags']) : '';
    $new_tags_array = explode(',', $selected_tags);

    // 썸네일 업로드 (옵션)
    $thumb_id = $old_thumb_id; // 기본은 기존 썸네일 유지
    if ( ! empty($_FILES['thumbnail']['name']) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_id = media_handle_upload('thumbnail', $post_id);
        if ( ! is_wp_error($attach_id) ) {
            $thumb_id = $attach_id;
        }
    }

    // 4-3) 글 업데이트
    $updated_post = array(
        'ID'           => $post_id,
        'post_title'   => $new_title,
        'post_content' => $new_content,
        'post_category'=> array($new_cat_id),  // 새 카테고리 적용
        'tags_input'   => $new_tags_array,     // 새 태그들
    );
    $result = wp_update_post($updated_post);
    if ( $result && ! is_wp_error($result) ) {
        // description 메타 갱신
        update_post_meta($post_id, 'description', $new_desc);
        // 썸네일 설정
        if ($thumb_id) set_post_thumbnail($post_id, $thumb_id);

        // 완료 후 리다이렉트 (수정된 페이지 slug 기준)
        $redir = home_url("/{$page}/?edit_success=1");
        wp_redirect($redir);
        exit;
    }
    else {
        // 오류 발생 시 처리
        wp_die('글 수정에 실패했습니다.');
    }
}

// TinyMCE 설정 (에디터)
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

// 미디어 업로드 스크립트
wp_enqueue_media();
get_header();
?>

<div class="max-w-3xl mx-auto p-6 bg-base-100 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4">글 수정 (Edit Post)</h2>
    <form method="post" enctype="multipart/form-data">
        <!-- 1) 제목 -->
        <label class="block mb-2 font-semibold">제목 <span class="text-red-500">*</span></label>
        <input type="text" name="post_title" required class="w-full p-2 border rounded-md"
               value="<?php echo esc_attr($old_title); ?>">

        <!-- 2) Description (옵션) -->
        <label class="block mt-4 mb-2 font-semibold">Description</label>
        <textarea name="post_description" class="w-full p-2 border rounded-md" placeholder="글 설명 (선택)"><?php 
            echo esc_textarea($old_desc); 
        ?></textarea>

        <!-- 3) 썸네일 업로드 (옵션) -->
        <label class="block mt-4 mb-2 font-semibold">썸네일(대표이미지)</label>
        <?php if ($old_thumb_id): ?>
            <div class="mb-2">
                <?php echo wp_get_attachment_image($old_thumb_id, 'thumbnail', false, array('class'=>'mb-2')); ?>
            </div>
        <?php endif; ?>
        <input type="file" name="thumbnail" accept="image/*">

        <!-- 4) 게시할 페이지(슬러그) 선택 - 버튼 형식 -->
        <label class="block mt-4 mb-2 font-semibold">게시할 페이지 선택</label>
        <div class="flex space-x-4">
            <?php foreach($allowed_pages as $page_slug => $allowed): ?>
                <?php if($allowed): 
                    // 현재 글이 해당 page_slug에 해당하면 active 처리
                    $active_class = ($old_page_slug === $page_slug) ? 'bg-primary text-white' : '';
                ?>
                    <button type="button" 
                            class="page-select-btn px-4 py-2 border rounded-md <?php echo $active_class; ?>"
                            data-page="<?php echo esc_attr($page_slug); ?>">
                        <?php echo ucfirst($page_slug); ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <!-- 선택된 페이지값을 저장할 hidden 필드 -->
        <input type="hidden" name="post_page" id="selected_page" 
               value="<?php echo esc_attr($old_page_slug); ?>" required>

        <!-- 5) 페이지 선택 후, 동적으로 표시되는 카테고리 버튼 -->
        <label class="block mt-4 mb-2 font-semibold">카테고리 선택 (하나만)</label>
        <div id="category-buttons" class="flex flex-wrap gap-2">
            <!-- 자바스크립트에서 동적으로 버튼을 생성하지만,
                 "수정" 시에는 기존 page_slug에 해당하는 카테고리 버튼도 미리 생성해둡니다. -->
            <?php 
            if ($old_page_slug && !empty($page_category_detailed[$old_page_slug])) {
                foreach($page_category_detailed[$old_page_slug] as $item) {
                    $cat_id = $item['id'];
                    $cat_name = $item['name'];
                    $cat_active_class = ($selected_cat == $cat_id) ? 'bg-primary text-white' : '';
                    ?>
                    <button type="button" 
                            class="cat-btn px-3 py-1 border rounded-md <?php echo $cat_active_class; ?>"
                            data-cat-id="<?php echo esc_attr($cat_id); ?>">
                        <?php echo esc_html($cat_name); ?>
                    </button>
                    <?php
                }
            }
            ?>
        </div>
        <input type="hidden" name="selected_category" id="selected_category"
               value="<?php echo intval($selected_cat); ?>" required>

        <!-- 6) 기존 태그 선택 (버튼 나열) + 7) 새 태그 추가 -->
        <label class="block mt-4 mb-2 font-semibold">태그 선택</label>
        <div id="tag-buttons" class="flex flex-wrap gap-2">
            <?php 
            // all_tags 전체 목록 중, 이미 선택된 것은 active 처리
            $all_tags = get_tags(array('hide_empty' => false));
            $selected_tags_array = explode(',', $old_tags_csv); // 구: $old_tags
            ?>
            <?php foreach($all_tags as $tag): 
                $tag_name = $tag->name;
                $active_class = in_array($tag_name, $selected_tags_array, true) ? 'bg-primary text-white' : '';
            ?>
                <button type="button" 
                        class="tag-btn px-3 py-1 border rounded-md <?php echo $active_class; ?>"
                        data-tag="<?php echo esc_attr($tag_name); ?>">
                    <?php echo esc_html($tag_name); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <!-- 선택된 태그들을 저장할 hidden 필드 -->
        <input type="hidden" name="selected_tags" id="selected_tags"
               value="<?php echo esc_attr($old_tags_csv); ?>">

        <!-- 새 태그 추가 (엔터키 입력 시 버튼 생성) -->
        <label class="block mt-4 mb-2 font-semibold">새 태그 추가</label>
        <input type="text" id="new_tag_input" class="w-full p-2 border rounded-md" placeholder="새 태그 입력 후 엔터">

        <!-- 8) 에디터 (글 내용 작성) -->
        <label class="block mt-4 mb-2 font-semibold">내용</label>
        <?php 
        // $old_content를 기본값으로 전달
        wp_editor($old_content, 'post_content_editor_id', $editor_settings); 
        ?>

        <!-- 9) 버튼들 (수정 완료, 임시저장, 취소) -->
        <div class="flex justify-end gap-3 mt-6">
            <button type="submit" name="submit_edit" class="p-3 bg-primary text-white rounded-md">
                수정 완료
            </button>
            <button type="button" id="save-draft" class="p-3 bg-secondary text-white rounded-md">
                임시저장
            </button>
            <a href="<?php echo get_permalink($post_id); ?>" class="p-3 bg-gray-500 text-white rounded-md">
                취소
            </a>
        </div>
    </form>
</div>

<script>
/**
 * 1) 페이지 선택 버튼 클릭 -> page_slug 선택
 * 2) 해당 페이지에 대응하는 카테고리 버튼 동적 생성
 * 3) 기존 cat-btn active 상태 해제 & 새로 생성된 버튼들 중 선택된 카테고리가 있으면 active
 * 4) 태그 버튼 선택/해제, 새 태그 추가 로직은 새 글 작성 폼과 동일
 */

const pageCategoryMap = <?php echo json_encode($page_category_detailed); ?>;
let selectedTags = [];

// 초기화: 기존 selected_tags 값을 배열로 변환
{
    const init_tags = document.getElementById('selected_tags').value;
    if (init_tags) {
        selectedTags = init_tags.split(',');
    }
}

// 페이지 선택 버튼 로직
document.querySelectorAll('.page-select-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // 페이지 버튼 active 스타일 처리 (단일 선택)
        document.querySelectorAll('.page-select-btn').forEach(b => b.classList.remove('bg-primary','text-white'));
        this.classList.add('bg-primary','text-white');
        // 선택된 페이지값
        const selectedPage = this.getAttribute('data-page');
        document.getElementById('selected_page').value = selectedPage;

        // 카테고리 버튼 재생성
        const catContainer = document.getElementById('category-buttons');
        catContainer.innerHTML = '';

        if (pageCategoryMap[selectedPage]) {
            pageCategoryMap[selectedPage].forEach(item => {
                const catBtn = document.createElement('button');
                catBtn.type = 'button';
                catBtn.className = 'cat-btn px-3 py-1 border rounded-md';
                catBtn.setAttribute('data-cat-id', item.id);
                catBtn.textContent = item.name;
                catBtn.addEventListener('click', function() {
                    // 단일 선택
                    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('bg-primary','text-white'));
                    this.classList.add('bg-primary','text-white');
                    document.getElementById('selected_category').value = this.getAttribute('data-cat-id');
                });
                catContainer.appendChild(catBtn);
            });
        }
        // 카테고리 hidden 값 초기화
        document.getElementById('selected_category').value = '';
    });
});

// 카테고리 버튼 클릭(이미 출력된 것 포함) - 이벤트 위임 or 각 버튼마다 설정
document.querySelectorAll('.cat-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('bg-primary','text-white'));
        this.classList.add('bg-primary','text-white');
        document.getElementById('selected_category').value = this.getAttribute('data-cat-id');
    });
});

// 태그 버튼 클릭 (기존 태그)
document.querySelectorAll('.tag-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tag = this.getAttribute('data-tag');
        if (this.classList.contains('bg-primary')) {
            // 해제
            this.classList.remove('bg-primary','text-white');
            selectedTags = selectedTags.filter(t => t !== tag);
        } else {
            // 선택
            this.classList.add('bg-primary','text-white');
            selectedTags.push(tag);
        }
        document.getElementById('selected_tags').value = selectedTags.join(',');
    });
});

// 새 태그 추가
document.getElementById('new_tag_input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const tag = this.value.trim();
        if (!tag) return;

        // 중복 여부 체크
        if (selectedTags.includes(tag)) {
            alert('이미 선택된 태그입니다.');
            this.value = '';
            return;
        }
        // 기존 태그 버튼 중복 여부
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

        // 새 버튼 생성
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'tag-btn px-3 py-1 border rounded-md bg-primary text-white';
        btn.setAttribute('data-tag', tag);
        btn.textContent = tag;

        // 클릭 이벤트 (토글)
        btn.addEventListener('click', function() {
            const t = this.getAttribute('data-tag');
            if (this.classList.contains('bg-primary')) {
                // 해제
                this.classList.remove('bg-primary','text-white');
                selectedTags = selectedTags.filter(x => x !== t);
                // 새 태그 삭제(필요하면 유지) => 여기서는 유지한다고 가정
                // this.remove(); // "선택 해제 시 삭제"를 원하면 주석 해제
            } else {
                // 선택
                this.classList.add('bg-primary','text-white');
                selectedTags.push(t);
            }
            document.getElementById('selected_tags').value = selectedTags.join(',');
        });

        selectedTags.push(tag);
        document.getElementById('selected_tags').value = selectedTags.join(',');

        document.getElementById('tag-buttons').appendChild(btn);
        this.value = '';
    }
});

// 임시저장 버튼
document.getElementById('save-draft').addEventListener('click', function() {
    alert('임시저장 기능은 추후 구현 예정입니다.');
});
</script>

<?php get_footer(); ?>