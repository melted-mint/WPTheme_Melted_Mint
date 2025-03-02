<?php
// 상위 '소설' 카테고리 객체와 ID를 가져옴
$novel_cat = get_category_by_slug('소설');
$novel_cat_id = $novel_cat ? $novel_cat->term_id : 0;

// '소설' 하위의 모든 카테고리 (hide_empty는 false로 처리 후 개별 체크)
$novel_categories = get_categories(array(
    'child_of'   => $novel_cat_id,
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC'
));

/**
 * 해당 카테고리에 'novel' 포스트 타입의 글이 1개 이상 있는지 확인
 */
function has_novel_posts_in_cat($cat_id) {
    $query = new WP_Query(array(
        'post_type'      => 'novel',  // 커스텀 포스트 타입
        'cat'            => $cat_id, // 해당 카테고리
        'posts_per_page' => 1,       // 1개만 확인하면 됨
        'fields'         => 'ids',
    ));
    return $query->have_posts();
}
?>

<div class="flex flex-col w-full">
    <div class="flex py-2">
        <h2 class="title-container text-md font-bold">
            <span class="smallBoxComponent mr-2 rounded-full">&nbsp;</span>
            카테고리
        </h2>
    </div>
    <div class="flex w-full">
        <ul class="w-full">
            <?php foreach ($novel_categories as $category): ?>
                <?php
                // novel 포스트 타입 글이 없는 카테고리는 출력하지 않음
                if ( ! has_novel_posts_in_cat($category->term_id) ) {
                    continue;
                }
                ?>
                <li class="w-full hoveronlyButton rounded-lg h-10">
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                       class="w-full flex justify-between items-center ml-2">
                        <!-- 왼쪽: 카테고리명 -->
                        <span class="mt-1 text-sm"><?php echo esc_html($category->name); ?></span>
                        <!-- 오른쪽: 전체 게시물 개수 (참고: category->count는 기본적으로 모든 포스트 타입을 포함) -->
                        <span class="mt-1.5 btn btn-ghost btn-disabled counterButton rounded-lg px-2 h-7 mr-4">
                            <?php echo esc_html($category->count); ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>