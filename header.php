<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css">
</head>
<script>
    // IMPORTANT: set this in <HEAD> top before any other tag.
    const setTheme = (theme) => {
        theme ??= localStorage.theme || "light";
        document.documentElement.dataset.theme = theme;
        localStorage.theme = theme;
    };
    setTheme();
    // H U E ! ! !
    const setHue = (hue) => {
        hue ??= localStorage.hue || 165;
        document.documentElement.style.setProperty("--hue", hue);
        localStorage.hue = hue;
    }
    setHue();
</script>
<body <?php body_class(); ?>>
<header class="sm:px-4">
  <!-- 상단 네비게이션 바 -->
  <div class="navbar cardComponent max-w-[74rem] mx-auto rounded-b-2xl px-2 h-34 -mt-21 md:-mt-11">
    <div class="flex flex-col min-w-full -mb-18 md:-mb-13">
        <div class="flex flex-row -mb-2">
            <!-- 왼쪽: 홈(사이트 제목) -->
            <div class="navbar-start sm:w-fit specialButton">
                <a href="<?php echo home_url('/home'); ?>" class="btn btn-ghost rounded-lg text:md sm:text-lg lg:text-2xl">
                    <svg class="-ml-3 w-5 h-5 sm:w-7 sm:h-7 lg:w-9 lg:h-9" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                    <path stroke-width="2" d="M12.3911 4.26185C11.986 3.84943 11.3167 3.86534 10.9317 4.29654L4.75406 11.2155C4.59044 11.3987 4.5 11.6358 4.5 11.8815V19.5C4.5 20.0523 4.94772 20.5 5.5 20.5H8.5C9.05228 20.5 9.5 20.0523 9.5 19.5V16C9.5 15.4477 9.94772 15 10.5 15H13.5C14.0523 15 14.5 15.4477 14.5 16V19.5C14.5 20.0523 14.9477 20.5 15.5 20.5H18.5C19.0523 20.5 19.5 20.0523 19.5 19.5V11.909C19.5 11.6469 19.3971 11.3953 19.2134 11.2083L12.3911 4.26185Z"/>
                    </svg>
                    <div class="-mr-2">
                        <?php bloginfo('name'); ?>
                    </div>
                </a>
            </div>

            <!-- 중앙: 검색창 (sm 이상에서만 보이도록) -->
            <div class="navbar-center hidden sm:flex flex-1 px-2 mr-4">
            <form 
                role="search" 
                method="get" 
                action="<?php echo esc_url(home_url('/')); ?>" 
                class="form-control w-full"
            >
                <input
                type="text"
                name="s"
                placeholder="검색..."
                class="input input-bordered w-full cardComponent"
                value="<?php echo get_search_query(); ?>"
                />
            </form>
            </div>

            <!-- 오른쪽: 버튼/아이콘들 (gap-1 = 약 4px 간격) -->
            <div class="navbar-end flex items-center gap-1 sm:gap-2 md:gap-3 sm:w-fit">

            <!-- 돋보기 아이콘 (md 미만에서만 보여서 검색창 드롭다운) -->
            <div class="dropdown dropdown-end sm:hidden">
                <label tabindex="0" class="btn btn-sm sm:btn-md btn-ghost btn-circle">
                <!-- magnifying glass icon -->
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    class="h-5 w-5" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="2" 
                    d="M21 21l-4.873-4.873M11.125 4a7.125 7.125 0 100 14.25 7.125 7.125 0 000-14.25z" 
                    />
                </svg>
                </label>
                <!-- 드롭다운으로 검색창 표시 -->
                <ul 
                tabindex="0" 
                class="dropdown-content cardComponent rounded-xl mt-4 p-2 shadow md:z-0 w-screen -m-40"
                >
                <li>
                    <form 
                    role="search" 
                    method="get" 
                    action="<?php echo esc_url(home_url('/')); ?>" 
                    class="form-control"
                    >
                    <input
                        type="text"
                        name="s"
                        placeholder="검색..."
                        class="input input-bordered w-full"
                        value="<?php echo get_search_query(); ?>"
                    />
                    </form>
                </li>
                </ul>
            </div>
            <div class="dropdown dropdown-center">
                <label tabindex="0" class="btn btn-ghost btn-sm sm:btn-md btn-circle fill-current sm:-ml-3">
                    <svg
                        class="w-5 h-5 sm:w-7 sm:h-7 md:w-10 md:h-10"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 -960 960 960">
                        <path d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 32.5-156t88-127Q256-817 330-848.5T488-880q80 0 151 27.5t124.5 76q53.5 48.5 85 115T880-518q0 115-70 176.5T640-280h-74q-9 0-12.5 5t-3.5 11q0 12 15 34.5t15 51.5q0 50-27.5 74T480-80Zm0-400Zm-220 40q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm120-160q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm200 0q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm120 160q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17ZM480-160q9 0 14.5-5t5.5-13q0-14-15-33t-15-57q0-42 29-67t71-25h70q66 0 113-38.5T800-518q0-121-92.5-201.5T488-800q-136 0-232 93t-96 227q0 133 93.5 226.5T480-160Z"/>
                    </svg>
                </label>
                <!-- Hue 조정 컨텐츠 -->
                <div tabindex="0" class="dropdown-content z-50 mt-4 w-60 p-2 shadow-lg rounded-lg">
                    <!-- 상단 -->
                    <div class="flex items-center justify-between">
                        <!-- 왼쪽: 🎨 아이콘 + 제목 + 초기화 버튼 -->
                        <div class="flex items-center gap-2">
                            <span class="smallBoxComponent rounded-full h-4">&nbsp;</span>
                            <h2 class="text-md font-semibold">테마 색</h2>
                            <!-- reset button -->
                            <button id="resetHue" class="btn rounded-md btn-xs btn-circle buttonComponent btn-ghost">
                            <svg class="w-3 h-3" viewBox="0 0 18 18" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-width="2" d="M6 7L7 6L4.70711 3.70711L5.19868 3.21553C5.97697 2.43724 7.03256 2 8.13323 2C11.361 2 14 4.68015 14 7.93274C14 11.2589 11.3013 14 8 14C6.46292 14 4.92913 13.4144 3.75736 12.2426L2.34315 13.6569C3.90505 15.2188 5.95417 16 8 16C12.4307 16 16 12.3385 16 7.93274C16 3.60052 12.4903 0 8.13323 0C6.50213 0 4.93783 0.647954 3.78447 1.80132L3.29289 2.29289L1 0L0 1V7H6Z"/>
                            </svg>
                            </button>
                        </div>

                        <!-- 오른쪽: 현재 Hue 색상 -->
                        <div class="bg-primary buttonComponent text-sm font-bold px-2 py-1 rounded">
                            <span id="hueValue">165</span>
                        </div>
                    </div>

                    <!-- 하단 (슬라이더) -->
                    <div class="relative mt-2">
                        <input type="range" min="0" max="360" value="210"
                            class="hue-slider w-full rounded-lg"
                            id="hueSlider">
                    </div>
                </div>
            </div>
            <!-- 다크/라이트 모드 토글 -->
            <label class="swap swap-rotate btn-sm sm:btn-md btn btn-ghost btn-circle">
                <!-- this hidden checkbox controls the state -->
                <input type="checkbox" id="themeToggle"/>

                <!-- sun icon -->
                <svg
                    class="swap-on h-5 w-5 sm:h-7 sm:w-7 md:h-10 md:w-10 fill-current"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24">
                    <path
                    d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
                </svg>

                <!-- moon icon -->
                <svg
                    class="swap-off h-5 w-5 sm:h-7 sm:w-7 md:h-10 md:w-10 fill-current"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24">
                    <path
                    d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z" />
                </svg>
            </label>

            <?php
            $current_user = wp_get_current_user();
            $is_logged_in = is_user_logged_in();
            $is_admin = current_user_can('manage_options'); // 어드민 여부 확인
            ?>

            <!-- 아바타 드롭다운 -->
            <div class="dropdown dropdown-end">
                <label tabindex="0" role="button" class="btn btn-sm sm:btn-md btn-ghost btn-circle avatar">
                    <div class="w-6 sm:w-7 md:w-10 rounded-full">
                        <?php if ($is_logged_in): ?>
                            <!-- 로그인한 경우: 사용자 아바타 표시 -->
                            <?php echo get_avatar($current_user->ID, 40); ?>
                        <?php else: ?>
                            <!-- 로그인하지 않은 경우: 기본 이미지 -->
                            <img alt="Default Avatar" src="<?php echo get_template_directory_uri(); ?>/assets/images/default-avatar.png" />
                        <?php endif; ?>
                    </div>
                </label>

                <ul tabindex="0" class="menu menu-sm !w-[12rem] dropdown-content rounded-box z-[1] mt-4 p-2 shadows text-center">
                    <?php if ($is_logged_in): ?>
                        <!-- 로그인한 경우 -->
                        <li class="text-md sm:text-lg lg:text-xl my-0.5 mx-2"><?php echo esc_html($current_user->display_name); ?></li>
                        <li class="my-0.5"><a class="hoveronlyButton text-sm sm:text-md lg:text-lg" href="<?php echo site_url('/my-posts'); ?>">내가 쓴 글</a></li>
                        <li class="my-0.5"><a class="hoveronlyButton text-sm sm:text-md lg:text-lg" href="<?php echo esc_url(admin_url('profile.php')); ?>">사용자 설정</a></li>
                        <?php if ($is_admin): ?>
                            <li class="my-0.5"><a class="hoveronlyButton text-sm sm:text-md lg:text-lg" href="<?php echo esc_url(admin_url()); ?>">관리자 설정판</a></li>
                        <?php endif; ?>
                        <li class="my-0.5"><a class="hoveronlyButton text-sm sm:text-md lg:text-lg" href="<?php echo esc_url(wp_logout_url(home_url())); ?>">로그아웃</a></li>
                    <?php else: ?>
                        <!-- 로그인하지 않은 경우 -->
                        <li class="text-lg min-w-30 my-0.5 mx-2">익명</li>
                        <li class="my-0.5"><a class="hoveronlyButton text-sm sm:text-md lg:text-lg" href="<?php echo esc_url(wp_login_url()); ?>">로그인</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="dropdown dropdown-end pr-2">
                <!-- 햄버거 아이콘 (메뉴 토글) -->
                <label tabindex="0" role="button" class="btn btn-ghost btn-sm sm:btn-md btn-circle rounded-sm md:hidden">
                    <!-- hamburger icon -->
                    <svg
                    class="fill-current w-5 h-5 sm:w-7 sm:h-7"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 512 512">
                    <path d="M64,384H448V341.33H64Zm0-106.67H448V234.67H64ZM64,128v42.67H448V128Z" />
                    </svg>
                </label>
                <ul class="menu menu-compact dropdown-content mt-4 p-2 shadow rounded-box w-32">
                    <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'container'      => false,
                            'items_wrap'     => '%3$s' // <ul>을 감싸는 기본 구조를 제거하여 기존 <ul> 내부에 삽입
                        ));
                        wp_nav_menu(array(
                            'theme_location' => 'external',
                            'container'      => false,
                            'items_wrap'     => '%3$s' // 같은 <ul> 내부에 삽입
                        ));
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="md:flex md:flex-row hidden -my-2">
            <!-- 하단(아래쪽)에 쫙 펼쳐진 메뉴 (md 이상에서 보여줄지, 모바일 토글과 연동할지는 추가 설정) -->
            <!-- 네비게이션 바 -->
            <nav class="mt-3 max-w-3xl mx-auto flex items-center gap-0 py-1">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'menu menu-horizontal rounded-box rounded-r-none -mr-2 text-lg lg:text-xl -mt-1',
                    'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                ));
                wp_nav_menu(array(
                    'theme_location' => 'external',
                    'container'      => false,
                    'menu_class'     => 'menu menu-horizontal rounded-box rounded-l-none -ml-2 text-lg lg:text-xl -mt-1',
                    'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                ));
                ?>
            </nav>
        </div>
    </div>
  </div>
</header>