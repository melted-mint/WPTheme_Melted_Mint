<?php
/*
Template Name: Post
*/

// 1) 로그인/권한 체크
$current_user = wp_get_current_user();
$is_admin  = current_user_can('administrator');
$is_editor = current_user_can('editor');
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( home_url('/') ) );
    exit;
}

// 2) 주 카테고리 권한
$allowed_cats = array(
    'blog'      => ($is_admin),
    'novel'     => ($is_admin),
    'spinoff'   => ($is_editor || $is_admin),
    'community' => true
);

// 3) 주 카테고리 -> 실제 ID
$cat_map = array(
    'blog'      => 8,
    'novel'     => 17,
    'spinoff'   => 20,
    'community' => 22
);

// 4) “게시판별” 추가 카테고리 목록 매핑 (예시)
$cat_for_section = array(
    'blog' => array(8, 9),       // blog 섹션에서 허용할 카테고리 ID들 (예: 8,9)
    'novel' => array(17, 18),
    'spinoff' => array(20, 21),
    'community' => array(22, 23)
);

// 5) 모든 태그
$all_tags = get_tags(array('hide_empty'=>false));

// 6) 폼 전송 처리
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post']) ) {
    $section = sanitize_text_field($_POST['post_section']);
    if ( empty($allowed_cats[$section]) ) wp_die('권한 없음');

    $post_title   = sanitize_text_field($_POST['post_title']);
    $post_content = wp_kses_post($_POST['post_content']);
    $post_desc    = sanitize_text_field($_POST['post_description']);
    $post_tags    = sanitize_text_field($_POST['post_tags']);
    $primary_cat_id = isset($cat_map[$section]) ? intval($cat_map[$section]) : 0;

    // 새 태그 추가
    $new_tag = sanitize_text_field($_POST['new_tag']);
    if ($new_tag) {
        $post_tags .= ($post_tags ? ',' : '') . $new_tag;
    }

    // 추가 카테고리 (체크박스들)
    $additional_cats = array();
    if ( isset($_POST['additional_cats']) && is_array($_POST['additional_cats']) ) {
        $additional_cats = array_map('intval', $_POST['additional_cats']);
    }

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

    // 최종 카테고리 (주 + 추가)
    $final_cats = array_merge( array($primary_cat_id), $additional_cats );
    $final_cats = array_unique($final_cats);

    // 글 작성
    $new_post = array(
        'post_title'   => $post_title,
        'post_content' => $post_content,
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
        'post_category'=> $final_cats,
        'tags_input'   => explode(',', $post_tags),
    );
    $post_id = wp_insert_post($new_post);
    if ($post_id) {
        update_post_meta($post_id, 'description', $post_desc);
        if ($thumb_id) set_post_thumbnail($post_id, $thumb_id);

        $redir = home_url("/{$section}/?success=1");
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
        'height' => 300,
        'plugins' => 'lists,link,image,media,code,table,advlist,fullscreen,charmap,hr',
        'toolbar1'=> 'formatselect,bold,italic,underline,alignleft,aligncenter,alignright,bullist,numlist,link,image,media,table,code,undo,redo'
    ),
    'quicktags' => true
);

// 미디어 업로드
wp_enqueue_media();
get_header();
?>

<div class="max-w-3xl mx-auto p-6 bg-base-100 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4">Page-based Posting</h2>

    <form method="post" enctype="multipart/form-data">

        <!-- 1) 주 카테고리(게시판) 선택 (라디오) -->
        <label class="block mb-2 font-semibold">게시판(주 카테고리) 선택</label>
        <?php foreach($allowed_cats as $sect=>$allowed): ?>
          <?php if($allowed): ?>
            <label class="inline-flex items-center mr-4">
              <input type="radio" name="post_section" value="<?php echo esc_attr($sect); ?>" class="mr-1"> 
              <?php echo ucfirst($sect); ?>
            </label>
          <?php endif; ?>
        <?php endforeach; ?>

        <!-- 2) 제목 -->
        <label class="block mt-4 mb-2 font-semibold">제목</label>
        <input type="text" name="post_title" required class="w-full p-2 border rounded-md">

        <!-- 3) 내용 (TinyMCE) -->
        <label class="block mt-4 mb-2 font-semibold">내용</label>
        <?php wp_editor('', 'post_content_editor_id', $editor_settings); ?>

        <!-- 4) Description -->
        <label class="block mt-4 mb-2 font-semibold">Description</label>
        <textarea name="post_description" class="w-full p-2 border rounded-md"></textarea>

        <!-- 5) 기존 태그 다중 선택 -->
        <label class="block mt-4 mb-2 font-semibold">기존 태그 선택</label>
        <select name="post_tags" multiple class="w-full p-2 border rounded-md">
          <?php foreach($all_tags as $tag): ?>
            <option value="<?php echo esc_attr($tag->name); ?>">
              <?php echo esc_html($tag->name); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- 5-1) 새 태그 추가 -->
        <label class="block mt-4 mb-2 font-semibold">새 태그 추가</label>
        <input type="text" name="new_tag" class="w-full p-2 border rounded-md" placeholder="새 태그 입력">

        <!-- 6) 추가 카테고리(체크박스들), 섹션별로 구분 -->
        <label class="block mt-4 mb-2 font-semibold">추가 카테고리</label>
        <?php foreach ($cat_for_section as $sect_key => $cat_ids): ?>
            <!-- 각 섹션마다 div -->
            <div id="cat_section_<?php echo esc_attr($sect_key); ?>" class="cat-section hidden ml-2">
                <p class="text-sm mb-1">[<?php echo ucfirst($sect_key); ?> 전용 카테고리]</p>
                <?php foreach($cat_ids as $cid): 
                    $term = get_category($cid);
                    if ($term && ! is_wp_error($term)): ?>
                    <label class="inline-flex items-center mr-2">
                        <input type="checkbox" name="additional_cats[]" value="<?php echo esc_attr($term->term_id); ?>">
                        <span class="ml-1"><?php echo esc_html($term->name); ?></span>
                    </label>
                <?php endif; endforeach; ?>
            </div>
        <?php endforeach; ?>

        <!-- 7) 썸네일 업로드 -->
        <label class="block mt-4 mb-2 font-semibold">썸네일(대표이미지)</label>
        <input type="file" name="thumbnail" accept="image/*">

        <!-- 8) 제출 버튼 -->
        <button type="submit" name="submit_post" class="mt-4 p-3 bg-primary text-white rounded-md">
            작성 완료
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 라디오 버튼 변경 시 cat-section 표시/숨김
    let radioButtons = document.querySelectorAll('input[name="post_section"]');
    let catSections = document.querySelectorAll('.cat-section');

    function updateCatSection() {
        // 모든 cat-section 숨김
        catSections.forEach(div => div.classList.add('hidden'));

        // 현재 선택된 라디오
        let selected = document.querySelector('input[name="post_section"]:checked');
        if (selected) {
            let sect = selected.value;
            let targetDiv = document.getElementById('cat_section_' + sect);
            if (targetDiv) {
                targetDiv.classList.remove('hidden');
            }
        }
    }

    radioButtons.forEach(radio => {
        radio.addEventListener('change', updateCatSection);
    });

    // 초기 실행
    updateCatSection();
});
</script>

<?php get_footer(); ?>