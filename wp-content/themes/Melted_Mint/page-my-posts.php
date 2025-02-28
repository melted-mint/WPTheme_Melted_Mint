<?php
/*
Template Name: My Blog
*/

// 1) 로그인 사용자 전용
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( home_url('/') ) );
    exit;
}

get_header();

/**
 * 2) custom_two_skip_pagination 함수가 
 *    이미 functions.php나 다른 공용 파일에 정의되어 있다면
 *    아래 정의부를 제거하시고, 
 *    그 대신 해당 함수가 자동 로드되도록 해주세요.
 */
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

        // 버튼 스타일 (Tailwind + DaisyUI 예시)
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

        // 페이지 번호 표시
        $range = 2;
        if ($total_pages <= 5) {
            // 전체 페이지가 5 이하
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $paged) {
                    echo '<span class="' . $btn_active_class . '">' . $i . '</span>';
                } else {
                    echo '<a href="' . esc_url(get_pagenum_link($i)) . '" class="' . $btn_base_class . '">' . $i . '</a>';
                }
            }
        } else {
            // 5 페이지 초과
            // 1) 항상 1페이지 표시
            if ($paged == 1) {
                echo '<span class="' . $btn_active_class . '">1</span>';
            } else {
                echo '<a href="' . esc_url(get_pagenum_link(1)) . '" class="' . $btn_base_class . '">1</a>';
            }

            // 2) 현재 페이지 주변 표시
            $start = max(2, $paged - $range);
            $end   = min($total_pages - 1, $paged + $range);

            // '...' (왼쪽)
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

            // '...' (오른쪽)
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

        // 페이지 직접 입력 폼 (옵션)
        echo '<div class="flex items-center justify-center my-4">';
        echo '<form action="" method="GET" class="flex items-center gap-2">';
        echo '<label for="paged" class="whitespace-nowrap text-sm sm:text-lg md:text-xl">Go to page >> </label>';
        echo '<input type="number" name="paged" min="1" max="' . $total_pages . '" value="' . $paged . '" class="input h-8 sm:h-10 cardComponent w-14 sm:w-17 md:w-20 text-center" />';
        echo '<button type="submit" class="btn btn-ghost btn-sm sm:btn-md cardComponent">Go</button>';
        echo '</form>';
        echo '</div>';
    }
}

// 3) “현재 로그인한 사용자”가 작성한 글만 쿼리
$paged = max(1, get_query_var('paged'));
$args = array(
    'post_type'      => 'post',
    'author'         => get_current_user_id(),  // ★ 현재 로그인 사용자
    'posts_per_page' => 5,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'paged'          => $paged,
);

$my_query = new WP_Query($args);
?>

<div class="max-w-4xl mx-auto p-4 rounded-lg">
    <h1 class="text-2xl font-bold mb-4">내가 쓴 글</h1>

    <?php if ( $my_query->have_posts() ): ?>
        <ul class="space-y-3">
            <?php while ( $my_query->have_posts() ): $my_query->the_post(); ?>
                <!-- ================================
                     여기부터 loop.php 카드 구조
                     ================================ -->
                <li class="p-2 rounded-lg shadow-md grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-4 cardComponent">
                    
                    <!-- 왼쪽: 텍스트/메타 -->
                    <div class="pl-2 order-2 sm:order-1">
                        <!-- 제목 -->
                        <a href="<?php the_permalink(); ?>" 
                           class="block font-semibold group hoveronlyText text-xl sm:text-2xl">
                            <?php the_title(); ?>
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 inline-block transition-all opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 duration-100 fill-current -mt-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                                <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                            </svg>
                        </a>

                        <!-- Description 메타데이터 표시 -->
                        <div class="ml-2 text-md sm:text-lg">
                            <p>
                                <?php
                                // 'description' 메타
                                $description = get_post_meta( get_the_ID(), 'description', true );
                                echo $description ? esc_html($description) : '';
                                ?>
                            </p>
                        </div>

                        <div class="flex flex-row">
                            <!-- 날짜 -->
                            <div class="flex mt-1 sm:mt-2 flex items-center text-xs sm:text-sm grayTextThings">
                                <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class=" fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                                </div>
                                <!-- 글 날짜 -->
                                <span class="mr-2"><?php echo get_the_date('Y-m-d'); ?></span>
                                <?php if ( get_the_date() != get_the_modified_date() ): ?>
                                    <!-- 마지막 수정일 -->
                                    <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v200h-80v-40H200v400h280v80H200Zm0-560h560v-80H200v80Zm0 0v-80 80ZM560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z"/></svg>
                                    </div>
                                    <span><?php echo get_the_modified_date('Y-m-d'); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="flex mt-1 sm:mt-2 flex w-fit items-center text-xs sm:text-sm grayTextThings">
                                <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M300-80q-58 0-99-41t-41-99v-520q0-58 41-99t99-41h500v600q-25 0-42.5 17.5T740-220q0 25 17.5 42.5T800-160v80H300Zm-60-267q14-7 29-10t31-3h20v-440h-20q-25 0-42.5 17.5T240-740v393Zm160-13h320v-440H400v440Zm-160 13v-453 453Zm60 187h373q-6-14-9.5-28.5T660-220q0-16 3-31t10-29H300q-26 0-43 17.5T240-220q0 26 17 43t43 17Z"/></svg>
                                </div>
                                <!-- 카테고리 목록 -->
                                <div class="btn btn-ghost text-xs sm:text-sm rounded-lg h-7 sm:h-8 w-fit px-1 hoveronlyButton">
                                    <?php the_category(''); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-1 sm:mt-2 flex w-fit items-center text-xs sm:text-sm grayTextThings">
                            <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="m240-160 40-160H120l20-80h160l40-160H180l20-80h160l40-160h80l-40 160h160l40-160h80l-40 160h160l-20 80H660l-40 160h160l-20 80H600l-40 160h-80l40-160H360l-40 160h-80Zm140-240h160l40-160H420l-40 160Z"/></svg>
                            </div>
                            <!-- 태그 목록 -->
                            <div>
                                <?php
                                $tags = get_the_tags();
                                if ( $tags ) :
                                    echo '<div class="flex flex-wrap items-center">';
                                    foreach ( $tags as $index => $tag ) :
                                        $tag_link = get_tag_link( $tag->term_id );
                                        if ( $index > 0 ) {
                                            echo '<span class="mx-0">/</span>';
                                        }
                                        ?>
                                        <a href="<?php echo esc_url( $tag_link ); ?>" 
                                           class="btn btn-ghost rounded-lg px-1 h-7 sm:h-8 lg:h-9 hoveronlyButton">
                                           <?php echo esc_html( $tag->name ); ?>
                                        </a>
                                        <?php
                                    endforeach;
                                    echo '</div>';
                                else:
                                    echo '<span class="grayTextThings ml-1">No tags</span>';
                                endif;
                                ?>
                            </div>
                        </div>

                        <!-- 글자수(공백 제외), 읽기 시간 -->
                        <?php
                        $content_raw = get_the_content(null, false);
                        $content_stripped = wp_strip_all_tags($content_raw);
                        $content_no_spaces = preg_replace('/\s+/', '', $content_stripped);
                        $char_count = mb_strlen($content_no_spaces, 'UTF-8');
                        $word_count = str_word_count($content_stripped);
                        $reading_time = ceil($word_count / 200 + 1);
                        ?>
                        <div class="p-2 mt-1 sm:mt-2 text-md sm:text-lg grayTextThings">
                            <?php echo number_format($char_count); ?> 글자&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            <?php echo $reading_time; ?>분
                        </div>
                    </div>

                    <!-- 썸네일(대표이미지) -->
                    <?php if ( has_post_thumbnail() ): ?>
                        <div class="-mb-2 sm:mb-0 relative group overflow-hidden rounded order-1 sm:order-2">
                            <div class="sm:w-50 w-full h-full">
                                <a href="<?php the_permalink(); ?>" class="block w-full h-full relative">
                                    <?php the_post_thumbnail('medium', [
                                        'class' => 'rounded-lg w-full h-40 sm:h-full object-cover transition ease-in-out duration-300 group-hover:opacity-40'
                                    ]); ?>
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition ease-in-out duration-200">
                                        <svg class="w-16 h-16 text-white" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                                            <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                                        </svg>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="hidden sm:block relative group overflow-hidden rounded order-1 sm:order-2">
                            <div class="sm:w-24 sm:h-full">
                                <a href="<?php the_permalink(); ?>" class="btn btn-ghost rounded-lg tagButton w-full sm:h-full sm:flex items-center justify-center text-base-content">
                                    <svg class="w-16 h-16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                                        <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>

        <!-- 4) 페이지네이션 호출 -->
        <?php custom_two_skip_pagination($my_query); ?>
    <?php else: ?>
        <p>아직 작성한 글이 없습니다.</p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</div>

<?php get_footer(); ?>