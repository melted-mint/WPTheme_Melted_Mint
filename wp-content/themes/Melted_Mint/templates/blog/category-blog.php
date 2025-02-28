<?php
/**
 * category-13.php
 * 카테고리 ID=13 전용 템플릿
 */

// ------------------------------
// 1) 커스텀 페이지네이션 함수
// ------------------------------
if ( ! function_exists('custom_two_skip_pagination') ) {
    function custom_two_skip_pagination($wp_query = null) {
        // $wp_query가 없으면 전역 $wp_query 사용
        if (null === $wp_query) {
            global $wp_query;
        }

        $paged       = max(1, get_query_var('paged'));
        $total_pages = $wp_query->max_num_pages;

        // 페이지가 없는 경우에는 표시할 필요가 없으므로 종료
        if ($total_pages < 1) {
            return;
        }

        // 버튼 스타일 (Tailwind + DaisyUI 예시)
        $btn_base_class     = "btn btn-square availableButton w-8 h-8 sm:w-9.5 sm:h-9.5 md:w-11 md:h-11 text-xl sm:text-2xl md:text-3xl text-neutral-content border-none buttonComponent flex items-center justify-center";
        $btn_active_class   = "btn btn-square activatedButton w-8 h-8 sm:w-9.5 sm:h-9.5 md:w-11 md:h-11 text-xl sm:text-2xl md:text-3xl text-primary-content border-none flex items-center justify-center buttonComponent";
        $btn_disabled_class = "btn btn-square btn-disabled disabledButton w-8 h-8 sm:w-9.5 sm:h-9.5 text-xl md:w-11 md:h-11 md:text-3xl text-neutral-content border-none opacity-50 cursor-not-allowed flex items-center justify-center";

        // 페이지네이션 컨테이너 (화살표 + 페이지 번호)
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

        // ------------------------------
        // 페이지 번호 표시 로직
        // ------------------------------
        // - 5페이지 이하인 경우: 전체 페이지(1~total_pages) 모두 출력
        // - 5페이지 초과인 경우: 처음/끝/현재 주변만 출력하고, 나머지는 '...'로 축약

        // 표시 범위(현재 페이지 기준 좌우로 몇 개씩)
        $range = 2;

        if ($total_pages <= 5) {
            // 5페이지 이하 => 모든 페이지 번호 노출
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $paged) {
                    echo '<span class="' . $btn_active_class . '">' . $i . '</span>';
                } else {
                    echo '<a href="' . esc_url(get_pagenum_link($i)) . '" class="' . $btn_base_class . '">' . $i . '</a>';
                }
            }
        } else {
            // 5페이지 초과 => 처음/끝/현재 주변만 표시
            // 1) 항상 페이지 1 표시
            if ($paged == 1) {
                echo '<span class="' . $btn_active_class . '">1</span>';
            } else {
                echo '<a href="' . esc_url(get_pagenum_link(1)) . '" class="' . $btn_base_class . '">1</a>';
            }

            // 2) 현재 페이지 기준 범위 계산
            $start = max(2, $paged - $range);
            $end   = min($total_pages - 1, $paged + $range);

            // 3) 만약 시작 번호가 2보다 크면 => '...' 표시
            if ($start > 2) {
                echo '<span class="' . $btn_disabled_class . '">...</span>';
            }

            // 4) 현재 페이지 주변 번호 출력
            for ($i = $start; $i <= $end; $i++) {
                if ($i == $paged) {
                    echo '<span class="' . $btn_active_class . '">' . $i . '</span>';
                } else {
                    echo '<a href="' . esc_url(get_pagenum_link($i)) . '" class="' . $btn_base_class . '">' . $i . '</a>';
                }
            }

            // 5) end가 마지막 페이지 직전보다 작으면 => '...' 표시
            if ($end < $total_pages - 1) {
                echo '<span class="' . $btn_disabled_class . '">...</span>';
            }

            // 6) 항상 마지막 페이지 표시(단, total_pages > 1인 경우)
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

        echo '</div>'; // .flex

        // ------------------------------
        // 페이지 직접 입력 폼
        // ------------------------------
        // - GET 파라미터 paged 로 이동 (Plain 퍼머링크 기준)
        // - 예쁜(permalink) 구조를 쓰신다면 별도 처리 필요
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
// 2) 카테고리 13 글 쿼리 설정
// ------------------------------
$paged = max(1, get_query_var('paged'));
query_posts(array(
    'cat'            => 'blog',      // 카테고리 ID
    'posts_per_page' => 10,      // 한 페이지에 10개
    'orderby'        => 'date',  // 날짜 기준
    'order'          => 'DESC',  // 최신글 순
    'paged'          => $paged,
));
?>

<!-- 레이아웃 시작 -->
<div class="grid grid-cols-1 lg:grid-cols-[17.5rem_1fr] gap-4 py-4 max-w-[80rem] mx-auto sm:px-4 items-start">
    <!-- 사이드바 -->
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

    <!-- 메인 -->
    <main class="order-first lg:order-none rounded-xl">
        <?php get_template_part('templates/blog/loop'); ?>
        <?php custom_two_skip_pagination(); ?>
    </main>
</div>

<?php
// 쿼리 리셋
wp_reset_query();
?>