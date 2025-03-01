<?php
/**
 * sidebar-left-category.php
 * 
 * - 'blog'라는 slug의 카테고리를 최상위로 삼고,
 * - 그 하위 카테고리들을 계층 구조로 표시.
 */

// 1) 'blog' slug의 카테고리 ID 가져오기
$blog_cat = get_category_by_slug('blog');
$blog_cat_id = $blog_cat ? $blog_cat->term_id : 0;

// 2) 블로그 카테고리의 하위 카테고리(모든 후손) 가져오기
$blog_categories = get_categories(array(
    'child_of'   => $blog_cat_id, // blog 카테고리 아래 전체
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
    // 'hierarchical' => false, 
    // 필요에 따라 true/false 설정 가능 (get_categories()가 서브카테고리까지 모두 가져옴)
));

// 3) 카테고리를 트리 형태로 출력하는 재귀 함수
if ( ! function_exists('render_category_tree') ) {
    function render_category_tree($parent_id, $all_categories) {
        // $parent_id를 부모로 하는 카테고리 목록 필터링
        $children = array_filter($all_categories, function($cat) use ($parent_id) {
            return $cat->parent == $parent_id;
        });

        // 자식이 없으면 함수 종료
        if ( empty($children) ) return;

        echo '<ul class="w-full ml-4 border-l border-gray-200 pl-2">'; 
        // ↑ 들여쓰기/디자인을 위해 margin-left나 border 등을 추가 (원하시는 대로 수정 가능)

        foreach ($children as $child) {
            // 카테고리 링크/카운트 UI
            echo '<li class="w-full hoveronlyButton rounded-lg h-10 mb-1">';

            echo '<a href="' . get_category_link($child->term_id) . '" 
                     class="w-full flex justify-between items-center ml-2">';
            // 왼쪽: 카테고리명
            echo '<span class="mt-1 text-sm">' . esc_html($child->name) . '</span>';
            // 오른쪽: 게시물 개수
            echo '<span class="mt-1.5 btn btn-ghost btn-disabled counterButton rounded-lg px-2 h-7 mr-4">'
               . esc_html($child->count)
               . '</span>';
            echo '</a>';

            // 하위 카테고리가 더 있을 경우 재귀 호출
            render_category_tree($child->term_id, $all_categories);

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
        //    (blog_cat_id 자체도 보여주고 싶다면, blog_cat_id를 포함한 링크를 먼저 표시하고,
        //     그 아래에서 render_category_tree()를 호출하는 식으로 확장 가능합니다.)
        if ($blog_cat_id) {
            ?>
            <ul class="w-full">
                <!-- 상위 blog 카테고리를 자체적으로 표시하고 싶으면 여기에 넣을 수 있음 -->
                <!-- 지금 예시는 'blog'라는 상위 카테고리는 제외하고, 하위만 표시하는 형태 -->
            </ul>
            <?php
            // 하위 계층 재귀 표시
            render_category_tree($blog_cat_id, $blog_categories);
        } else {
            echo '<p class="text-sm text-gray-500 ml-2">No "blog" category found.</p>';
        }
        ?>
    </div>
</div>