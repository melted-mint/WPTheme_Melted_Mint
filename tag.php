<?php
/**
 * tag.php (카테고리 제거 + 문제 해결 버전)
 * 
 * 요구사항:
 *   1) 카테고리 제거
 *   2) 태그 목록을 사이드바에 배치
 *   3) 선택된 태그는 activatedButton. 해당 태그만 필터링
 *   4) 블로그/커뮤니티 버튼 누르면 해당 CPT만 표시 + activatedButton
 * 
 * 수정사항:
 *   - 블로그/커뮤니티 버튼 클릭 시 홈으로 돌아가는 문제 해결(쿼리스트링 방식)
 *   - 선택 태그가 한글일 때 퍼센트 인코딩되는 문제 해결(태그 이름으로 표시)
 */

get_header();

/** ------------------------------
 * 1) 커스텀 페이지네이션
 * ------------------------------ */
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
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $paged) {
                    echo '<span class="' . $btn_active_class . '">' . $i . '</span>';
                } else {
                    echo '<a href="' . esc_url(get_pagenum_link($i)) . '" class="' . $btn_base_class . '">' . $i . '</a>';
                }
            }
        } else {
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

        // (선택) 페이지 직접 이동 폼
        echo '<div class="flex items-center justify-center my-4">';
        echo '<form action="" method="GET" class="flex items-center gap-2">';
        echo '<label for="paged" class="whitespace-nowrap text-sm sm:text-lg md:text-xl">Go to page >> </label>';
        echo '<input type="number" name="paged" min="1" max="' . $total_pages . '" value="' . $paged . '" class="input h-8 sm:h-10 cardComponent w-14 sm:w-17 md:w-20 text-center" />';
        echo '<button type="submit" class="btn btn-ghost btn-sm sm:btn-md cardComponent">Go</button>';
        echo '</form>';
        echo '</div>';
    }
}

/** ------------------------------
 * 2) GET 파라미터로 mypage, tag 체크
 * ------------------------------ */
// get_query_var('mypage')가 비어 있으면 $_GET['mypage'] 사용
$mypage = get_query_var('mypage');
if ( empty($mypage) && isset($_GET['mypage']) ) {
    $mypage = sanitize_text_field($_GET['mypage']);
}

// blog / community 구분
switch ($mypage) {
    case 'community':
        $current_post_type = 'community';
        $page_label        = '커뮤니티';
        break;
    case 'blog':
    default:
        $current_post_type = 'blog';
        $page_label        = '블로그';
        break;
}

// get_query_var('tag')가 비어 있으면 $_GET['tag'] 사용
$current_tag_slug = get_query_var('tag');
if ( empty($current_tag_slug) && isset($_GET['tag']) ) {
    $current_tag_slug = sanitize_text_field($_GET['tag']);
}

/** ------------------------------
 * 3) 메인 쿼리: mypage(CPT) + tag 필터링
 * ------------------------------ */
$paged = max(1, get_query_var('paged'));
$args = array(
    'post_type'      => $current_post_type,   // 블로그 or 커뮤니티
    'posts_per_page' => 10,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
);
if ( ! empty($current_tag_slug) ) {
    $args['tag'] = $current_tag_slug;
}
$query = new WP_Query($args);

/** ------------------------------
 * 4) 사이드바용 태그 목록
 * ------------------------------ */
$all_tags = get_tags(array(
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC'
));
?>

<!-- 레이아웃 시작 -->
<div class="grid grid-cols-1 lg:grid-cols-[17.5rem_1fr] gap-4 py-4 max-w-[80rem] mx-auto sm:px-4 items-start">

    <!-- 사이드바 -->
    <aside class="order-last lg:order-none lg:sticky lg:top-12 w-full self-start">
        
        <!-- 상단: 블로그/커뮤니티 버튼 (페이지 전환) -->
        <div class="flex cardComponent rounded-xl px-2 py-2 max-w-[80rem] mx-auto sm:px-4 mb-4">
            <h3 class="text-lg font-bold mr-5 mt-1">페이지</h3>
            <ul class="flex space-x-2">
                <?php
                // 블로그/커뮤니티 버튼 각각 생성
                $pages = array(
                    'blog'      => '블로그',
                    'community' => '커뮤니티',
                );
                foreach ($pages as $slug => $label):
                    // 현재 페이지와 일치하면 activatedButton
                    $page_active_class = ($slug === $mypage) ? 'activatedButton' : '';
                    
                    // 1) 기본 링크: home_url() => 예) http://도메인/
                    // 2) add_query_arg( ['mypage'=>blog, 'tag'=>$current_tag_slug ], .. )
                    //    => 누르면 blog + 현재 태그 유지
                    $btn_link = add_query_arg(
                        array(
                            'mypage' => $slug,
                            'tag'    => $current_tag_slug, // 현재 태그가 있다면 그대로 이어붙이기
                        ),
                        home_url('/') // 루트(혹은 원하는 베이스 URL)
                    );
                ?>
                <li>
                    <a href="<?php echo esc_url($btn_link); ?>"
                       class="btn btn-ghost tagButton btn-md mx-1 <?php echo esc_attr($page_active_class); ?>">
                       <?php echo esc_html($label); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- 태그 목록 -->
        <div class="w-full cardComponent p-4 rounded-xl">
            <h3 class="text-lg font-bold mb-2">태그</h3>
            <ul class="flex flex-wrap gap-2">
                <?php foreach ($all_tags as $tag_obj): ?>
                    <?php
                    // 현재 선택된 태그와 같으면 activatedButton
                    $tag_active_class = ($tag_obj->slug === $current_tag_slug) ? 'activatedButton' : '';

                    // 태그 링크도 동일하게 ?mypage=XXX&tag=YYY 형태로 가도 되지만,
                    // WordPress 기본 태그 링크(/tag/slug/)를 쓰려면 rewrite 설정이 필요.
                    // 여기서는 쿼리스트링 방식으로 일관성 있게 처리해볼 수도 있음.

                    $tag_link = add_query_arg(
                        array(
                            'mypage' => $mypage,       // 현재 페이지(블로그/커뮤니티)
                            'tag'    => $tag_obj->slug // 클릭한 태그
                        ),
                        home_url('/')
                    );
                    ?>
                    <li>
                        <a href="<?php echo esc_url($tag_link); ?>"
                           class="btn btn-ghost p-1 tagButton h-8 <?php echo esc_attr($tag_active_class); ?>">
                           <?php echo esc_html($tag_obj->name); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <!-- 메인 영역 -->
    <main class="order-first lg:order-none rounded-xl">
        <!-- 상단 제목 -->
        <div class="cardComponent p-4 rounded-xl mb-4">
            <h2 class="text-xl font-semibold"><?php echo esc_html($page_label); ?></h2>
            <?php
            // 인코딩 풀린 태그명을 표시하려면, slug -> term으로 찾아서 name 사용
            if ( ! empty($current_tag_slug) ) {
                $tag_term = get_term_by('slug', $current_tag_slug, 'post_tag');
                if ( $tag_term && ! is_wp_error($tag_term) ) {
                    $decoded_tag_name = $tag_term->name;
                } else {
                    // term이 없으면 slug 자체를 urldecode
                    $decoded_tag_name = urldecode($current_tag_slug);
                }
                echo '<p class="text-sm mt-2">선택한 태그: <strong>' 
                     . esc_html($decoded_tag_name) 
                     . '</strong></p>';
            }
            ?>
        </div>

        <!-- 게시물 루프 -->
        <?php if ($query->have_posts()) : ?>
            <ul class="space-y-3">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <li class="p-2 rounded-lg shadow-md grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-4 cardComponent">
                        
                        <!-- 왼쪽: 텍스트/메타 -->
                        <div class="pl-2 order-2 sm:order-1">
                            <!-- 제목 -->
                            <a href="<?php the_permalink(); ?>"
                               class="block font-semibold group hoveronlyText text-xl sm:text-2xl">
                                <?php the_title(); ?>
                                <svg class="w-8 h-8 sm:w-10 sm:h-10 inline-block transition-all opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 duration-100 fill-current -mt-2" fill="currentColor" viewBox="0 -960 960 960">
                                    <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                                </svg>
                            </a>

                            <!-- 예시: Description 메타데이터 -->
                            <div class="ml-2 text-md sm:text-lg">
                                <p>
                                    <?php
                                    $description = get_post_meta(get_the_ID(), 'description', true);
                                    if ($description) {
                                        echo esc_html($description);
                                    }
                                    ?>
                                </p>
                            </div>

                            <!-- 날짜/수정일 + 카테고리 -->
                            <div class="flex flex-row">
                                <!-- 날짜 -->
                                <div class="flex mt-1 sm:mt-2 flex items-center text-xs sm:text-sm grayTextThings">
                                    <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-2">
                                        <!-- 날짜 아이콘 -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class=" fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                                    </div>
                                    <span class="mr-2"><?php echo get_the_date('Y-m-d'); ?></span>
                                    <?php if ( get_the_date() != get_the_modified_date() ): ?>
                                        <!-- 마지막 수정일 -->
                                        <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v200h-80v-40H200v400h280v80H200Zm0-560h560v-80H200v80Zm0 0v-80 80ZM560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z"/></svg>
                                        </div>
                                        <span><?php echo get_the_modified_date('Y-m-d'); ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- 카테고리 -->
                                <div class="flex mt-1 sm:mt-2 flex w-fit items-center text-xs sm:text-sm grayTextThings">
                                    <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                                        <!-- 카테고리 아이콘 -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M300-80q-58 0-99-41t-41-99v-520q0-58 41-99t99-41h500v600q-25 0-42.5 17.5T740-220q0 25 17.5 42.5T800-160v80H300Zm-60-267q14-7 29-10t31-3h20v-440h-20q-25 0-42.5 17.5T240-740v393Zm160-13h320v-440H400v440Zm-160 13v-453 453Zm60 187h373q-6-14-9.5-28.5T660-220q0-16 3-31t10-29H300q-26 0-43 17.5T240-220q0 26 17 43t43 17Z"/></svg>
                                    </div>
                                    <!-- the_category() -->
                                    <div class="btn btn-ghost text-xs sm:text-sm rounded-lg h-7 sm:h-8 w-fit px-1 hoveronlyButton">
                                        <?php the_category(''); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- 태그 목록 -->
                            <div class="mt-1 sm:mt-2 flex w-fit items-center text-xs sm:text-sm grayTextThings">
                                <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6">
                                        <path d="m240-160 40-160H120l20-80h160l40-160H180l20-80h160l40-160h80l-40 160h160l40-160h80l-40 160h160l-20 80H660l-40 160h160l-20 80H600l-40 160h-80l40-160H360l-40 160h-80Zm140-240h160l40-160H420l-40 160Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <?php
                                    $post_tags = get_the_tags();
                                    if ($post_tags) {
                                        echo '<div class="flex flex-wrap items-center">';
                                        foreach ($post_tags as $index => $t) {
                                            $tag_link = add_query_arg(
                                                array(
                                                    'mypage' => $mypage,    // 현재 페이지
                                                    'tag'    => $t->slug,   // 해당 글의 태그 슬러그
                                                ),
                                                home_url('/')
                                            );
                                            // 첫 버튼이 아니라면 슬래시 구분
                                            if ($index > 0) {
                                                echo '<span class="mx-0">/</span>';
                                            }
                                            ?>
                                            <a href="<?php echo esc_url($tag_link); ?>"
                                               class="btn btn-ghost rounded-lg px-1 h-7 sm:h-8 lg:h-9 hoveronlyButton">
                                                <?php echo esc_html($t->name); ?>
                                            </a>
                                            <?php
                                        }
                                        echo '</div>';
                                    } else {
                                        echo '<span class="grayTextThings ml-1">태그 없음</span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- 글자수/읽기시간 (예시) -->
                            <?php
                            $content_raw       = get_the_content(null, false);
                            $content_stripped  = wp_strip_all_tags($content_raw);
                            $content_no_spaces = preg_replace('/\s+/', '', $content_stripped);
                            $char_count        = mb_strlen($content_no_spaces, 'UTF-8');
                            $word_count        = str_word_count($content_stripped);
                            $reading_time      = ceil($word_count / 200 + 1);
                            ?>
                            <div class="p-2 mt-1 sm:mt-2 text-md sm:text-lg grayTextThings">
                                <?php echo number_format($char_count); ?> 글자 |
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
                                            <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 -960 960 960">
                                                <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- 썸네일이 없을 때 -->
                            <div class="hidden sm:block relative group overflow-hidden rounded order-1 sm:order-2">
                                <div class="sm:w-24 sm:h-full">
                                    <a href="<?php the_permalink(); ?>"
                                       class="btn btn-ghost rounded-lg tagButton w-full sm:h-full sm:flex items-center justify-center text-base-content">
                                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 -960 960 960">
                                            <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>

            <!-- 페이지네이션 -->
            <?php custom_two_skip_pagination($query); ?>
            <?php wp_reset_postdata(); ?>

        <?php else : ?>
            <p class="text-center p-4 cardComponent rounded-xl">
                게시물이 없습니다.
            </p>
        <?php endif; ?>
    </main>
</div>

<?php
get_template_part('templates/category-tag-footer-navigation');
get_footer(); ?>