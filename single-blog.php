<?php
// single.php
get_header(); // 헤더 불러오기

if ( have_posts() ) :
    while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <!-- 원하는 디자인 (글 제목, 메타 정보, 내용, 썸네일, 댓글 등) -->
            <h1><?php the_title(); ?></h1>
            <div class="meta">
                <span><?php the_date(); ?></span>
                <span><?php the_author(); ?></span>
                <!-- ... -->
            </div>

            <div class="content">
                <?php the_content(); ?>
            </div>
        </article>

    <?php endwhile;
else :
    echo '<p>글이 없습니다.</p>';
endif;
?>
<main></main>
<?php
get_footer(); // 푸터 불러오기
?>