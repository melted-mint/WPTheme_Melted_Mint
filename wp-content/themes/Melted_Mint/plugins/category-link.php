<?php
/**
 * /category/(카테고리슬러그)/(페이지이름)/?  구조를
 * index.php?category_name=$matches[1]&mypage=$matches[2] 로 매핑
 * 예: /category/news/blog => category_name=news, mypage=blog
 */
function my_custom_page_rewrites() {
    add_rewrite_rule(
        '^category/([^/]+)/([^/]+)/?$',
        'index.php?category_name=$matches[1]&mypage=$matches[2]',
        'top'
    );
}
add_action('init', 'my_custom_page_rewrites');

/**
 * mypage 라는 커스텀 변수를 쿼리바에 등록
 */
function my_custom_query_vars($vars) {
    $vars[] = 'mypage'; // 페이지(섹션) 식별용
    return $vars;
}
add_filter('query_vars', 'my_custom_query_vars');
?>