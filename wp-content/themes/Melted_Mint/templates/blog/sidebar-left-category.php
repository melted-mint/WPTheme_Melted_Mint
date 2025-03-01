<?php
/**
 * sidebar-left-category.php
 * 
 * - 'blog'라는 slug의 카테고리를 최상위로 삼고,
 * - 그 하위 카테고리들을 계층 구조로 표시하되,
 * - 기존 CSS(hoveronlyButton, rounded-lg, h-10 등) 유지.
 */

// 1) 'blog' slug의 카테고리 ID 가져오기
$blog_cat = get_category_by_slug('blog');
$blog_cat_id = $blog_cat ? $blog_cat->term_id : 0;

// 2) 블로그 카테고리의 하위 카테고리(모든 후손) 가져오기
$blog_categories = get_categories(array(
    'child_of'   => $blog_cat_id,
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
));

// 3) 재귀 함수 (계층 구조)
if ( ! function_exists('render_blog_category_tree') ) {
    function render_blog_category_tree($parent_id, $all_categories) {
        // $parent_id를 부모로 하는 카테고리 필터
        $children = array_filter($all_categories, function($cat) use ($parent_id) {
            return $cat->parent == $parent_id;
        });

        // 자식이 없으면 종료
        if ( empty($children) ) return;

        // 들여쓰기(ml-2) 등으로 하위 카테고리 표시
        echo '<ul class="w-full ml-2">';

        foreach ($children as $child) {
            // li 요소 (기존 디자인: hoveronlyButton, rounded-lg, h-10)
            echo '<li class="w-full hoveronlyButton -ml-2 rounded-lg h-10">';

            // a 링크 (w-full flex justify-between 등)
            echo '<a href="' . esc_url( get_category_link($child->term_id) ) . '" 
                   class="w-full flex ml-2 justify-between items-center">';
            
            // 왼쪽: 카테고리명
            echo '<span class="mt-1 text-sm">' . esc_html($child->name) . '</span>';

            // 오른쪽: 게시물 개수
            echo '<span class="mt-1.5 btn btn-ghost btn-disabled counterButton rounded-lg px-2 h-7 mr-4">'
               . esc_html($child->count)
               . '</span>';

            echo '</a>';

            // 하위 카테고리 재귀
            render_blog_category_tree($child->term_id, $all_categories);

            echo '</li>';
        }

        echo '</ul>';
    }
}
?>

<div class="flex flex-col w-full">
    <div class="flex py-2">
        <h2 class="title-container text-md font-bold">
            <span class="smallBoxComponent mr-2 rounded-full">&nbsp;</span>
            Category
        </h2>
    </div>
    <div class="flex w-full">
        <?php
        // 4) 최상위 blog_cat_id 아래의 하위 트리를 표시
        if ($blog_cat_id) {
            // 최상위 <ul> (1개만)
            echo '<ul class="w-full">';
            // (원한다면 여기서 blog_cat_id 자체를 표시 가능, 현재는 생략)
            
            // 하위 계층 재귀 표시
            render_blog_category_tree($blog_cat_id, $blog_categories);

            echo '</ul>';
        } else {
            echo '<p class="ml-2">No Category...</p>';
        }
        ?>
    </div>
</div>