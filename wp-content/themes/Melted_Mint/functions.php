<?php
/**
 * functions.php (정리 버전)
 * 
 * - theme_plugins.php 불러오기 (커스텀 포스트 타입 등)
 * - CSS & JS 로드
 * - 테마 메뉴 등록
 * - external 메뉴 새 탭 처리
 * - "my-posts" 페이지 템플릿 교체
 * - 사이드바 등록
 * - 로그인 쿠키 강제 삭제
 * - 메뉴 링크에 클래스 추가
 * - 특정 페이지 템플릿에서 미디어 버튼
 * - 업로드 허용 파일타입 확장
 */

/* theme_plugins.php 불러오기 */
require_once get_template_directory() . '/theme_plugins.php';

/* CSS & JS 로드 */
function melted_mint_enqueue_scripts() {
    wp_enqueue_style('melted-mint-style', get_stylesheet_uri());
    wp_enqueue_style('tailwind-css', get_template_directory_uri() . '/assets/css/output.css');
    wp_enqueue_script('startSetting', get_template_directory_uri() . '/assets/js/startSetting.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts', 'melted_mint_enqueue_scripts');

/* 테마 메뉴 등록 */
function melted_mint_setup() {
    register_nav_menus(array(
        'primary'   =>  __('Primary Menu', 'meltedmint'),
        'external'  =>  __('External Menu', 'meltedmint'),
    ));
}
add_action('after_setup_theme', 'melted_mint_setup');

/* external 메뉴 -> 새 탭으로 열기 */
function external_menu_new_tab($atts, $item, $args) {
    if ($args->theme_location === 'external') {
        $atts['target'] = '_blank';
    }
    return $atts;
}
add_filter('nav_menu_link_attributes', 'external_menu_new_tab', 10, 3);

/* "my-posts" 페이지 템플릿 교체 (page-my-posts.php) */
function custom_view_my_posts_template($template) {
    if ( is_page('my-posts') ) {
        $custom_template = locate_template('page-my-posts.php');
        if ( $custom_template ) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('page_template', 'custom_view_my_posts_template');

/* 사이드바 등록 */
function custom_sidebars() {
    register_sidebar(array(
        'name'          => 'Left Sidebar',
        'id'            => 'left-sidebar',
        'description'   => '좌측 Sidebar (Blog 전용)',
        'before_widget' => '<div class="widget p-4 mb-4 bg-base-100 shadow-md rounded-lg">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="text-lg font-bold mb-2">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => 'Right Sidebar',
        'id'            => 'right-sidebar',
        'description'   => '우측 Sidebar (Blog 전용)',
        'before_widget' => '<div class="widget p-4 mb-4 bg-base-100 shadow-md rounded-lg">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="text-lg font-bold mb-2">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'custom_sidebars');

/* 로그인 쿠키 강제 삭제 */
function force_logout_fix() {
    if ( ! is_user_logged_in() && isset($_COOKIE['wordpress_logged_in']) ) {
        foreach ($_COOKIE as $cookie => $value) {
            setcookie($cookie, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
            setcookie($cookie, '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN);
        }
    }
}
add_action('init', 'force_logout_fix');

/* 메뉴 링크에 Tailwind 클래스 추가 */
function add_menu_link_attributes($atts) {
    $atts['class'] = 'hoveronlyButton text-md'; 
    return $atts;
}
add_filter('nav_menu_link_attributes', 'add_menu_link_attributes', 10, 4);

/* 특정 페이지 템플릿에서 미디어 버튼 로드 */
function melted_mint_enqueue_frontend_editor_scripts() {
    if ( is_page_template('template-front-editor.php') ) {
        wp_enqueue_media(); 
    }
}
add_action('wp_enqueue_scripts', 'melted_mint_enqueue_frontend_editor_scripts');

/* 업로드 허용 파일타입 확장 */
function allow_custom_file_types($mime_types) {
    // 예: PNG 허용, SVG 허용 등
    $mime_types['png'] = 'image/png';
    return $mime_types;
}
add_filter('upload_mimes', 'allow_custom_file_types');