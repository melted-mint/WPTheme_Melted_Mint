<?php
/**
 * page-novel.php
 * 
 * - Novel 커스텀 포스트 타입을 10개씩 불러오고
 * - loop.php(templates/novel/loop.php)로 출력
 * - custom_two_skip_pagination() 함수로 페이지네이션
 */

// ------------------------------
// 1) 커스텀 페이지네이션 함수
// ------------------------------
if ( ! function_exists('custom_two_skip_pagination') ) {
    function custom_two_skip_pagination($wp_query = null) {
        if (null === $wp_query) {
            global $wp_query;
        }

        $paged       = max(1, get_query_var('paged'));
        $total_pages = $wp_query->max_num_pages;

        if ($total_pages < 1) {
            return;
        }

        // Tailwind + DaisyUI 버튼 클래스
        $btn_base_class     = "btn btn-square availableButton w-8 h-8 sm:w-9.5 sm:h-9.5 md:w-11 md:h-11 text-xl sm:text-2xl md:text-3xl text-neutral-content border-none buttonComponent flex items-center justify-center";
        $btn_active_class   = "btn btn-square activatedButton w-8 h-8 sm:w-9.5 sm:h-9.5 md:w-11 md:h-11 text-xl sm:text-2xl md:text-3xl text-primary-content border-none flex items-center justify-center buttonComponent";
        $btn_disabled_class = "btn btn-square btn-disabled disabledButton w-8 h-8 sm:w-9.5 sm:h-9.5 text-xl md:w-11 md:h-11 md:text-3xl text-neutral-content border-none opacity-50 cursor-not-allowed flex items-center justify-center";

        echo '<div class="flex items-center justify-center my-4">';

        // << (5페이지 뒤로)
        if ($paged > 5) {
            $skip_5_back = max(1, $paged - 5);
            echo '<a href="' . esc_url(get_pagenum_link($skip_5_back)) . '" class="' . $btn_base_class . '">&laquo;</a>';
        } else {
            echo '<span class="' . $btn_disabled_class . '">&laquo;</span>';
        }

        // < (1페이지 뒤로)
        if ($paged > 1) {
            $skip_1_back = $paged - 1;
            echo '<a href="' . esc_url(get_pagenum_link($skip_1_back)) . '" class="' . $btn_base_class . '">&lsaquo;</a>';
        } else {
            echo '<span class="' . $btn_disabled_class . '">&lsaquo;</span>';
        }

        // 표시 범위(현재 페이지 주변 몇 개씩)
        $range = 2;

        if ($total_pages <= 5) {
            // 5페이지 이하 => 전체 페이지 표시
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $paged) {
                    echo '<span class="' . $btn_active_class . '">' . $i . '</span>';
                } else {
                    echo '<a href="' . esc_url(get_pagenum_link($i)) . '" class="' . $btn_base_class . '">' . $i . '</a>';
                }
            }
        } else {
            // 5페이지 초과 => 처음/끝/현재 주변만 표시
            // 첫 페이지
            if ($paged == 1) {
                echo '<span class="' . $btn_active_class . '">1</span>';
            } else {
                echo '<a href="' . esc_url(get_pagenum_link(1)) . '" class="' . $btn_base_class . '">1</a>';
            }

            $start = max(2, $paged - $range);
            $end   = min($total_pages - 1, $paged + $range);

            if ($start > 2) {
                echo '<span class="' . $btn_disabled_class . '">...</span>';
            }

            for ($i = $start; $i <= $end; $i++) {
                if ($i == $paged) {
                    echo '<span class="' . $btn_active_class . '">' . $i . '</span>';
                } else {
                    echo '<a href="' . esc_url(get_pagenum_link($i)) . '" class="' . $btn_base_class . '">' . $i . '</a>';
                }
            }

            if ($end < $total_pages - 1) {
                echo '<span class="' . $btn_disabled_class . '">...</span>';
            }

            // 마지막 페이지
            if ($paged == $total_pages) {
                echo '<span class="' . $btn_active_class . '">' . $total_pages . '</span>';
            } else {
                echo '<a href="' . esc_url(get_pagenum_link($total_pages)) . '" class="' . $btn_base_class . '">' . $total_pages . '</a>';
            }
        }

        // > (1페이지 앞으로)
        if ($paged < $total_pages) {
            $skip_1_forward = $paged + 1;
            echo '<a href="' . esc_url(get_pagenum_link($skip_1_forward)) . '" class="' . $btn_base_class . '">&rsaquo;</a>';
        } else {
            echo '<span class="' . $btn_disabled_class . '">&rsaquo;</span>';
        }

        // >> (5페이지 앞으로)
        if ($paged + 5 <= $total_pages) {
            $skip_5_forward = $paged + 5;
            echo '<a href="' . esc_url(get_pagenum_link($skip_5_forward)) . '" class="' . $btn_base_class . '">&raquo;</a>';
        } else {
            echo '<span class="' . $btn_disabled_class . '">&raquo;</span>';
        }

        echo '</div>';

        // 페이지 직접 입력 폼 (선택 사항)
        echo '<div class="flex items-center justify-center my-4">';
        echo '<form action="" method="GET" class="flex items-center gap-2">';
        echo '<label for="paged" class="whitespace-nowrap text-sm sm:text-lg md:text-xl">Go to page >> </label>';
        echo '<input type="number" name="paged" min="1" max="' . $total_pages . '" value="' . $paged . '" class="input h-8 sm:h-10 cardComponent w-14 sm:w-17 md:w-20 text-center" />';
        echo '<button type="submit" class="btn btn-ghost btn-sm sm:btn-md cardComponent">Go</button>';
        echo '</form>';
        echo '</div>';
    }
}

// ------------------------------
// 2) Novel CPT 쿼리 설정
// ------------------------------
$paged = max(1, get_query_var('paged'));
$novel_args = array(
    'post_type'      => 'novel',  // 커스텀 포스트 타입
    'posts_per_page' => 10,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'paged'          => $paged,
);

$novel_query = new WP_Query($novel_args);
?>

<!-- 레이아웃 시작 -->
<div class="grid grid-cols-1 lg:grid-cols-[17.5rem_1fr] gap-4 py-4 max-w-[80rem] mx-auto sm:px-4 items-start">

    <!-- 사이드바 (카테고리/태그) -->
    <aside class="order-last lg:order-none lg:sticky lg:top-12 self-start">
        <div class="flex flex-col">
            <div class="flex cardComponent rounded-xl px-2 py-2 mb-4">
                <?php get_template_part('templates/novel/sidebar-left-category'); ?>
            </div>
            <div class="flex cardComponent rounded-xl px-2 py-2">
                <?php get_template_part('templates/novel/sidebar-left-tag'); ?>
            </div>
        </div>
    </aside>

    <!-- 메인 영역 -->
    <main class="order-first lg:order-none rounded-xl">
        <?php
        if ( $novel_query->have_posts() ) {
            // 1) 전역 $wp_query 백업
            global $wp_query;
            $temp_wp_query = $wp_query;

            // 2) 전역을 $novel_query로 교체
            $wp_query = $novel_query;

            // 3) loop.php 불러오기
            //    (여기서 have_posts(), the_post()를 사용할 수 있음)
            get_template_part('templates/novel/loop');

            // 4) 페이지네이션
            custom_two_skip_pagination($novel_query);

            // 5) 복원 + reset
            $wp_query = $temp_wp_query;
            wp_reset_postdata();
        } else {
            echo '<p class="text-center p-4 cardComponent rounded-xl">아직 올라온 소설이 없어요...</p>';
        }
        ?>
    </main>
</div>