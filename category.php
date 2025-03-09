<?php
/**
 * category.php
 * 
 * 이 파일에서:
 *   1) get_query_var('mypage') 로 페이지 슬러그 확인
 *   2) switch 문으로 분기
 *   3) 해당 slug에 맞는 template part 호출 (category-{slug}.php)
 * 
 * 주의: category-(page slug).php 파일이 필요합니다.
 */

get_header();

// URL 구조에서 rewrite로 등록된 mypage 변수 가져오기
$mypage = get_query_var('mypage'); // 예: blog, community, novel, spinoff 등

switch ($mypage) {
    case 'blog':
        // category-blog.php 불러오기
        get_template_part('category-blog');
        break;

    case 'community':
        // category-community.php 불러오기
        get_template_part('category-community');
        break;

    case 'novel':
        // category-novel.php 불러오기
        get_template_part('category-novel');
        break;

    case 'spinoff':
        // category-spinoff.php 불러오기
        get_template_part('category-spinoff');
        break;

    default:
        // 그 외 slug가 없거나 잘못된 값인 경우 => blog 템플릿(또는 원하는 템플릿)으로 처리
        get_template_part('category-blog');
        break;
}
?>
<!-- 공통 Footer 파트 (공유) -->
<?php 
get_template_part('footer-navigation');
get_template_part('footer-scroll');
get_footer(); 