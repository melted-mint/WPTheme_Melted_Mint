<?php
$blog_cat = get_category_by_slug('blog');
$blog_cat_id = $blog_cat ? $blog_cat->term_id : 0;

$blog_categories = get_categories(array(
    'child_of'   => $blog_cat_id,
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC'
));
?>
<div class="flex flex-col w-full">
    <div class="flex py-2">
        <h2 class="title-container text-md font-bold">
            <span class="smallBoxComponent mr-2 rounded-full">&nbsp;</span>
            Category
        </h2>
    </div>
    <div class="flex w-full">
        <ul class="w-full">
            <?php foreach ($blog_categories as $category): ?>
                <li class="w-full hoveronlyButton rounded-lg h-10">
                    <a href="<?php echo get_category_link($category->term_id); ?>" 
                    class="w-full flex justify-between items-center ml-2">
                        <!-- 왼쪽: 카테고리명 -->
                        <span class="mt-1 text-sm"><?php echo esc_html($category->name); ?></span>

                        <!-- 오른쪽: 게시물 개수 -->
                        <span class="mt-1.5 btn btn-ghost btn-disabled counterButton rounded-lg px-2 h-7 mr-4">
                            <?php echo esc_html($category->count); ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>