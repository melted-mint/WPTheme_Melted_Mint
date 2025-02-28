<?php if ( is_user_logged_in() ): ?>
    <!-- 바닥 네비게이션 (로그인 상태) -->
    <nav 
        class="cardComponent fixed bottom-4 left-1/2 min-w-[26rem] transform -translate-x-1/2
               shadow-lg rounded-full flex items-center 
               py-2 px-6 space-x-6 z-50">
        <!-- 홈 -->
        <a href="<?php echo home_url(); ?>" class="text-lg">
            🏠 홈
        </a>
        <!-- 글쓰기 -->
        <a href="<?php echo home_url('/post/'); ?>" 
           class="btn btn-ghost btn-xl p-3 rounded-full">
            ✏️ 글쓰기
        </a>
        <!-- 로그아웃 -->
        <a href="<?php echo wp_logout_url(home_url()); ?>" class="text-lg">
            🚪 로그아웃
        </a>
    </nav>
<?php else: ?>
    <!-- 바닥 네비게이션 (비로그인 상태) -->
    <nav 
        class="cardComponent fixed bottom-4 left-1/2 transform -translate-x-1/2
               shadow-lg rounded-full flex items-center
               py-2 px-6 space-x-6 z-50">
        <!-- 홈 -->
        <a href="<?php echo home_url(); ?>" class="text-lg">
            🏠 홈
        </a>
        <!-- 로그인 -->
        <a href="<?php echo wp_login_url(); ?>" 
           class="btn btn-secondary p-3 rounded-full text-white">
            🔑 로그인
        </a>
    </nav>
<?php endif; ?>