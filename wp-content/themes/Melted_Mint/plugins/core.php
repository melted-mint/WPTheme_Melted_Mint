<?php
/**
 * core.php
 *
 * 1) Hides default "Posts" menu (edit.php).
 * 2) Registers 4 CPTs (blog, novel, spinoff, community).
 * 3) "All Posts" menu to display merged CPT.
 * 4) Adds submenus for Category/Tag management under "All Posts".
 * 5) (추가) Blog CPT 퍼머링크를 /%category%/%year%/%monthnum%/%day%/%hour%/%post_id%/ 로 만들기
 *
 * Usage:
 * - Place this file in your theme folder.
 * - In functions.php, add:
 *       require_once get_template_directory() . '/plugins/core.php';
 * - Go to "Settings > Permalinks" and click "Save" once to refresh rewrite rules.
 */

/*-----------------------------------------------------------
 | 1) Hide default "Posts" menu
 -----------------------------------------------------------*/
add_action('admin_menu', 'remove_default_post_type_menu');
function remove_default_post_type_menu() {
    // Hides the default "Posts" menu (edit.php).
    remove_menu_page('edit.php');
}

/*-----------------------------------------------------------
 | 2) Register 4 CPTs
 -----------------------------------------------------------*/
function melted_mint_register_custom_post_types() {

    // --- 1) Blog ---
    register_post_type( 'blog', array(
        'labels' => array(
            'name'                  => 'Blog Posts',
            'singular_name'         => 'Blog Post',
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New Blog Post',
            'edit_item'             => 'Edit Blog Post',
            'new_item'              => 'New Blog Post',
            'view_item'             => 'View Blog Post',
            'view_items'            => 'View Blog Posts',
            'search_items'          => 'Search Blog Posts',
            'not_found'             => 'No Blog Posts found.',
            'not_found_in_trash'    => 'No Blog Posts found in Trash.',
            'all_items'             => 'All Blog Posts',
            'archives'              => 'Blog Post Archives',
            'attributes'            => 'Blog Post Attributes',
            'insert_into_item'      => 'Insert into Blog Post',
            'uploaded_to_this_item' => 'Uploaded to this Blog Post',
            'featured_image'        => 'Featured Image',
            'set_featured_image'    => 'Set featured image',
            'remove_featured_image' => 'Remove featured image',
            'use_featured_image'    => 'Use as featured image',
            'menu_name'             => 'Blog',
        ),
        'public'              => true,
        // ★ Blog는 /%category%/%year%/... 구조로 할 것이므로 WP 기본 rewrite OFF
        'has_archive'         => false,  // 아카이브도 OFF (원한다면 별도 규칙)
        'rewrite'             => false,  // <-- 중요
        'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'menu_icon'           => 'dashicons-edit',
        'menu_position'       => 5,
        'show_in_rest'        => true,
        'publicly_queryable'  => true,
        'exclude_from_search' => false,
        'hierarchical'        => false,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
        'show_in_menu'        => true,
        'taxonomies'          => array('category', 'post_tag'),
    ));

    // --- 2) Novel ---
    register_post_type( 'novel', array(
        'labels' => array(
            'name'                  => 'Novel Posts',
            'singular_name'         => 'Novel Post',
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New Novel Post',
            'edit_item'             => 'Edit Novel Post',
            'new_item'              => 'New Novel Post',
            'view_item'             => 'View Novel Post',
            'view_items'            => 'View Novel Posts',
            'search_items'          => 'Search Novel Posts',
            'not_found'             => 'No Novel Posts found.',
            'not_found_in_trash'    => 'No Novel Posts found in Trash.',
            'all_items'             => 'All Novel Posts',
            'archives'              => 'Novel Post Archives',
            'attributes'            => 'Novel Post Attributes',
            'insert_into_item'      => 'Insert into Novel Post',
            'uploaded_to_this_item' => 'Uploaded to this Novel Post',
            'featured_image'        => 'Featured Image',
            'set_featured_image'    => 'Set featured image',
            'remove_featured_image' => 'Remove featured image',
            'use_featured_image'    => 'Use as featured image',
            'menu_name'             => 'Novel',
        ),
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => array(
            'slug'       => 'novel',
            'with_front' => false,
        ),
        'supports'            => array( 'title', 'editor', 'thumbnail' ),
        'menu_icon'           => 'dashicons-book',
        'menu_position'       => 6,
        'show_in_rest'        => true,
        'publicly_queryable'  => true,
        'exclude_from_search' => false,
        'hierarchical'        => false,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
        'show_in_menu'        => true,
        'taxonomies'          => array('category', 'post_tag'),
    ));

    // --- 3) Spinoff ---
    register_post_type( 'spinoff', array(
        'labels' => array(
            'name'                  => 'Spinoff Posts',
            'singular_name'         => 'Spinoff Post',
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New Spinoff Post',
            'edit_item'             => 'Edit Spinoff Post',
            'new_item'              => 'New Spinoff Post',
            'view_item'             => 'View Spinoff Post',
            'view_items'            => 'View Spinoff Posts',
            'search_items'          => 'Search Spinoff Posts',
            'not_found'             => 'No Spinoff Posts found.',
            'not_found_in_trash'    => 'No Spinoff Posts found in Trash.',
            'all_items'             => 'All Spinoff Posts',
            'archives'              => 'Spinoff Post Archives',
            'attributes'            => 'Spinoff Post Attributes',
            'insert_into_item'      => 'Insert into Spinoff Post',
            'uploaded_to_this_item' => 'Uploaded to this Spinoff Post',
            'featured_image'        => 'Featured Image',
            'set_featured_image'    => 'Set featured image',
            'remove_featured_image' => 'Remove featured image',
            'use_featured_image'    => 'Use as featured image',
            'menu_name'             => 'Spinoff',
        ),
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => array(
            'slug'       => 'spinoff',
            'with_front' => false,
        ),
        'supports'            => array( 'title', 'editor', 'thumbnail' ),
        'menu_icon'           => 'dashicons-randomize',
        'menu_position'       => 7,
        'show_in_rest'        => true,
        'publicly_queryable'  => true,
        'exclude_from_search' => false,
        'hierarchical'        => false,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
        'show_in_menu'        => true,
        'taxonomies'          => array('category', 'post_tag'),
    ));

    // --- 4) Community ---
    register_post_type( 'community', array(
        'labels' => array(
            'name'                  => 'Community Posts',
            'singular_name'         => 'Community Post',
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New Community Post',
            'edit_item'             => 'Edit Community Post',
            'new_item'              => 'New Community Post',
            'view_item'             => 'View Community Post',
            'view_items'            => 'View Community Posts',
            'search_items'          => 'Search Community Posts',
            'not_found'             => 'No Community Posts found.',
            'not_found_in_trash'    => 'No Community Posts found in Trash.',
            'all_items'             => 'All Community Posts',
            'archives'              => 'Community Post Archives',
            'attributes'            => 'Community Post Attributes',
            'insert_into_item'      => 'Insert into Community Post',
            'uploaded_to_this_item' => 'Uploaded to this Community Post',
            'featured_image'        => 'Featured Image',
            'set_featured_image'    => 'Set featured image',
            'remove_featured_image' => 'Remove featured image',
            'use_featured_image'    => 'Use as featured image',
            'menu_name'             => 'Community',
        ),
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => array(
            'slug'       => 'community',
            'with_front' => false,
        ),
        'supports'            => array( 'title', 'editor', 'thumbnail' ),
        'menu_icon'           => 'dashicons-groups',
        'menu_position'       => 8,
        'show_in_rest'        => true,
        'publicly_queryable'  => true,
        'exclude_from_search' => false,
        'hierarchical'        => false,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
        'show_in_menu'        => true,
        'taxonomies'          => array('category', 'post_tag'),
    ));

}
add_action('init', 'melted_mint_register_custom_post_types');


/*-----------------------------------------------------------
 | 3) "All Posts" menu to display merged CPT
 -----------------------------------------------------------*/
add_action('admin_menu', 'add_all_posts_merged_menu');
function add_all_posts_merged_menu() {
    // 메인 메뉴 (All Posts)
    add_menu_page(
        'All Posts Merged',
        'All Posts',
        'edit_posts',
        'all-posts-merged',
        'render_all_posts_merged',
        'dashicons-admin-post',
        5
    );

    // 서브메뉴: 카테고리
    add_submenu_page(
        'all-posts-merged',
        'Manage Categories',
        'Categories',
        'manage_categories',
        'edit-tags.php?taxonomy=category'
    );

    // 서브메뉴: 태그
    add_submenu_page(
        'all-posts-merged',
        'Manage Tags',
        'Tags',
        'manage_categories',
        'edit-tags.php?taxonomy=post_tag'
    );
}

function render_all_posts_merged() {
    // 권한 체크
    if ( ! current_user_can('edit_posts') ) {
        wp_die('You do not have permission to access this page.');
    }

    echo '<div class="wrap"><h1>All Posts (Merged)</h1>';

    // WP_Query: 4개 CPT 통합
    $all_post_types = array('blog','novel','spinoff','community');
    $args = array(
        'post_type'      => $all_post_types,
        'posts_per_page' => 50,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    $merged_query = new WP_Query($args);

    if ( $merged_query->have_posts() ) {
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>Title</th><th>Type</th><th>Date</th><th>Actions</th></tr></thead>';
        echo '<tbody>';

        while ( $merged_query->have_posts() ) {
            $merged_query->the_post();

            $post_id   = get_the_ID();
            $post_type = get_post_type();
            $title     = get_the_title();
            $date      = get_the_date('Y-m-d');

            // 수정/삭제 링크
            $edit_link   = get_edit_post_link($post_id);
            $delete_link = get_delete_post_link($post_id);

            echo '<tr>';
            echo '<td><strong>' . esc_html($title) . '</strong></td>';
            echo '<td>' . esc_html($post_type) . '</td>';
            echo '<td>' . esc_html($date) . '</td>';
            echo '<td>';
            if ($edit_link) {
                echo '<a href="' . esc_url($edit_link) . '">Edit</a> | ';
            }
            if ($delete_link) {
                echo '<a href="' . esc_url($delete_link) . '" style="color:red;">Delete</a>';
            }
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        wp_reset_postdata();
    } else {
        echo '<p>No posts found.</p>';
    }

    echo '</div>';
}
