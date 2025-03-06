<?php
// 보호된 페이지, 혹은 단일 글이 아닐 때는 TOC 생략할 수도 있음
if ( is_singular() && ! post_password_required() ) {
    // 1) 본문 파싱
    global $post;
    // raw content (단, the_content 필터 전/후 상태에 따라 조정 가능)
    $content = get_post_field('post_content', $post->ID);

    // (A) heading 정규식으로 찾기 (h1~h3; 더 확장 가능)
    //  ※ 주의: 실제 태그에 클래스나 속성이 있으면 더욱 복잡해질 수 있으니 상황에 맞게 수정
    $pattern = '/<h([1-3])[^>]*>(.*?)<\/h\1>/i';

    if ( preg_match_all($pattern, $content, $matches, PREG_SET_ORDER) ) {
        echo '<div class="mb-4 p-3 bg-base-200 rounded-md">';
        echo '<h2 class="text-lg font-bold mb-2">목차</h2>';
        echo '<ul class="space-y-1 list-inside">';
        
        // (B) 각 heading에 임의 id를 부여하기 위해 content 수정 필요 (자동주입 or 수동)
        // 여기서는 단순히, heading 텍스트를 slug화하여 id로 쓴 예시
        foreach ( $matches as $m ) {
            $level = $m[1];  // 1, 2, 3
            $heading_text = wp_strip_all_tags($m[2]); // <strong> 등 제거
            // 슬러그화
            $id_slug = sanitize_title($heading_text);

            // 목차 목록 (들여쓰기: level에 따라 padding-left, 등)
            $indent = (int)($level) - 1; // h1=0, h2=1, h3=2
            $padding = 10 * $indent; // 간단 예

            echo '<li style="padding-left:' . $padding . 'px;">';
            echo '<a href="#' . $id_slug . '" class="hover:underline">';
            echo esc_html($heading_text);
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}
?>

<!-- 최근 "읽은" 글 목록 (쿠키) -->
<div class="mb-4">
    <h2 class="text-lg font-bold">최근에 본 글</h2>
    <ul class="space-y-2">
        <?php
        // 쿠키에서 recently_viewed 읽기
        if ( isset($_COOKIE['recently_viewed']) && ! empty($_COOKIE['recently_viewed']) ) {
            $viewed_ids = explode(',', $_COOKIE['recently_viewed']);

            foreach ( $viewed_ids as $vid ) {
                $vid = intval($vid);
                if ( $vid > 0 ) {
                    $title = get_the_title($vid);
                    $permalink = get_permalink($vid);
                    if ( $title && $permalink ) {
                        ?>
                        <li>
                            <a href="<?php echo esc_url($permalink); ?>" 
                               class="block p-2 bg-base-100 rounded-md hover:bg-primary hover:text-white">
                               <?php echo esc_html($title); ?>
                            </a>
                        </li>
                        <?php
                    }
                }
            }
        } else {
            echo '<li class="text-sm text-gray-500">최근 본 글이 없습니다.</li>';
        }
        ?>
    </ul>
</div>

<!-- 기존 '최근 글' (발행순) 섹션 그대로 두고 싶으면 아래 -->
<div class="mb-4">
    <h2 class="text-lg font-bold">📰 최근 발행 글</h2>
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
</div>