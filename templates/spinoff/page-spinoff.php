<?php
/**
 * page-spinoff.php
 * 
 * - Spinoff 커스텀 포스트 타입을 10개씩 불러오고
 * - loop.php(templates/spinoff/loop.php)로 출력
 * - custom_two_skip_pagination() 함수로 페이지네이션
 */

// ------------------------------
// 2) Spinoff CPT 쿼리 설정
// ------------------------------
$paged = max(1, get_query_var('paged'));
$spinoff_args = array(
    'post_type'      => 'spinoff',  // 커스텀 포스트 타입
    'posts_per_page' => 10,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'paged'          => $paged,
);

$spinoff_query = new WP_Query($spinoff_args);
?>

<!-- 레이아웃 시작 -->
<div class="grid grid-cols-1 lg:grid-cols-[17.5rem_1fr] gap-4 py-4 max-w-[80rem] mx-auto sm:px-4 items-start">

    <!-- 사이드바 (카테고리/태그) -->
    <aside class="order-last lg:order-none lg:sticky lg:top-12 self-start">
        <div class="flex flex-col">
            <div class="flex cardComponent rounded-xl px-2 py-2 mb-4">
                <?php get_template_part('templates/spinoff/sidebar-left-category'); ?>
            </div>
            <div class="flex cardComponent rounded-xl px-2 py-2">
                <?php get_template_part('templates/spinoff/sidebar-left-tag'); ?>
            </div>
        </div>
    </aside>

    <!-- 메인 영역 -->
    <main class="order-first lg:order-none rounded-xl">
        <?php
        if ( $spinoff_query->have_posts() ) {
            // 1) 전역 $wp_query 백업
            global $wp_query;
            $temp_wp_query = $wp_query;

            // 2) 전역을 $spinoff_query로 교체
            $wp_query = $spinoff_query;

            // 3) loop.php 불러오기
            //    (여기서 have_posts(), the_post()를 사용할 수 있음)
            get_template_part('templates/spinoff/loop');

            // 4) 페이지네이션
            custom_two_skip_pagination($spinoff_query);

            // 5) 복원 + reset
            $wp_query = $temp_wp_query;
            wp_reset_postdata();
        } else {
            echo '<p class="text-center p-4 cardComponent rounded-xl">아직 외전에 올라온 글이 없어요...</p>';
        }
        ?>
    </main>
</div>