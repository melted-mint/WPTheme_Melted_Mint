<?php
// 전체 태그(혹은 특정 조건) 불러오기
$all_tags = get_tags(array(
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC'
));
?>

<div class="flex flex-col">
    <div class="flex py-2">
        <h2 class="title-container text-md font-bold">
            <span class="smallBoxComponent mr-2 rounded-full h-4">&nbsp;</span>
            태그
        </h2>
    </div>
    <div class="flex ml-2">
        <ul class="flex flex-wrap gap-2">
            <?php foreach ($all_tags as $tag) : ?>
                <li>
                    <a href="<?php echo get_tag_link($tag->term_id); ?>" 
                    class="btn btn-ghost p-1 tagButton h-8">
                    <?php echo esc_html($tag->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>