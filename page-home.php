<?php
/**
 * Template Name: Home
 *
 * - 블로그(blog)와 커뮤니티(community) 각 5개씩 최신글
 * - templates/blog/loop.php & templates/community/loop.php 재사용
 * - md 이하 → 1열(세로 스택), md 이상 → 2열(가로 배치)
 */

get_header();
?>

<div class="max-w-[100rem] mx-auto sm:px-4 py-4">

    <!-- 2칸 그리드: md 이하=1col, xl 이상=2col -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

        <!-- [A] Blog 섹션 -->
        <div class="flex flex-col gap-4">
            <!-- 섹션 제목(클릭 시 /blog 이동) -->
            <a href="<?php echo site_url('/blog'); ?>"
               class="cardComponent text-center text-xl sm:text-2xl font-bold
                      flex items-center justify-center h-12 sm:h-18 w-3/4 mx-auto rounded-lg">
                최근 블로그 글
            </a>

            <?php
            // 1) blog CPT에서 최신글 5개
            $args_blog = array(
                'post_type'      => 'blog',
                'posts_per_page' => 5,
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            $blog_query = new WP_Query($args_blog);

            if ( $blog_query->have_posts() ) :
                // 전역 쿼리 백업
                $temp_query = $wp_query;
                // 전역 $wp_query를 blog_query로 교체
                $wp_query = $blog_query;

                // templates/blog/loop.php 호출
                // loop.php 내부에서 "while (have_posts()) : the_post() ..."가 동작
                get_template_part('templates/blog/loop');

                // 원래 쿼리 복원
                $wp_query = $temp_query;
                wp_reset_postdata();
            else :
                echo '<p class="cardComponent text-center text-xl sm:text-2xl font-bold
                           flex items-center justify-center h-14 sm:h-18 rounded-lg">
                      아직 블로그가 없네요... T . T
                      </p>';
            endif;
            ?>

            <!-- "더보기" 버튼 -->
            <div class="flex justify-center text-lg mb-8 -mt-1 text-center">
                <a href="<?php echo site_url('/blog'); ?>"
                   class="btn btn-ghost hoveronlyButton rounded-lg cardComponent w-1/2 h-15">
                    블로그 페이지로 이동
                </a>
            </div>
        </div><!-- Blog 섹션 끝 -->

        <!-- [B] Community 섹션 -->
        <div class="flex flex-col gap-4">
            <a href="<?php echo site_url('/community'); ?>"
               class="cardComponent text-center text-xl sm:text-2xl font-bold
                      flex items-center justify-center h-12 sm:h-18 w-3/4 md:120 mx-auto rounded-lg">
                최근 커뮤니티 글
            </a>

            <?php
            // 2) community CPT에서 최신글 5개
            $args_community = array(
                'post_type'      => 'community',
                'posts_per_page' => 5,
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            $community_query = new WP_Query($args_community);

            if ( $community_query->have_posts() ) :
                $temp_query = $wp_query;
                $wp_query = $community_query;

                // templates/community/loop.php 호출
                get_template_part('templates/community/loop');

                $wp_query = $temp_query;
                wp_reset_postdata();
            else :
                echo '<p class="cardComponent text-center text-xl sm:text-2xl font-bold
                           flex items-center justify-center h-14 sm:h-18 rounded-lg">
                      아직 커뮤니티 글이 없네요... T . T
                      </p>';
            endif;
            ?>

            <!-- "더보기" 버튼 -->
            <div class="flex justify-center mt-4">
                <a href="<?php echo site_url('/community'); ?>"
                   class="btn btn-ghost hoveronlyButton rounded-lg cardComponent w-1/2 h-15">
                    커뮤니티 글로 이동
                </a>
            </div>
        </div><!-- Community 섹션 끝 -->

    </div><!-- grid -->

</div><!-- max-w -->

<?php
get_template_part('footer-navigation');
get_template_part('footer-scroll');
get_footer();