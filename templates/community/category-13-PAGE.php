<div class="grid grid-cols-12 gap-4 p-4 max-w-[85rem] mx-auto">
    <!-- 좌측 Sidebar (Blog 카테고리 목록) -->
    <aside class="col-span-3 bg-base-200 shadow-lg rounded-lg p-4 min-h-[5rem] hidden md:block">
        <?php get_template_part('templates/blog/sidebar-left'); ?>
    </aside>

    <!-- 메인 콘텐츠 영역 -->
    <main class="col-span-6 p-4">
        <h1 class="text-2xl font-bold mb-4">📖 <?php single_cat_title(); ?></h1>
        <?php get_template_part('templates/blog/loop'); ?>
    </main>

    <!-- 우측 Sidebar (태그 목록 등 추가) -->
    <aside class="col-span-3 bg-base-200 shadow-lg rounded-lg p-4 min-h-screen hidden md:block">
        <?php get_template_part('templates/blog/sidebar-right'); ?>
    </aside>
</div>