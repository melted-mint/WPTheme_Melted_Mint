<?php
/**
 * Template Name: Home
 */

get_header();
?>

<!-- 최대 폭/여백 설정 -->
<div class="max-w-[80rem] mx-auto lg:px-4 py-8">

    <!-- 1) 두 칸(Grid)으로 Blog / Community -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        
        <!-- 왼쪽: Blog 영역 -->
        <div class="flex flex-col gap-4">
            <!-- 상단 박스 (클릭 시 /blog 이동) -->
            <a href="<?php echo site_url('/blog'); ?>"
               class="cardComponent text-center text-xl sm:text-2xl font-bold flex items-center justify-center h-12 sm:h-18 w-65 sm:w-80 mx-auto rounded-lg">
               Recent Blog Posts
            </a>
            
            <!-- Blog 글 5개 불러오기 -->
            <?php
            $args_blog = array(
                'posts_per_page' => 5,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'category_name'  => 'blog',  // 실제 카테고리 슬러그/ID로 변경
            );
            $blog_query = new WP_Query($args_blog);

            if ( $blog_query->have_posts() ) :
                // 임시로 메인 쿼리 백업
                $temp_wp_query = $wp_query;
                // 전역 $wp_query 교체
                $wp_query = $blog_query;

                // 기존 loop.php 재활용
                get_template_part('templates/blog/loop');

                // 원래 쿼리로 복원
                $wp_query = $temp_wp_query;
                wp_reset_postdata();
            else :
                echo '<p class="sm:mx-4 lg:mx-0 cardComponent text-center text-xl sm:text-2xl font-bold flex items-center justify-center h-14 sm:h-18 rounded-lg">No Blog Posts T . T</p>';
            endif;
            ?>
        </div>

        <!-- 오른쪽: Community 영역 -->
        <div class="flex flex-col gap-4">
            <!-- 상단 박스 (클릭 시 /community 이동) -->
            <a href="<?php echo site_url('/community'); ?>"
               class="cardComponent text-center text-xl sm:text-2xl font-bold flex items-center justify-center h-12 sm:h-18 w-65 sm:w-80 mx-auto rounded-lg">
               Recent Community Posts
            </a>

            <!-- Community 글 5개 불러오기 -->
            <?php
            $args_community = array(
                'posts_per_page' => 5,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'category_name'  => 'community', // 실제 카테고리 슬러그/ID로 변경
            );
            $community_query = new WP_Query($args_community);

            if ( $community_query->have_posts() ) :
                // 임시로 메인 쿼리 백업
                $temp_wp_query = $wp_query;
                // 전역 $wp_query 교체
                $wp_query = $community_query;

                // 기존 loop.php 재활용
                get_template_part('templates/blog/loop');

                // 원래 쿼리로 복원
                $wp_query = $temp_wp_query;
                wp_reset_postdata();
            else :
                echo '<p class="sm:mx-4 lg:mx-0 cardComponent text-center text-xl sm:text-2xl font-bold flex items-center justify-center h-14 sm:h-18 rounded-lg">No Community Posts T . T</p>';
            endif;
            ?>
        </div>

    </div><!-- .grid -->

</div><!-- .max-w -->

<?php
get_footer();
?>