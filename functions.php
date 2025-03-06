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
require_once get_template_directory() . '/plugins/tag-link.php';    // tag link!

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
    // 워드프레스 기본 jQuery 로드
    wp_enqueue_script('jquery');

    // Summernote CSS/JS
    wp_enqueue_style('summernote-css', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css', array(), '0.8.20');
    wp_enqueue_script('summernote-js', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js', array('jquery'), '0.8.20', true);

    // **한글 번역(ko-KR) 추가**
    wp_enqueue_script('summernote-lang-kr', 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/lang/summernote-ko-KR.min.js', array('summernote-js'), '0.8.20', true);
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
    $post_title    = sanitize_text_field($_POST['post_title']);
    $post_desc     = sanitize_text_field($_POST['post_description']);
    $license_value = isset($_POST['license_value']) ? sanitize_text_field($_POST['license_value']) : '';
    $post_content  = wp_kses_post($_POST['post_content']);
    $cat_slug      = isset($_POST['selected_category']) ? sanitize_text_field($_POST['selected_category']) : '';
    $selected_tags = isset($_POST['selected_tags']) ? sanitize_text_field($_POST['selected_tags']) : '';
    $one_liner_value = isset($_POST['one_liner_value']) ? sanitize_text_field($_POST['one_liner_value']) : '';

    // 댓글 상태
    $comment_status = 'open';
    if ( isset($_POST['comment_status']) ) {
        $comment_status = sanitize_text_field($_POST['comment_status']);
    }

    // 4) post_status, post_date
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

    // (D) 예약 옵션
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

    // **[변경] 카테고리 slug → ID 변환**
    $cat_id = 0;
    if ( ! empty($cat_slug) ) {
        $cat_obj = get_category_by_slug($cat_slug);
        if ( $cat_obj && ! is_wp_error($cat_obj) ) {
            $cat_id = (int) $cat_obj->term_id;
        }
    }
    // 만약 $cat_id == 0 이면 에러 처리할 수도
    if ( $cat_id === 0 ) {
        wp_send_json_error('올바른 카테고리 슬러그가 아닙니다: ' . $cat_slug);
    }

    // **[변경] 태그 slug → ID 변환** (이미 존재하면 사용, 없으면 새로 생성)
    $tag_ids = array();
    $tags_array = array_filter(array_map('trim', explode(',', $selected_tags)));
    foreach ( $tags_array as $slug ) {
        if ( ! $slug ) continue;
        $term = get_term_by('slug', $slug, 'post_tag');
        if ( $term && ! is_wp_error($term) ) {
            $tag_ids[] = (int) $term->term_id;
        } else {
            // 새로 생성
            $new_tag = wp_insert_term($slug, 'post_tag', array('slug' => $slug));
            if ( ! is_wp_error($new_tag) ) {
                $tag_ids[] = (int) $new_tag['term_id'];
            }
        }
    }

    // 6) 최종 wp_insert_post
    $new_post = array(
        'post_type'      => $page,
        'post_title'     => $post_title,
        'post_content'   => $post_content,
        'post_author'    => get_current_user_id(),
        'post_category'  => array($cat_id),
        // tags_input에 ID 배열을 넘겨도 되고, tax_input 사용해도 됨
        'tags_input'     => $tag_ids, 
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

        // 한마디글
        if ( ! empty($one_liner_value) ) {
            update_post_meta($post_id, 'one_liner', $one_liner_value);
        }

        // 썸네일
        if ( $thumb_id ) {
            set_post_thumbnail($post_id, $thumb_id);
        }

        // 응답
        $is_scheduled = ($post_status === 'future');
        wp_send_json_success(array(
            'post_id'   => $post_id,
            'scheduled' => $is_scheduled
        ));
    } else {
        wp_send_json_error('글 등록에 실패했습니다.');
    }
}

/* 댓글 */
function my_custom_comment_callback($comment, $args, $depth) {
    // WP 전역 변수 설정
    $GLOBALS['comment'] = $comment;

    // 스타일: 리스트인지 div인지
    $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

    // depth에 따라 들여쓰기나 border-left로 "ㄴ" 구조 표현
    // 예: Tailwind로 left border + padding
    // 예시로 depth 1이면 ml-0, depth 2면 ml-4, depth 3면 ml-8 ...
    // 아래는 단순 예시
    $depth_class = 'ml-' . (4 * ($depth - 1));

    // HTML 래핑 시작
    echo "<{$tag} "; 
    comment_class("my-comment depth-{$depth} {$depth_class} border-l border-gray-300 pl-4 mb-4"); 
    echo ' id="comment-'. get_comment_ID() . '">';

    // (1) 아바타 + 작성자 + 날짜
    echo '<div class="flex items-start gap-3 mb-2">';
        // 아바타
        if ( $args['avatar_size'] != 0 ) {
            echo get_avatar( $comment, $args['avatar_size'], '', '', ['class' => 'rounded-full'] );
        }
        // 작성자명 + 날짜
        echo '<div>';
            echo '<span class="font-semibold">';
            comment_author_link();
            echo '</span>';
            echo '<div class="text-sm text-gray-500">';
            // 날짜/시간
            echo get_comment_date('Y-m-d H:i');
            echo '</div>';
        echo '</div>';
    echo '</div>'; // end flex

    // (2) 승인 대기
    if ( '0' == $comment->comment_approved ) {
        echo '<em class="text-red-500 block mb-2">댓글 승인 대기중...</em>';
    }

    // (3) 댓글 본문
    echo '<div class="mb-2">';
    comment_text();
    echo '</div>';

    // (4) "답글 달기" 버튼
    echo '<div class="text-sm reply">';
    comment_reply_link(array_merge($args, [
        'reply_text' => '답글 달기',
        'depth'      => $depth,
        'max_depth'  => $args['max_depth'],
    ]));
    echo '</div>';

    echo "</{$tag}>";
}

/* 댓글 웨 리다이렉트 되 지 ? */
function mytheme_comment_redirect_to_post($location, $comment) {
    // 댓글 달린 글의 링크 + #comments
    return get_permalink($comment->comment_post_ID) . '#comments';
}
add_filter('comment_post_redirect', 'mytheme_comment_redirect_to_post', 10, 2);

/* 무한스크롤 */
// 이미 추가된 부분이라 가정. 없다면 새로 추가.
// (functions.php 내)

add_action('wp_ajax_my_infinite_scroll_timeline', 'my_infinite_scroll_timeline');
add_action('wp_ajax_nopriv_my_infinite_scroll_timeline', 'my_infinite_scroll_timeline');

function my_infinite_scroll_timeline() {
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    
    // 예시: blog(CPT) + community(CPT)를 가져온다고 가정
    $args = array(
        'post_type'      => array('blog','community'), 
        'posts_per_page' => 5,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();

            // 블로그인지 커뮤니티인지 구분
            $p_type = get_post_type(); // 'blog' or 'community'
            
            // HTML 생성
            echo my_timeline_card_html($p_type);
        }
        $html = ob_get_clean();

        wp_reset_postdata();

        wp_send_json_success(array(
            'html' => $html,
            'max_page' => $query->max_num_pages,
        ));
    } else {
        wp_send_json_error('No more posts');
    }
}

/**
 * post-item(타임라인) + 카드 레이아웃 결합
 */
function my_timeline_card_html($post_type) {
    // 블로그 -> 왼쪽 정렬 + 좌측 여백
    // 커뮤니티 -> 오른쪽 정렬 + 우측 여백
    if ($post_type === 'blog') {
        // md 사이즈 이상에서 가로폭 절반, 왼쪽 padding
        $side_class = 'mr-auto pr-8 lg:pl-20 w-full lg:w-1/2';
    } else {
        // 커뮤니티 -> 오른쪽 padding
        $side_class = 'ml-auto pl-8 lg:pr-20 w-full lg:w-1/2';
    }

    ob_start();
    ?>
    <div class="post-item relative my-4 <?php echo esc_attr($side_class); ?>"
         data-post-type="<?php echo esc_attr($post_type); ?>">

      <!-- 타임라인 점 -->
      <div class="absolute top-8
                  <?php echo ($post_type === 'blog') ? 'right-8 translate-x-1/2' : 'left-8 -translate-x-1/2'; ?>
                  w-4 h-4 rounded-full bg-<?php echo ($post_type === 'blog') ? 'primary' : 'secondary'; ?> z-20">
      </div>

      <!-- 카드 컨테이너 (질문 주신 layout 예시) -->
      <div class="p-2 rounded-lg shadow-md grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-4 cardComponent">
        
        <!-- 왼쪽 텍스트/메타 (order-2 on small, order-1 on large) -->
        <div class="pl-2 order-2 lg:order-1">
          <!-- 제목 + 화살표 아이콘 -->
          <a href="<?php the_permalink(); ?>" 
             class="block font-semibold group hoveronlyText text-xl sm:text-2xl">
            <?php the_title(); ?>
            <svg class="w-8 h-8 sm:w-10 sm:h-10 inline-block transition-all opacity-0 group-hover:opacity-100 
                       translate-x-0 group-hover:translate-x-1 duration-100 fill-current -mt-2"
                 fill="currentColor" viewBox="0 -960 960 960">
              <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
            </svg>
          </a>

          <!-- description 메타 -->
          <div class="ml-2 text-md sm:text-lg">
            <p>
              <?php
              $description = get_post_meta(get_the_ID(), 'description', true);
              if (! empty($description)) {
                  echo esc_html($description);
              }
              ?>
            </p>
          </div>

          <!-- 날짜/수정일 + 카테고리 -->
          <div class="flex flex-row">
            <!-- 날짜 -->
            <div class="flex mt-1 sm:mt-2 items-center text-xs sm:text-sm grayTextThings">
              <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-2">
                <!-- 달력 아이콘 -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" 
                     class="fill-current w-5 h-5 sm:w-6 sm:h-6">
                  <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40
                           q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Z"/>
                </svg>
              </div>
              <span class="mr-2"><?php echo get_the_date('Y-m-d'); ?></span>
              <?php if (get_the_date() != get_the_modified_date()): ?>
                <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-2">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" 
                       class="fill-current w-5 h-5 sm:w-6 sm:h-6">
                    <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40
                             q33 0 56.5 23.5T840-720v200h-80v-40H200v400h280v80H200Zm0-560h560v-80H200v80Zm0 0v-80 80ZM560-80v-123
                             l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263
                             -37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z"/>
                  </svg>
                </div>
                <span class="mr-2"><?php echo get_the_modified_date('Y-m-d'); ?></span>
              <?php endif; ?>
            </div>

            <!-- 카테고리 -->
            <div class="flex mt-1 sm:mt-2 w-fit items-center text-xs sm:text-sm grayTextThings">
              <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                <!-- 카테고리 아이콘 -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" 
                     class="fill-current w-5 h-5 sm:w-6 sm:h-6">
                  <path d="M300-80q-58 0-99-41t-41-99v-520q0-58 41-99t99-41h500v600q-25 0-42.5 17.5T740-220q0 25
                           17.5 42.5T800-160v80H300Z"/>
                </svg>
              </div>
              <div class="btn btn-ghost text-xs sm:text-sm rounded-lg h-7 sm:h-8 w-fit px-1 hoveronlyButton">
                <?php the_category(''); ?>
              </div>
            </div>
          </div>

          <!-- 태그 목록 -->
          <div class="mt-1 sm:mt-2 flex w-fit items-center text-xs sm:text-sm grayTextThings">
            <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" 
                   class="fill-current w-5 h-5 sm:w-6 sm:h-6">
                <path d="m240-160 40-160H120l20-80h160l40-160H180l20-80h160l40-160h80
                         l-40 160h160l40-160h80l-40 160h160l-20 80H660l-40 160h160l-20 80H600l-40 160h-80
                         l40-160H360l-40 160h-80Zm140-240h160l40-160H420l-40 160Z"/>
              </svg>
            </div>
            <div>
              <?php
              $tags = get_the_tags();
              if ($tags) {
                echo '<div class="flex flex-wrap items-center">';
                foreach ($tags as $index => $t) {
                  $tag_link = get_tag_link($t->term_id);
                  if ($index > 0) {
                    echo '<span class="mx-0">/</span>';
                  }
                  ?>
                  <a href="<?php echo esc_url($tag_link); ?>" 
                     class="btn btn-ghost rounded-lg px-1 h-7 sm:h-8 lg:h-9 hoveronlyButton">
                     <?php echo esc_html($t->name); ?>
                  </a>
                  <?php
                }
                echo '</div>';
              } else {
                echo '<span class="grayTextThings ml-1">태그 없음</span>';
              }
              ?>
            </div>
          </div>

          <!-- 글자수 + 읽기시간 -->
          <?php
          $content_raw       = get_the_content(null, false);
          $content_stripped  = wp_strip_all_tags($content_raw);
          $content_no_spaces = preg_replace('/\s+/', '', $content_stripped);
          $char_count        = mb_strlen($content_no_spaces, 'UTF-8');
          $word_count        = str_word_count($content_stripped);
          $reading_time      = max(1, ceil($word_count / 200));
          ?>
          <div class="p-2 mt-1 sm:mt-2 text-md sm:text-lg grayTextThings">
            <?php echo number_format($char_count); ?> 글자&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
            <?php echo $reading_time; ?>분
          </div>
        </div> <!-- /왼쪽 텍스트 -->

        <!-- 썸네일 영역 -->
        <?php if ( has_post_thumbnail() ): ?>
          <div class="-mb-2 lg:mb-0 relative group overflow-hidden rounded order-1 lg:order-2">
            <div class="lgg:w-50 w-full h-full">
              <a href="<?php the_permalink(); ?>" class="block w-full h-full relative">
                <?php the_post_thumbnail('medium', [
                  'class' => 'rounded-lg w-full h-40 lg:h-full object-cover 
                              transition ease-in-out duration-300 group-hover:opacity-40'
                ]); ?>
                <div class="absolute inset-0 flex items-center justify-center opacity-0 
                            group-hover:opacity-100 transition ease-in-out duration-200">
                  <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 -960 960 960">
                    <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                  </svg>
                </div>
              </a>
            </div>
          </div>
        <?php else: ?>
          <!-- 썸네일 없을 때 -->
          <div class="hidden lg:block relative group overflow-hidden rounded order-1 lg:order-2">
            <div class="lg:w-24 lg:h-full">
              <a href="<?php the_permalink(); ?>" 
                 class="btn btn-ghost rounded-lg tagButton w-full lg:h-full lg:flex items-center justify-center text-base-content">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 -960 960 960">
                  <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                </svg>
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div> <!-- /카드 컨테이너 -->
    </div>
    <?php
    return ob_get_clean();
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
