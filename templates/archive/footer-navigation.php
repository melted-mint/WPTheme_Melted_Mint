<!-- 메인 네비게이션 바 래퍼 (가운데 정렬) -->
<div class="fixed bottom-4 h-10 sm:h-11 lg:h-14 w-full flex justify-center z-50">
    <!-- 실제 네비게이션 영역 -->
    <nav class="cardComponent shadow-lg rounded-xl inline-flex flex-nowrap items-center justify-center -py-1 sm:py-0 lg:py-1 px-4 space-x-2 sm:space-x-3 lg:space-x-4">
        <!-- Home -->
        <a href="<?php echo home_url('/home'); ?>" class="btn btn-ghost tagButton availableButton p-2 h-8 lg:h-10 rounded-xl text-sm sm:text-lg lg:text-xl">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                <path stroke-width="2" d="M12.3911 4.26185C11.986 3.84943 11.3167 3.86534 10.9317 4.29654L4.75406 11.2155C4.59044 11.3987 4.5 11.6358 4.5 11.8815V19.5C4.5 20.0523 4.94772 20.5 5.5 20.5H8.5C9.05228 20.5 9.5 20.0523 9.5 19.5V16C9.5 15.4477 9.94772 15 10.5 15H13.5C14.0523 15 14.5 15.4477 14.5 16V19.5C14.5 20.0523 14.9477 20.5 15.5 20.5H18.5C19.0523 20.5 19.5 20.0523 19.5 19.5V11.909C19.5 11.6469 19.3971 11.3953 19.2134 11.2083L12.3911 4.26185Z"/>
            </svg>
        </a>

        <!-- Blog -->
        <a href="<?php echo home_url('/blog'); ?>" class="btn tagButton btn-ghost availableButton p-2 h-8 lg:h-10 rounded-xl text-sm sm:text-lg lg:text-xl">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor">
                <path d="M200-200v-560 179-19 400Zm80-240h221q2-22 10-42t20-38H280v80Zm0 160h157q17-20 39-32.5t46-20.5q-4-6-7-13t-5-14H280v80Zm0-320h400v-80H280v80Zm-80 480q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v258q-14-26-34-46t-46-33v-179H200v560h202q-1 6-1.5 12t-.5 12v56H200Zm480-200q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29ZM480-120v-56q0-24 12.5-44.5T528-250q36-15 74.5-22.5T680-280q39 0 77.5 7.5T832-250q23 9 35.5 29.5T880-176v56H480Z"/>
            </svg>
        </a>
        
        <!-- Community -->
        <a href="<?php echo home_url('/community'); ?>" class="btn tagButton btn-ghost availableButton p-2 h-8 lg:h-10 rounded-xl text-sm sm:text-lg lg:text-xl">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor">
                <path d="M240-400h320v-80H240v80Zm0-120h480v-80H240v80Zm0-120h480v-80H240v80ZM80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z"/>
            </svg>
        </a>
        
        <!-- About -->
        <a href="<?php echo home_url('/about'); ?>" class="btn btn-ghost tagButton availableButton p-2 h-8 lg:h-10 rounded-xl text-sm sm:text-lg lg:text-xl">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor">
                <path d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
            </svg>
            소개
        </a>
        
        <!-- Post (page-post.php) -->
        <?php if ( is_user_logged_in() ) : ?>
            <!-- 로그인 상태일 때 -->
            <a href="<?php echo home_url('/post'); ?>" class="btn btn-ghost tagButton availableButton p-2 h-8 lg:h-10 rounded-xl text-sm sm:text-lg lg:text-xl">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor">
                    <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h360v80H200v560h560v-360h80v360q0 33-23.5 56.5T760-120H200Zm120-160v-80h320v80H320Zm0-120v-80h320v80H320Zm0-120v-80h320v80H320Zm360-80v-80h-80v-80h80v-80h80v80h80v80h-80v80h-80Z"/>
                </svg>
                글쓰기
            </a>
        <!-- Not Yet ! ! !
        <php else : ?>
            비로그인 상태일 때: 로그인 후 page-post.php로 리다이렉트
            <a href="<php echo wp_login_url( home_url('/page-post.php') ); ?>" 
               class="btn btn-ghost availableButton p-3 rounded-full">
                ✏️ Post
            </a>
        -->
        <?php endif; ?>
    </nav>
</div>