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

<!-- novels (Edit Nedded) -->
<?php
function custom_novel_category_template($template) {
    if (is_category('novel')) { // Blog 카테고리 ID 확인
        $custom_template = locate_template('page-blog.php'); // page-blog.php 불러오기
        if ($custom_template) {
            return $custom_template;
        }
    }
    return $template; // 기본 템플릿 유지
}
add_filter('category_template', 'custom_novel_category_template');
?>

<!-- about -->
<?php
function custom_about_category_template($template) {
    if (is_category('about')) { // Blog 카테고리 ID 확인
        $custom_template = locate_template('page-about.php'); // page-blog.php 불러오기
        if ($custom_template) {
            return $custom_template;
        }
    }
    return $template; // 기본 템플릿 유지
}
add_filter('category_template', 'custom_about_category_template');
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
 * SunEditor 이미지 업로드 처리
 */
function melted_mint_suneditor_image_upload() {
    // 1) 로그인·권한 체크 (예: Editor 이상만 가능)
    if ( ! is_user_logged_in() ) {
        // JSON 형태로 에러 반환
        wp_send_json_error( array( 'message' => '로그인이 필요합니다.' ) );
    }

    // 2) 실제 업로드할 파일이 있는지 확인
    if ( empty( $_FILES['file'] ) ) {
        wp_send_json_error( array( 'message' => '업로드할 파일이 없습니다.' ) );
    }

    // 3) 워드프레스 업로드 처리
    //   (test_form=false 로 설정하면, $_POST 검증을 건너뜀)
    $upload = wp_handle_upload( $_FILES['file'], array( 'test_form' => false ) );
    if ( isset( $upload['error'] ) ) {
        // 업로드 실패
        wp_send_json_error( array( 'message' => $upload['error'] ) );
    }

    // 4) 업로드 성공 -> SunEditor가 요구하는 형식으로 응답
    // SunEditor 문서(File Upload) 기준, 예:
    // {
    //   "errorMessage": "",
    //   "result": [ { "url": "업로드된파일URL" } ]
    // }
    $uploaded_url = $upload['url'];

    // SunEditor에 반환할 데이터
    $response = array(
        'errorMessage' => '',
        'result'       => array(
            array( 'url' => $uploaded_url )
        )
    );

    // 5) JSON 성공 응답
    wp_send_json_success( $response );
}

// AJAX 액션 등록 (로그인 사용자 + 비로그인 사용자 둘 다 허용 시 nopriv도 추가)
add_action( 'wp_ajax_my_suneditor_image_upload', 'melted_mint_suneditor_image_upload' );
add_action( 'wp_ajax_nopriv_my_suneditor_image_upload', 'melted_mint_suneditor_image_upload' );
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

<!-- media... -->
<?php
function allow_custom_file_types($mime_types) {
    $mime_types['png'] = 'image/png'; // Example for SVG files
    return $mime_types;
}
add_filter('upload_mimes', 'allow_custom_file_types');
?>