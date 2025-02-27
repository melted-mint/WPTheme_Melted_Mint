<?php
function melted_mint_enqueue_scripts() {
    wp_enqueue_style('melted-mint-style', get_stylesheet_uri());
    wp_enqueue_style('tailwind-css', get_template_directory_uri() . '/assets/css/output.css');
    wp_enqueue_script('startSetting', get_template_directory_uri() . '/assets/js/startSetting.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts', 'melted_mint_enqueue_scripts');
?>

<?php
function melted_mint_setup() {
    register_nav_menus(array(
        'primary'   =>  __('Primary Menu', 'meltedmint'),
        'external'  =>  __('External Menu', 'meltedmint'),
    ));
}
add_action('after_setup_theme', 'melted_mint_setup');
?>

<!-- external -->

<?php
function external_menu_new_tab($atts, $item, $args) {
    if ($args->theme_location == 'external') {
        $atts['target'] = '_blank';
    }
    return $atts;
}
add_filter('nav_menu_link_attributes', 'external_menu_new_tab', 10, 3);
?>

<!-- post pages! -->
<?php
function custom_posting_template($template) {
    if (is_category('post')) { // Blog 카테고리 ID 확인
        $custom_template = locate_template('page-post.php'); // page-blog.php 불러오기
        if ($custom_template) {
            return $custom_template;
        }
    }
    return $template; // 기본 템플릿 유지
}
add_filter('category_template', 'custom_posting_template');
?>

<!-- blogs -->
<?php
function custom_blog_category_template($template) {
    if (is_category('blog')) { // Blog 카테고리 ID 확인
        $custom_template = locate_template('page-blog.php'); // page-blog.php 불러오기
        if ($custom_template) {
            return $custom_template;
        }
    }
    return $template; // 기본 템플릿 유지
}
add_filter('category_template', 'custom_blog_category_template');
?>

<!-- sidebar -->
<?php
function custom_sidebars() {
    register_sidebar(array(
        'name' => 'Left Sidebar',
        'id' => 'left-sidebar',
        'description' => '좌측 Sidebar (Blog 전용)',
        'before_widget' => '<div class="widget p-4 mb-4 bg-base-100 shadow-md rounded-lg">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="text-lg font-bold mb-2">',
        'after_title' => '</h2>',
    ));

    register_sidebar(array(
        'name' => 'Right Sidebar',
        'id' => 'right-sidebar',
        'description' => '우측 Sidebar (Blog 전용)',
        'before_widget' => '<div class="widget p-4 mb-4 bg-base-100 shadow-md rounded-lg">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="text-lg font-bold mb-2">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'custom_sidebars');
?>

<!-- login -->
<?php
function force_logout_fix() {
    if (!is_user_logged_in() && isset($_COOKIE['wordpress_logged_in'])) {
        // 모든 쿠키 삭제
        foreach ($_COOKIE as $cookie => $value) {
            setcookie($cookie, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
            setcookie($cookie, '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN);
        }
    }
}
add_action('init', 'force_logout_fix');
?>

<!-- menu css -->
<?php
function add_menu_link_attributes($atts) {
    // 각 네비게이션 메뉴에 특정 클래스를 추가
    $atts['class'] = 'hoveronlyButton text-md'; // Tailwind 버튼 스타일 적용
    return $atts;
}
add_filter('nav_menu_link_attributes', 'add_menu_link_attributes', 10, 4);
?>

<!-- Ajax Form -->
<?php
/**
 * AJAX 콜백: 프론트엔드 글쓰기
 */
function my_ajax_submit_post_form() {
    // 1) 로그인 여부 확인 (비로그인 시 중단)
    if ( ! is_user_logged_in() ) {
        wp_send_json_error(array(
            'message' => '로그인이 필요합니다.'
        ));
    }

    // 2) 권한 체크 (예: Editor 또는 Admin만 글 작성 가능)
    if ( ! current_user_can('editor') && ! current_user_can('administrator') ) {
        wp_send_json_error(array(
            'message' => '글 작성 권한이 없습니다.'
        ));
    }

    // 3) 입력값 받기
    // (주의: sanitize 필요. 여기서는 예시로만 작성)
    $post_section  = isset($_POST['post_section']) ? sanitize_text_field($_POST['post_section']) : '';
    $post_title    = isset($_POST['post_title'])   ? sanitize_text_field($_POST['post_title'])   : '';
    $post_content  = isset($_POST['post_content']) ? wp_kses_post($_POST['post_content'])         : '';
    $post_tags     = isset($_POST['post_tags'])    ? sanitize_text_field($_POST['post_tags'])     : '';
    $tags_array    = explode(',', $post_tags);

    // 필요하다면 게시판별 권한 체크 로직 추가 가능
    // 예: if ($post_section === 'blog' && ! current_user_can('administrator')) { ... }

    // 4) 카테고리 (체크박스 등으로 받았다면)
    $post_category = array();
    if ( isset($_POST['post_category']) && is_array($_POST['post_category']) ) {
        $post_category = array_map('intval', $_POST['post_category']);
    }

    // 5) 새 글 정보
    $new_post = array(
        'post_title'    => $post_title,
        'post_content'  => $post_content,
        'post_status'   => 'publish', // 바로 게시
        'post_author'   => get_current_user_id(),
        'post_category' => $post_category,
        'tags_input'    => $tags_array,
    );

    // 6) 글 작성
    $post_id = wp_insert_post($new_post);

    if ( is_wp_error($post_id) || ! $post_id ) {
        wp_send_json_error(array(
            'message' => '글 작성 실패'
        ));
    }

    // 7) 파일 업로드 (이미지, 오디오 등)
    // 폼에서 <input type="file" name="featured_image">, <input type="file" name="audio_file"> 등을 받는 경우
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    // 대표 이미지(Featured Image) 업로드 예시
    if ( isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name']) ) {
        $attach_id = media_handle_upload('featured_image', $post_id);
        if ( ! is_wp_error($attach_id) ) {
            // 대표이미지 설정
            set_post_thumbnail($post_id, $attach_id);
        }
    }

    // 오디오 파일 업로드 예시
    if ( isset($_FILES['audio_file']) && !empty($_FILES['audio_file']['name']) ) {
        $audio_id = media_handle_upload('audio_file', $post_id);
        if ( ! is_wp_error($audio_id) ) {
            // 글 메타 등에 저장하거나, 본문에 삽입할 수 있음
            update_post_meta($post_id, '_attached_audio', $audio_id);
        }
    }

    // 8) 성공 응답
    wp_send_json_success(array(
        'message'   => '글이 성공적으로 작성되었습니다.',
        'post_id'   => $post_id,
        'redirect'  => home_url("/{$post_section}/?success=1")
    ));
}

// AJAX 액션 등록
add_action('wp_ajax_submit_post_form', 'my_ajax_submit_post_form');
add_action('wp_ajax_nopriv_submit_post_form', 'my_ajax_submit_post_form');
?>
<!-- media add button -->
<?php
// functions.php

function melted_mint_enqueue_frontend_editor_scripts() {
    // 예: 특정 페이지 템플릿에서만 로드
    if ( is_page_template('template-front-editor.php') ) {
        wp_enqueue_media(); 
        // "Add Media" 버튼에 필요한 JS/CSS (이미지/파일 업로드)
    }
}
add_action('wp_enqueue_scripts', 'melted_mint_enqueue_frontend_editor_scripts');
?>