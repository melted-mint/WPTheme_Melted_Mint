<?php if (is_user_logged_in()): ?>
    <!-- 바닥 네비게이션 -->
    <nav class="fixed bottom-0 left-0 w-full bg-base-200 shadow-lg flex justify-around items-center py-3 h-14">
        <a href="<?php echo home_url(); ?>" class="text-lg">🏠 홈</a>
        <?php if (is_user_logged_in()): ?>
            <a href="<?php echo home_url('/post/'); ?>" class="btn btn-primary p-3 rounded-full text-white">
                ✏️ 글쓰기
            </a>
        <?php endif; ?>
        <a href="<?php echo wp_logout_url(home_url()); ?>" class="text-lg">🚪 로그아웃</a>
    </nav>
<?php else: ?>
    <!-- 로그인 유도 -->
    <nav class="fixed bottom-0 left-0 w-full bg-base-200 shadow-lg flex justify-around items-center py-3">
        <a href="<?php echo home_url(); ?>" class="text-lg">🏠 홈</a>
        <a href="<?php echo wp_login_url(); ?>" class="btn btn-secondary p-3 rounded-full">🔑 로그인</a>
    </nav>
<?php endif; ?>
