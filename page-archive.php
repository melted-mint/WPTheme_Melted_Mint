<?php get_header(); ?>

<?php 
// templates/blog/page-blog.php 파일을 불러오기
get_template_part('templates/archive/page-archive'); 
?>

<?php
get_template_part('templates/archive/footer-navigation');
?>

<?php
get_template_part('footer-scroll');
?>

<?php get_footer(); ?>