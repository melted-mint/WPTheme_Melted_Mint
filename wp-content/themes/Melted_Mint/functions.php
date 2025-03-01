<?php
/**
 * functions.php (정리 버전)
 * 
 * - core.php 불러오기 (커스텀 포스트 타입 등)
 * - class-all-posts-list-table.php (WP_List_Table 클래스)
 * - CSS & JS 로드
 * - 테마 메뉴 등록
 * - external 메뉴 새 탭 처리
 * - "my-posts" 페이지 템플릿 교체
 * - 사이드바 등록
 * - 로그인 쿠키 강제 삭제
 * - 메뉴 링크에 클래스 추가
 * - 특정 페이지 템플릿에서 미디어 버튼
 * - 업로드 허용 파일타입 확장
 * - WP_List_Table 기반 "All Posts" 메뉴
 */

/* 1) plugin 불러오기 */
require_once get_template_directory() . '/plugins/core.php';
require_once get_template_directory() . '/plugins/class-all-posts-list-table.php';
require_once get_template_directory() . '/plugins/link.php';    //custom link!
require_once get_template_directory() . '/plugins/category-link.php';   // category link!

/* 2) CSS & JS 로드 */
function melted_mint_enqueue_scripts() {
    wp_enqueue_style('melted-mint-style', get_stylesheet_uri());
    wp_enqueue_style('tailwind-css', get_template_directory_uri() . '/assets/css/output.css');
    wp_enqueue_script('startSetting', get_template_directory_uri() . '/assets/js/startSetting.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts', 'melted_mint_enqueue_scripts');

/* 3) 테마 메뉴 등록 */
function melted_mint_setup() {
    register_nav_menus(array(
        'primary'   =>  __('Primary Menu', 'meltedmint'),
        'external'  =>  __('External Menu', 'meltedmint'),
    ));
}
add_action('after_setup_theme', 'melted_mint_setup');

/* 4) external 메뉴 -> 새 탭으로 열기 */
function external_menu_new_tab($atts, $item, $args) {
    if ($args->theme_location === 'external') {
        $atts['target'] = '_blank';
    }
    return $atts;
}
add_filter('nav_menu_link_attributes', 'external_menu_new_tab', 10, 3);

/* 5) "my-posts" 페이지 템플릿 교체 (page-my-posts.php) */
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

/* 6) 사이드바 등록 */
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

/* 7) 로그인 쿠키 강제 삭제 */
function force_logout_fix() {
    if ( ! is_user_logged_in() && isset($_COOKIE['wordpress_logged_in']) ) {
        foreach ($_COOKIE as $cookie => $value) {
            setcookie($cookie, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
            setcookie($cookie, '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN);
        }
    }
}
add_action('init', 'force_logout_fix');

/* 8) 메뉴 링크에 Tailwind 클래스 추가 */
function add_menu_link_attributes($atts) {
    $atts['class'] = 'hoveronlyButton text-md'; 
    return $atts;
}
add_filter('nav_menu_link_attributes', 'add_menu_link_attributes', 10, 4);

/* 9) 특정 페이지 템플릿에서 미디어 버튼 로드 */
function melted_mint_enqueue_frontend_editor_scripts() {
    if ( is_page_template('template-front-editor.php') ) {
        wp_enqueue_media(); 
    }
}
add_action('wp_enqueue_scripts', 'melted_mint_enqueue_frontend_editor_scripts');

/* 10) 업로드 허용 파일타입 확장 */
function allow_custom_file_types($mime_types) {
    // 예: PNG 허용, SVG 허용 등
    $mime_types['png'] = 'image/png';
    return $mime_types;
}
add_filter('upload_mimes', 'allow_custom_file_types');

/*-----------------------------------------------------------
 | 11) WP_List_Table 기반 "All Posts Edit" 메뉴
 -----------------------------------------------------------*/
add_action('admin_menu', 'all_posts_wp_list_table_menu');
function all_posts_wp_list_table_menu() {
    add_menu_page(
        'All Posts Edit',           // 브라우저 탭 제목
        'All Posts Edit',           // 메뉴에 표시될 텍스트
        'edit_posts',               // 권한
        'all-posts-edit',           // slug
        'render_all_posts_edit',    // 콜백
        'dashicons-admin-page',     // 아이콘
        4                           // 위치
    );
}

/**
 * 메뉴 콜백
 */
function render_all_posts_edit() {
    // 권한 체크
    if ( ! current_user_can('edit_posts') ) {
        wp_die('You do not have permission to access this page.');
    }

    // Bulk Action 처리
    if ( isset($_REQUEST['_wpnonce']) && ! empty($_REQUEST['_wpnonce']) ) {
        $nonce  = sanitize_text_field($_REQUEST['_wpnonce']);
        if ( wp_verify_nonce($nonce, 'bulk-posts') ) {
            // WP_List_Table 객체 생성 후 Bulk Action 처리
            $table = new All_Posts_List_Table();
            $table->process_bulk_action();
        }
    }

    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">All Posts Edit</h1>';

    // 검색 폼
    echo '<form method="get">';
    // 현재 페이지 slug (all-posts-edit)
    echo '<input type="hidden" name="page" value="all-posts-edit" />';
    // 검색창
    echo '<p class="search-box">';
    echo '<label class="screen-reader-text" for="post-search-input">Search Posts:</label>';
    echo '<input type="search" id="post-search-input" name="s" value="' . esc_attr( isset($_REQUEST['s']) ? $_REQUEST['s'] : '' ) . '"/>';
    echo '<input type="submit" id="search-submit" class="button" value="Search Posts"/>';
    echo '</p>';
    echo '</form>';

    // WP_List_Table 객체 생성
    $table = new All_Posts_List_Table();
    $table->prepare_items();

    echo '<form method="post">';
    // Bulk action 시 필요
    wp_nonce_field('bulk-posts');

    // 테이블 출력
    $table->display();

    echo '</form>';
    echo '</div>';
}
