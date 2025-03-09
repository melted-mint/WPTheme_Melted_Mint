<?php
/**
 * category.php
 * 
 * URL 구조: /category/(카테고리슬러그)/(페이지이름)
 * 예) /category/news/blog
 *     => category_name=news, mypage=blog
 */

get_header();

/** ------------------------------
 * 2) 카테고리/페이지(세션) 파라미터
 * ------------------------------ */
// rewrite 규칙을 통해 category_name, mypage가 넘어옴
$category_slug = get_query_var('category_name'); // 예: news, notice 등
$mypage        = get_query_var('mypage');        // 예: blog, community 등

// 세션 => CPT 매핑
switch ($mypage) {
    case 'blog':
        $current_post_type = 'blog';
        $page_label        = '블로그';
        break;
    case 'community':
        $current_post_type = 'community';
        $page_label        = '커뮤니티';
        break;
    default:
        // 잘못된 페이지이름이면 기본값(블로그)으로 처리
        $current_post_type = 'blog';
        $page_label        = '블로그';
        break;
}

/** ------------------------------
 * 3) 메인 쿼리: 현재 CPT + 카테고리
 * ------------------------------ */
$paged = max(1, get_query_var('paged'));
$args = array(
    'post_type'      => $current_post_type,
    'posts_per_page' => 10,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'category_name'  => $category_slug, // 기본 카테고리 필터
);
$query = new WP_Query($args);

/** ------------------------------
 * 4) 사이드바용 카테고리 목록
 *    - 소설(novel), 외전(spinoff)에 쓰인 카테고리는 제외
 *    - 현재 CPT에 글이 없는 카테고리는 제외
 * ------------------------------ */
$all_categories = get_categories(array(
    'hide_empty' => false,  // 일단 모두 가져온 뒤, 개별적으로 필터링
    'orderby'    => 'id',
    'order'      => 'ASC',
));

// (A) 소설, 외전에 쓰인 카테고리인지 체크
function is_used_by_novel_or_spinoff($cat_id) {
    $check_query = new WP_Query(array(
        'post_type'      => array('novel','spinoff'),
        'cat'            => $cat_id,
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ));
    $used = $check_query->have_posts();
    wp_reset_postdata();
    return $used;
}

// (B) 현재 CPT에 글이 1개 이상 있는지 확인
function has_post_in_current_cpt($cat_id, $cpt) {
    $check_query = new WP_Query(array(
        'post_type'      => $cpt,
        'cat'            => $cat_id,
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ));
    $has_post = $check_query->have_posts();
    wp_reset_postdata();
    return $has_post;
}
?>

<!-- 레이아웃 시작 -->
<div class="grid grid-cols-1 lg:grid-cols-[17.5rem_1fr] gap-4 py-4 max-w-[80rem] mx-auto sm:px-4 items-start">

    <!-- 사이드바 (카테고리만) -->
    <aside class="order-last lg:order-none lg:sticky lg:top-12 w-full self-start">
        <!-- 상단: 세션(페이지) 선택 -->
        <div class="flex cardComponent rounded-xl px-2 py-2 max-w-[80rem] mx-auto sm:px-4 mb-4">
            <h3 class="text-lg font-bold mr-5 mt-1">페이지</h3>
            <ul class="flex space-x-2">
                <?php
                $pages = array(
                    'blog'      => '블로그',
                    'community' => '커뮤니티',
                );
                foreach ($pages as $slug => $label):
                    // 현재 섹션(mypage) 활성화 여부
                    $page_active_class = ($slug === $mypage) ? 'activatedButton' : '';
                    // 링크: /category/현재카테고리슬러그/새mypage
                    $page_link = home_url('/category/' . $category_slug . '/' . $slug);
                ?>
                <li>
                    <a href="<?php echo esc_url($page_link); ?>"
                    class="btn btn-ghost tagButton btn-md mx-1 <?php echo esc_attr($page_active_class); ?>">
                    <?php echo esc_html($label); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="w-full cardComponent p-4 rounded-xl">
            <h3 class="text-lg font-bold mb-2">카테고리</h3>
            <ul class="space-y-1">
            <?php foreach ($all_categories as $cat): ?>
                <?php
                // 1) 소설(novel), 외전(spinoff)에 쓰인 카테고리면 제외
                if ( is_used_by_novel_or_spinoff($cat->term_id) ) {
                    continue;
                }
                // 2) 현재 CPT에 글이 없으면 제외
                if ( ! has_post_in_current_cpt($cat->term_id, $current_post_type) ) {
                    continue;
                }
                // 활성화 여부
                $active_class = ($cat->slug === $category_slug) ? 'activatedButton' : '';
                // 클릭 시 /category/새카테고리슬러그/현재mypage
                $cat_link = home_url('/category/' . $cat->slug . '/' . $mypage);
                ?>
                <li class="w-full hoveronlyButton rounded-lg h-10">
                    <a href="<?php echo esc_url($cat_link); ?>"
                    class="btn btn-ghost w-full flex justify-between items-center px-2 text-left <?php echo esc_attr($active_class); ?>">
                        <!-- 왼쪽: 카테고리명 -->
                        <span class="mt-1 text-sm">
                            <?php echo esc_html($cat->name); ?>
                        </span>

                        <!-- 오른쪽: 게시물 개수 -->
                        <span class="btn btn-ghost btn-disabled counterButton rounded-lg px-2 h-7">
                            <?php echo esc_html($cat->count); ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <!-- 메인 영역 -->
    <main class="order-first lg:order-none rounded-xl">
        <!-- 선택된 카테고리 + 페이지 라벨 -->
        <div class="cardComponent p-4 rounded-xl mb-4">
            <h2 class="text-xl font-semibold">
                <?php
                // 현재 카테고리명
                $current_cat_obj = get_category_by_slug($category_slug);
                $cat_name = $current_cat_obj ? $current_cat_obj->name : $category_slug;
                echo esc_html($cat_name) . ' > ' . esc_html($page_label);
                ?>
            </h2>
        </div>

        <!-- 게시물 루프 (사용자님이 주신 카드형 loop) -->
        <?php if ($query->have_posts()) : ?>
            <ul class="space-y-3">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <!-- 카드 컨테이너 -->
                    <li class="p-2 rounded-lg shadow-md grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-4 cardComponent">
                        
                        <!-- 왼쪽: 텍스트/메타 -->
                        <div class="pl-2 order-2 sm:order-1">
                            <!-- 제목 -->
                            <a href="<?php the_permalink(); ?>" 
                               class="block font-semibold group hoveronlyText text-xl sm:text-2xl"> 
                                <?php the_title(); ?>
                                <!-- 기본적으로 보이지 않다가, group-hover 시 나타나도록 -->
                                <svg class="w-8 h-8 sm:w-10 sm:h-10 inline-block transition-all opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 duration-100 fill-current -mt-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                                    <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                                </svg>
                            </a>

                            <!-- Description 메타데이터 표시 -->
                            <div class="ml-2 text-md sm:text-lg">
                                <p>
                                    <?php
                                    // 'description' 메타데이터 가져오기
                                    $description = get_post_meta( get_the_ID(), 'description', true );
                                    if ( ! empty( $description ) ) {
                                        echo esc_html( $description );
                                    } else {
                                        echo '';
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
                                    <!-- 태그 아이콘 -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="m240-160 40-160H120l20-80h160l40-160H180l20-80h160l40-160h80l-40 160h160l40-160h80l-40 160h160l-20 80H660l-40 160h160l-20 80H600l-40 160h-80l40-160H360l-40 160h-80Zm140-240h160l40-160H420l-40 160Z"/></svg>
                                </div>
                                <div>
                                    <?php
                                    $tags = get_the_tags(); // 현재 글의 태그 배열 가져오기
                                    if ($tags) :
                                        echo '<div class="flex flex-wrap items-center">';
                                        foreach ($tags as $index => $tag) :
                                            $tag_link = get_tag_link($tag->term_id);
                                            // 첫 번째 버튼이 아니라면, 앞에 슬래시를 표시
                                            if ($index > 0) {
                                                echo '<span class="mx-0">/</span>';
                                            }
                                            ?>
                                            <a href="<?php echo esc_url($tag_link); ?>" 
                                               class="btn btn-ghost rounded-lg px-1 h-7 sm:h-8 lg:h-9 hoveronlyButton">
                                                <?php echo esc_html($tag->name); ?>
                                            </a>
                                            <?php
                                        endforeach;
                                        echo '</div>';
                                    else:
                                        echo '<span class="grayTextThings ml-1">태그 없음</span>';
                                    endif;
                                    ?>
                                </div>
                            </div>

                            <!-- 글자수(공백 제외), 읽기 시간(200WPM 기준) -->
                            <?php
                            $content_raw       = get_the_content(null, false);
                            $content_stripped  = wp_strip_all_tags($content_raw);
                            $content_no_spaces = preg_replace('/\s+/', '', $content_stripped);
                            $char_count        = mb_strlen($content_no_spaces, 'UTF-8');
                            // 영어 단어 수
                            $word_count   = str_word_count($content_stripped);
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
                            <!-- 썸네일 없을 때 -->
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

            <!-- 페이지네이션 -->
            <?php custom_two_skip_pagination($query); ?>
            <?php wp_reset_postdata(); ?>

        <?php else : ?>
            <p class="text-center p-4 cardComponent rounded-xl">이 카테고리에 글이 없습니다.</p>
        <?php endif; ?>
    </main>
</div>

<?php 
get_template_part('footer-navigation');
get_template_part('footer-scroll');
get_footer(); ?>
