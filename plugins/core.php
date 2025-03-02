<?php
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
        'has_archive'         => false,  // ★
        'rewrite'             => false,  // ★
        'supports'            => array( 
            'title', 
            'editor', 
            'thumbnail', 
            'excerpt', 
            'custom-fields' 
        ),
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
        'has_archive'         => false,  // ★
        'rewrite'             => false,  // ★
        'supports'            => array( 
            'title', 
            'editor', 
            'thumbnail', 
            'excerpt', 
            'custom-fields' 
        ),
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
        'has_archive'         => false,  // ★
        'rewrite'             => false,  // ★
        'supports'            => array( 
            'title', 
            'editor', 
            'thumbnail', 
            'excerpt', 
            'custom-fields' 
        ),
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
        'has_archive'         => false,  // ★
        'rewrite'             => false,  // ★
        'supports'            => array( 
            'title', 
            'editor', 
            'thumbnail', 
            'excerpt', 
            'custom-fields' 
        ),
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