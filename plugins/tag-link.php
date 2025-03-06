<?php
function custom_tag_rewrite_rules() {
    // /tag/(태그슬러그)/(mypage) -> tag_name=$matches[1], mypage=$matches[2]
    add_rewrite_rule(
        '^tag/([^/]+)/([^/]+)/?',
        'index.php?tag_name=$matches[1]&mypage=$matches[2]',
        'top'
    );
}
add_action('init', 'custom_tag_rewrite_rules');

// 'mypage'라는 query_var 등록
function add_mypage_query_var($vars) {
    $vars[] = 'mypage';
    return $vars;
}
add_filter('query_vars', 'add_mypage_query_var');
?>