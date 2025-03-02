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
    wp_enqueue_script('comment-reply', get_template_directory_uri() . '/assets/js/comment-reply.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts', 'melted_mint_enqueue_scripts');

/* jQuery, summernote */
function my_enqueue_jquery_scripts() {
    // 워드프레스 기본 jQuery를 로드
    wp_enqueue_script('jquery');
    // Summernote도 같이 등록 (CDN 예시)
    wp_enqueue_style('summernote-css', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css', array(), '0.8.20');
    wp_enqueue_script('summernote-js', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js', array('jquery'), '0.8.20', true);
}
add_action('wp_enqueue_scripts', 'my_enqueue_jquery_scripts');

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
        'description'   => '좌측 Sidebar',
        'license'       => '좌측 Sidebar',
        'before_widget' => '<div class="widget p-4 mb-4 bg-base-100 shadow-md rounded-lg">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="text-lg font-bold mb-2">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => 'Right Sidebar',
        'id'            => 'right-sidebar',
        'description'   => '우측 Sidebar',
        'license'       => '우측 Sidebar',
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

// 1) Ajax 액션 등록... 9시간......!!!!! 제발 돼줘.......
add_action('wp_ajax_submit_post_ajax', 'handle_submit_post_ajax');
add_action('wp_ajax_nopriv_submit_post_ajax', 'handle_submit_post_ajax'); // 비로그인도 허용 시

function handle_submit_post_ajax() {
    // 1) 로그인 체크
    if ( ! is_user_logged_in() ) {
        wp_send_json_error('로그인이 필요합니다.');
    }

    // 2) CPT 접근 권한
    $allowed_pages = array(
        'blog'      => current_user_can('administrator'),
        'novel'     => current_user_can('administrator'),
        'spinoff'   => ( current_user_can('contributor') || current_user_can('administrator') ),
        'community' => true
    );
    $page = sanitize_text_field($_POST['post_page']);
    if ( empty($allowed_pages[$page]) ) {
        wp_send_json_error('권한 없음');
    }

    // 3) 기본 입력값
    $post_title   = sanitize_text_field($_POST['post_title']);
    $post_desc    = sanitize_text_field($_POST['post_description']);
    $license_value= isset($_POST['license_value']) ? sanitize_text_field($_POST['license_value']) : '';
    $post_content = wp_kses_post($_POST['post_content']);
    $selected_cat = intval($_POST['selected_category']);
    $selected_tags= isset($_POST['selected_tags']) ? sanitize_text_field($_POST['selected_tags']) : '';

    // (A) one_liner (한마디글)
    $one_liner_value = isset($_POST['one_liner_value']) ? sanitize_text_field($_POST['one_liner_value']) : '';

    // 댓글 상태
    $comment_status = 'open';
    if ( isset($_POST['comment_status']) ) {
        $comment_status = sanitize_text_field($_POST['comment_status']);
    }

    // 4) 기본 post_status, post_date
    $post_status = 'publish';
    $post_date   = current_time('mysql'); 
    $post_date_gmt= get_gmt_from_date($post_date);

    // (B) 임시저장?
    if ( isset($_POST['save_draft']) ) {
        $post_status = 'draft';
    }

    // (C) 비밀글?
    if ( ! empty($_POST['private_post']) ) {
        $post_status = 'private';
    }

    // (D) 예약 옵션 체크
    $publish_option = isset($_POST['publish_option']) ? sanitize_text_field($_POST['publish_option']) : 'immediate';
    if ( $publish_option === 'schedule' && ! empty($_POST['scheduled_time']) ) {
        $scheduled_time = sanitize_text_field($_POST['scheduled_time']);
        $ts = strtotime($scheduled_time);
        if ($ts && $ts > time()) {
            $post_status   = 'future';
            $post_date     = date('Y-m-d H:i:s', $ts);
            $post_date_gmt = get_gmt_from_date($post_date);
        } else {
            wp_send_json_error('예약 시간이 올바르지 않거나 현재 시간보다 앞섭니다.');
        }
    }

    // 5) 썸네일 처리
    $thumb_id = 0;
    if ( ! empty($_FILES['thumbnail']['name']) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_id = media_handle_upload('thumbnail', 0);
        if ( ! is_wp_error($attach_id) ) {
            $thumb_id = $attach_id;
        }
    }

    // 6) 최종 wp_insert_post
    $new_post = array(
        'post_type'      => $page,
        'post_title'     => $post_title,
        'post_content'   => $post_content,
        'post_author'    => get_current_user_id(),
        'post_category'  => array($selected_cat),
        'tags_input'     => explode(',', $selected_tags),
        'comment_status' => $comment_status,
        'post_status'    => $post_status,
        'post_date'      => $post_date,
        'post_date_gmt'  => $post_date_gmt
    );

    $post_id = wp_insert_post($new_post);
    if ( $post_id ) {
        // description
        update_post_meta($post_id, 'description', $post_desc);

        // license
        if ( ! empty($license_value) ) {
            update_post_meta($post_id, 'license', $license_value);
        }

        // 한마디글( one_liner_value )
        if ( ! empty($one_liner_value) ) {
            update_post_meta($post_id, 'one_liner', $one_liner_value);
        }

        // 썸네일
        if ( $thumb_id ) {
            set_post_thumbnail($post_id, $thumb_id);
        }

        // 응답
        $is_scheduled = ($post_status === 'future');
        wp_send_json_success( array(
            'post_id'   => $post_id,
            'scheduled' => $is_scheduled
        ));
    } else {
        wp_send_json_error('글 등록에 실패했습니다.');
    }
}

/* 댓글 */
function my_custom_comment_callback($comment, $args, $depth) {
    $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
    // 댓글 하나를 감싸는 li/div에 줄 클래스
    $comment_classes = 'p-4 bg-base-200 rounded-md flex gap-3 my-2';
    ?>
    <<?php echo $tag; ?> <?php comment_class($comment_classes); ?> id="comment-<?php comment_ID(); ?>">

        <!-- (1) 아바타 -->
        <div class="comment-avatar flex-shrink-0">
            <?php
            if ( $args['avatar_size'] != 0 ) {
                echo get_avatar( $comment, $args['avatar_size'] );
            }
            ?>
        </div>

        <!-- (2) 본문 -->
        <div class="flex-1">
            <div class="comment-author font-semibold">
                <?php printf( '<cite class="fn">%s</cite>', get_comment_author_link() ); ?>
            </div>
            <div class="comment-meta text-sm text-gray-500 mb-2">
                <?php
                printf(
                    '<a href="%1$s">%2$s</a>',
                    esc_url( get_comment_link( $comment->comment_ID ) ),
                    sprintf( '%1$s %2$s', get_comment_date('', $comment), get_comment_time() )
                );
                ?>
            </div>
            <?php if ( '0' == $comment->comment_approved ) : ?>
                <em class="text-red-500 block mb-2">댓글 승인 대기중...</em>
            <?php endif; ?>

            <div class="comment-text mb-2">
                <?php comment_text(); ?>
            </div>

            <div class="reply text-sm">
                <?php 
                comment_reply_link(array_merge($args, array(
                    'reply_text' => '답글 달기',
                    'depth'      => $depth,
                    'max_depth'  => $args['max_depth'],
                ))); 
                ?>
            </div>

            <!-- (부모 댓글에 자식(대댓글)이 있다면 "펼치기" 버튼 표시) -->
            <?php
            // WP가 자식 댓글을 자동으로 <ul class="children"> ... </ul> 형태로 렌더링.
            // 자식 댓글이 있으면, 이 li 내부에 <ul class="children">가 추가됨.
            // 아래에서 자식 댓글을 감싸는 ul에 display:none 처리를 위해 class를 추가할 수도 있고,
            // JS에서 .children를 찾는 방식으로 숨길 수도 있습니다.
            ?>
        </div>
    </<?php echo $tag; ?>>
    <?php
    // WP가 자식 댓글(대댓글)을 자동으로 <ul class="children">로 출력하도록
    // 뒤쪽에서 wp_list_comments()가 호출 시, 내부적으로 Walker가
    // 이 callback을 depth별로 호출 + 자식 <ul class="children"> 삽입.
    // 즉, 여기서 굳이 별도 children html을 직접 찍지 않아도 됩니다.
}

/* 댓글 웨 리다이렉트 되 지 ? */
function mytheme_comment_redirect_to_post($location, $comment) {
    // 댓글 달린 글의 링크 + #comments
    return get_permalink($comment->comment_post_ID) . '#comments';
}
add_filter('comment_post_redirect', 'mytheme_comment_redirect_to_post', 10, 2);

/* 무한스크롤 */
// (A) 액션 훅 등록
add_action('wp_ajax_my_infinite_scroll', 'my_infinite_scroll_handler');
add_action('wp_ajax_nopriv_my_infinite_scroll', 'my_infinite_scroll_handler');

/**
 * (B) 무한스크롤용 Ajax 핸들러
 * - post_type: blog, community
 * - paged: $_POST['paged']
 */
function my_infinite_scroll_handler() {
    // paged 파라미터
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    // 쿼리 생성
    $args = array(
        'post_type'      => array('blog','community'),
        'posts_per_page' => 10,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        // 출력 버퍼 시작
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            // 원하는 HTML 구조를 직접 출력
            // 여기서는 간단히 <li>만 예시
            ?>
            <li class="p-4 cardComponent rounded-lg shadow-md">
                <a href="<?php the_permalink(); ?>" class="font-semibold text-xl">
                    <?php the_title(); ?>
                </a>
                <p class="text-sm text-gray-500">
                    <?php echo get_the_date('Y-m-d'); ?>
                </p>
            </li>
            <?php
        }
        wp_reset_postdata();
        $html = ob_get_clean();

        // 남은 페이지 확인
        $max_page = $query->max_num_pages;

        // JSON 응답
        wp_send_json_success(array(
            'html'     => $html,        // 새 글 목록 HTML
            'max_page' => $max_page,    // 최대 페이지
        ));
    } else {
        // 더 이상 글이 없으면
        wp_send_json_error('No more posts');
    }
}

// functions.php 내에 추가

// (A) REST API 엔드포인트 등록
add_action('rest_api_init', function() {
    register_rest_route('timeline/v1', '/posts', [
        'methods'  => 'GET',
        'callback' => 'my_timeline_posts_callback',
    ]);
});

/**
 * (B) 블로그 + 커뮤니티 글을 날짜 역순으로 가져와 JSON 반환
 *     - /wp-json/timeline/v1/posts?page=1
 */
function my_timeline_posts_callback(\WP_REST_Request $request) {
    // 1) 페이지 파라미터
    $page     = $request->get_param('page');
    $paged    = (!empty($page)) ? intval($page) : 1;
    $per_page = 5; // 한 페이지에 몇 개씩

    // 2) WP_Query
    $args = [
        'post_type'      => ['blog','community'], // 두 포스트 타입만
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        'posts_per_page' => $per_page,
    ];
    $query = new WP_Query($args);

    // 3) 결과 배열
    $posts_data = [];
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $p_type    = get_post_type();
            $p_title   = get_the_title();
            $p_date    = get_the_date('Y-m-d'); 
            $p_excerpt = wp_trim_words( get_the_excerpt(), 30 );

            $posts_data[] = [
                'post_type' => $p_type,
                'title'     => $p_title,
                'date'      => $p_date,
                'excerpt'   => $p_excerpt,
            ];
        }
        wp_reset_postdata();
    }

    // 4) maxPages
    $max_pages = $query->max_num_pages;

    // 5) JSON 응답
    return [
        'posts'    => $posts_data,
        'maxPages' => $max_pages,
    ];
}