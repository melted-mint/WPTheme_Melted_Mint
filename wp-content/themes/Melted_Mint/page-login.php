<?php
/*
Template Name: Custom Login
*/
get_header();

// 로그인된 사용자는 홈으로 리디렉트
if (is_user_logged_in()) {
    wp_redirect(home_url('/'));
    exit;
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wp_login'])) {
    $username = sanitize_user($_POST['username']);
    $password = sanitize_text_field($_POST['password']);

    $credentials = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => isset($_POST['remember']) ? true : false
    );

    $user = wp_signon($credentials, false);

    if (is_wp_error($user)) {
        $login_error = '⚠️ 로그인 실패: 아이디 또는 비밀번호를 확인하세요.';
    } else {
        wp_redirect(home_url('/blog/')); // 로그인 후 이동할 페이지
        exit;
    }
}
?>

<div class="max-w-md mx-auto p-6 bg-base-100 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4">🔑 로그인</h2>

    <?php if (!empty($login_error)): ?>
        <p class="text-red-500"><?php echo esc_html($login_error); ?></p>
    <?php endif; ?>

    <form method="post">
        <label class="block mb-2">아이디</label>
        <input type="text" name="username" class="w-full p-2 border rounded-md" required>

        <label class="block mt-4 mb-2">비밀번호</label>
        <input type="password" name="password" class="w-full p-2 border rounded-md" required>

        <label class="inline-flex items-center mt-2">
            <input type="checkbox" name="remember" class="form-checkbox">
            <span class="ml-2">로그인 유지</span>
        </label>

        <button type="submit" name="wp_login" class="mt-4 w-full p-3 bg-primary text-white rounded-md">
            로그인
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="<?php echo wp_lostpassword_url(); ?>" class="text-blue-500">비밀번호 찾기</a> |
        <a href="<?php echo wp_registration_url(); ?>" class="text-blue-500">회원가입</a>
    </div>
</div>

<?php get_footer(); ?>