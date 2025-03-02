<?php
// Blogs!
// Blog: /blog/yyyy/mm/dd/hh/ii/ID/slug/
add_filter('post_type_link', 'my_blog_permalink_slug_id', 10, 2);
function my_blog_permalink_slug_id($permalink, $post) {
    if ($post->post_type !== 'blog') return $permalink;

    $year    = get_the_time('Y', $post);
    $month   = get_the_time('m', $post);
    $day     = get_the_time('d', $post);
    $hour    = get_the_time('H', $post);
    $minute  = get_the_time('i', $post);
    $pid     = $post->ID;            // 글 ID

    // slug에서 -2, -3 등 숫자 접미사 제거
    $slug = $post->post_name;
    $slug = preg_replace('/-\d+$/', '', $slug);

    // 최종 URL 예: /blog/2025/03/02/12/35/123/testasdf/
    return home_url("/blog/$year/$month/$day/$hour/$minute/$pid/$slug/");
}
add_action('init', 'my_blog_rewrite_rule');
function my_blog_rewrite_rule() {
    // ^blog/(\d{4})/(\d{2})/(\d{2})/(\d{2})/(\d{2})/(\d+)/([^/]+)/?$
    // $matches[6] => ID, $matches[7] => slug
    add_rewrite_rule(
        '^blog/(\d{4})/(\d{2})/(\d{2})/(\d{2})/(\d{2})/(\d+)/([^/]+)/?$',
        'index.php?post_type=blog&p=$matches[6]',
        'top'
    );
}

// Novels!
// Novel: /novel/yyyy/mm/dd/hh/ii/ID/slug/
add_filter('post_type_link', 'my_novel_permalink_slug_id', 10, 2);
function my_novel_permalink_slug_id($permalink, $post) {
    if ($post->post_type !== 'novel') return $permalink;

    $year   = get_the_time('Y', $post);
    $month  = get_the_time('m', $post);
    $day    = get_the_time('d', $post);
    $hour   = get_the_time('H', $post);
    $minute = get_the_time('i', $post);
    $pid    = $post->ID;

    // slug에서 -2, -3 등 숫자 접미사 제거
    $slug = $post->post_name;
    $slug = preg_replace('/-\d+$/', '', $slug);

    return home_url("/novel/$year/$month/$day/$hour/$minute/$pid/$slug/");
}
add_action('init', 'my_novel_rewrite_rule');
function my_novel_rewrite_rule() {
    add_rewrite_rule(
        '^novel/(\d{4})/(\d{2})/(\d{2})/(\d{2})/(\d{2})/(\d+)/([^/]+)/?$',
        'index.php?post_type=novel&p=$matches[6]',
        'top'
    );
}

// Spinoffs!
// Spinoff: /spinoff/yyyy/mm/dd/hh/ii/ID/slug/
add_filter('post_type_link', 'my_spinoff_permalink_slug_id', 10, 2);
function my_spinoff_permalink_slug_id($permalink, $post) {
    if ($post->post_type !== 'spinoff') return $permalink;

    $year   = get_the_time('Y', $post);
    $month  = get_the_time('m', $post);
    $day    = get_the_time('d', $post);
    $hour   = get_the_time('H', $post);
    $minute = get_the_time('i', $post);
    $pid    = $post->ID;

    // slug에서 -2, -3 등 숫자 접미사 제거
    $slug = $post->post_name;
    $slug = preg_replace('/-\d+$/', '', $slug);

    return home_url("/spinoff/$year/$month/$day/$hour/$minute/$pid/$slug/");
}
add_action('init', 'my_spinoff_rewrite_rule');
function my_spinoff_rewrite_rule() {
    add_rewrite_rule(
        '^spinoff/(\d{4})/(\d{2})/(\d{2})/(\d{2})/(\d{2})/(\d+)/([^/]+)/?$',
        'index.php?post_type=spinoff&p=$matches[6]',
        'top'
    );
}

// Communities!
// Community: /community/yyyy/mm/dd/hh/ii/ID/slug/
add_filter('post_type_link', 'my_community_permalink_slug_id', 10, 2);
function my_community_permalink_slug_id($permalink, $post) {
    if ($post->post_type !== 'community') return $permalink;

    $year   = get_the_time('Y', $post);
    $month  = get_the_time('m', $post);
    $day    = get_the_time('d', $post);
    $hour   = get_the_time('H', $post);
    $minute = get_the_time('i', $post);
    $pid    = $post->ID;

    // slug에서 -2, -3 등 숫자 접미사 제거
    $slug = $post->post_name;
    $slug = preg_replace('/-\d+$/', '', $slug);

    return home_url("/community/$year/$month/$day/$hour/$minute/$pid/$slug/");
}
add_action('init', 'my_community_rewrite_rule');
function my_community_rewrite_rule() {
    add_rewrite_rule(
        '^community/(\d{4})/(\d{2})/(\d{2})/(\d{2})/(\d{2})/(\d+)/([^/]+)/?$',
        'index.php?post_type=community&p=$matches[6]',
        'top'
    );
}