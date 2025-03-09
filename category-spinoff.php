<?php
/**
 * category-spinoff.php
 * 
 * 'spinoff' 페이지 내부에서,
 * 선택한 카테고리(queried_object_id)를 기반으로
 * spinoff CPT 글만 페이지네이션으로 보여주는 템플릿 예시
 */

/** 
 * 1) 현재 카테고리 객체/ID 가져오기 
 */
$cat_id = get_queried_object_id();       // 현재 카테고리의 term_id
$cat_obj = get_category($cat_id);
$cat_name = ($cat_obj) ? $cat_obj->name : '';

/**
 * 2) WP_Query: spinoff CPT + 현재 카테고리
 */
$paged = max(1, get_query_var('paged'));
$args = array(
    'post_type'      => 'spinoff',  // spinoff CPT
    'cat'            => $cat_id,    // 현재 카테고리 ID
    'posts_per_page' => 10,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
);
$query = new WP_Query($args);

/** 
 * 3) (선택) 페이지네이션 함수 
 * - 이미 functions.php 등에 있으면 중복 선언 없이 그대로 사용 
 */
if (!function_exists('custom_two_skip_pagination')) {
    function custom_two_skip_pagination($wp_query = null) {
        if ($wp_query === null) {
            global $wp_query;
        }
        $paged       = max(1, get_query_var('paged'));
        $total_pages = $wp_query->max_num_pages;
        if ($total_pages <= 1) return;

        echo '<div class="pagination-wrapper">';
        // 예: 기본형 paginate_links() 혹은 이전에 쓰던 버튼형 코드를 넣으시면 됩니다.
        echo paginate_links(array(
            'total'   => $total_pages,
            'current' => $paged,
        ));
        echo '</div>';
    }
}
?>

<div class="max-w-[80rem] mx-auto py-4 px-4">
    <!-- 4) 상단: 카테고리명 등 헤더 표시 -->
    <div class="mb-4">
        <h2 class="text-2xl font-semibold">
            <?php echo esc_html($cat_name); ?> > 스핀오프
        </h2>
    </div>

    <!-- 5) 게시물 루프 -->
    <?php if ($query->have_posts()) : ?>
        <ul class="space-y-4">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <li class="p-2 rounded-lg shadow-md grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-4 cardComponent">
                    
                    <<!-- 왼쪽: 텍스트/메타 -->
                    <div class="pl-2 order-2 sm:order-1">
                        <!-- 제목 -->
                        <a href="<?php the_permalink(); ?>" 
                            class="block font-semibold group hoveronlyText text-xl sm:text-2xl"> 
                            <?php the_title(); ?>
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 inline-block transition-all opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 duration-100 fill-current -mt-2" viewBox="0 -960 960 960">
                                <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                            </svg>
                        </a>

                        <!-- 예: description 메타 -->
                        <div class="ml-2 text-md sm:text-lg">
                            <?php 
                                $desc = get_post_meta(get_the_ID(), 'description', true);
                                if ($desc) {
                                    echo esc_html($desc);
                                }
                            ?>
                        </div>

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
                        <div>
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
                    </div>

                    <!-- 썸네일 -->
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
                        <div class="hidden sm:block relative group overflow-hidden rounded order-1 sm:order-2">
                            <div class="sm:w-24 sm:h-full">
                                <a href="<?php the_permalink(); ?>" class="btn btn-ghost rounded-lg tagButton w-full sm:h-full sm:flex items-center justify-center text-base-content">
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

        <!-- 6) 페이지네이션 -->
        <?php custom_two_skip_pagination($query); ?>
        <?php wp_reset_postdata(); ?>

    <?php else : ?>
        <p class="text-center p-4 cardComponent rounded-xl">
            이 카테고리에 스핀오프 글이 없습니다.
        </p>
    <?php endif; ?>
</div>