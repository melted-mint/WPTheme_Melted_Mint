<!-- 최근 글 목록 -->
<h2 class="text-lg font-bold mt-6">📰 최근 글</h2>
<ul class="space-y-2">
    <?php
    $recent_posts = wp_get_recent_posts(array(
        'numberposts' => 5,
        'post_status' => 'publish'
    ));
    foreach ($recent_posts as $post): ?>
        <li>
            <a href="<?php echo get_permalink($post['ID']); ?>" 
               class="block p-2 bg-base-100 rounded-md hover:bg-primary hover:text-white">
               <?php echo esc_html($post['post_title']); ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>