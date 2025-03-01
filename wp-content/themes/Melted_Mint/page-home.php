<?php
/**
 * Template Name: Blog & Community
 * 
 * 블로그와 커뮤니티 섹션만 노출하고, 
 * 각 5개씩 최근 글을 보여준 뒤 "더보기" 링크로 전체 목록(/blog, /community)에 이동.
 */

get_header();
?>

<div class="max-w-[80rem] mx-auto sm:px-4 py-4">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- [1] Blog 섹션 -->
        <div class="flex flex-col gap-4">
            <!-- 제목 박스 (클릭 시 /blog 이동) -->
            <a href="<?php echo site_url('/blog'); ?>"
               class="cardComponent text-center text-xl sm:text-2xl font-bold
                      flex items-center justify-center h-12 sm:h-18 w-80 sm:w-100 mx-auto rounded-lg">
               Recent Blog Posts
            </a>

            <?php
            // Blog CPT에서 최근 글 5개
            $args_blog = array(
                'post_type'      => 'blog',   // 커스텀 포스트 타입
                'posts_per_page' => 5,
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            $blog_query = new WP_Query($args_blog);

            if ( $blog_query->have_posts() ) :
                while ( $blog_query->have_posts() ) :
                    $blog_query->the_post();
                    ?>
                    <!-- 간단한 목록 UI -->
                    <div class="cardComponent p-4">
                        <h3 class="text-lg font-bold mb-2">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <div class="text-sm mb-2">
                            <?php the_time('Y-m-d'); ?>
                        </div>
                        <div class="prose line-clamp-3">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p class="cardComponent text-center text-xl sm:text-2xl font-bold
                           flex items-center justify-center h-14 sm:h-18 rounded-lg">
                      No Blog Posts T . T
                      </p>';
            endif;
            ?>

            <!-- "더보기" 링크 -->
            <div class="flex justify-center mt-4">
                <a href="<?php echo site_url('/blog'); ?>"
                   class="btn btn-primary">
                    More Blog Posts →
                </a>
            </div>
        </div><!-- Blog 섹션 끝 -->

        <!-- [2] Community 섹션 -->
        <div class="flex flex-col gap-4">
            <!-- 제목 박스 (클릭 시 /community 이동) -->
            <a href="<?php echo site_url('/community'); ?>"
               class="cardComponent text-center text-xl sm:text-2xl font-bold
                      flex items-center justify-center h-12 sm:h-18 w-80 sm:w-100 mx-auto rounded-lg">
               Recent Community Posts
            </a>

            <?php
            // Community CPT에서 최근 글 5개
            $args_community = array(
                'post_type'      => 'community',
                'posts_per_page' => 5,
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            $community_query = new WP_Query($args_community);

            if ( $community_query->have_posts() ) :
                while ( $community_query->have_posts() ) :
                    $community_query->the_post();
                    ?>
                    <!-- 간단한 목록 UI -->
                    <div class="cardComponent p-4">
                        <h3 class="text-lg font-bold mb-2">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <div class="text-sm mb-2">
                            <?php the_time('Y-m-d'); ?>
                        </div>
                        <div class="prose line-clamp-3">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p class="cardComponent text-center text-xl sm:text-2xl font-bold
                           flex items-center justify-center h-14 sm:h-18 rounded-lg">
                      No Community Posts T . T
                      </p>';
            endif;
            ?>

            <!-- "더보기" 링크 -->
            <div class="flex justify-center mt-4">
                <a href="<?php echo site_url('/community'); ?>"
                   class="btn btn-primary">
                    More Community Posts →
                </a>
            </div>
        </div><!-- Community 섹션 끝 -->

    </div><!-- grid -->

</div><!-- max-w -->

<?php
get_template_part('footer-navigation');
get_template_part('footer-scroll');
get_footer();