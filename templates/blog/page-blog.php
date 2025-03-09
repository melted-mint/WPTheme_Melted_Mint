<?php
/**
 * page-blog.php
 * 
 * - Blog 커스텀 포스트 타입을 10개씩 불러오고
 * - loop.php(templates/blog/loop.php)로 출력
 * - custom_two_skip_pagination() 함수로 페이지네이션
 */

// ------------------------------
// 2) Blog CPT 쿼리 설정
// ------------------------------
$paged = max(1, get_query_var('paged'));
$blog_args = array(
    'post_type'      => 'blog',  // 커스텀 포스트 타입
    'posts_per_page' => 10,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'paged'          => $paged,
);

$blog_query = new WP_Query($blog_args);
?>

<!-- 레이아웃 시작 -->
<div class="grid grid-cols-1 lg:grid-cols-[17.5rem_1fr] gap-4 py-4 max-w-[80rem] mx-auto sm:px-4 items-start">

    <!-- 사이드바 (카테고리/태그) -->
    <aside class="order-last lg:order-none lg:sticky lg:top-12 self-start">
        <div class="flex flex-col">
            <div class="flex cardComponent rounded-xl px-2 py-2 mb-4">
                <?php get_template_part('templates/blog/sidebar-left-category'); ?>
            </div>
            <div class="flex cardComponent rounded-xl px-2 py-2">
                <?php get_template_part('templates/blog/sidebar-left-tag'); ?>
            </div>
        </div>
    </aside>

    <!-- 메인 영역 -->
    <main class="order-first lg:order-none rounded-xl">
        <?php
        if ( $blog_query->have_posts() ) {
            // 1) 전역 $wp_query 백업
            global $wp_query;
            $temp_wp_query = $wp_query;

            // 2) 전역을 $blog_query로 교체
            $wp_query = $blog_query;

            // 3) loop.php 불러오기
            //    (여기서 have_posts(), the_post()를 사용할 수 있음)
            get_template_part('templates/blog/loop');

            // 4) 페이지네이션
            custom_two_skip_pagination($blog_query);

            // 5) 복원 + reset
            $wp_query = $temp_wp_query;
            wp_reset_postdata();
        } else {
            echo '<p class="text-center p-4 cardComponent rounded-xl">아직 블로그에 올라온 글이 없어요...</p>';
        }
        ?>
    </main>
</div>