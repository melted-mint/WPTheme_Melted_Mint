<?php
// single.php

get_header();

$current_slug = get_post_field( 'post_name', get_queried_object_id() );

switch ( $current_slug ) {
    case 'blog':
        get_template_part( 'templates/blog/single-blog' );
        break;
    case 'novel':
        get_template_part( 'templates/novel/single-novel' );
        break;
    case 'spinoff':
        get_template_part( 'templates/spinoff/single-spinoff' );
        break;
    // ... 나머지 분기
    default:
        get_template_part( 'templates/default/single-default' );
        break;
}

get_footer();