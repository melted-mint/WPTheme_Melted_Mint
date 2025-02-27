<?php
// 전체 태그(혹은 특정 조건) 불러오기
$all_tags = get_tags(array(
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC'
));
?>

<h2 class="title-container text-lg font-bold">
    <span class="smallBoxComponent mr-2 rounded-full">&nbsp;</span>
    태그
</h2>
<ul class="flex flex-wrap gap-2 mt-2">
    <?php foreach ($all_tags as $tag) : ?>
        <li>
            <a href="<?php echo get_tag_link($tag->term_id); ?>" 
               class="p-2 bg-base-100 rounded-md hover:bg-primary hover:text-white">
               #<?php echo esc_html($tag->name); ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>