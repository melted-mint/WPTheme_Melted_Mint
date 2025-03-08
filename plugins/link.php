<?php
/**
 * link.php (통합 버전)
 * 
 * "년/월/일/시/분/ID/슬러그" 구조를
 * blog, novel, spinoff, community 등 여러 CPT에 일괄 적용
 */

// 1) 적용 대상 CPT 배열
$my_cpts = array( 'blog', 'novel', 'spinoff', 'community' );

/**
 * 2) CPT별 커스텀 퍼머링크 & 리라이트 등록
 *    - 아래 함수가 각 CPT에 대한 post_type_link, rewrite_rule을 설정
 */
function register_custom_permalink_rewrites( $cpt_list ) {
    // (A) post_type_link 필터: 링크 생성 로직
    add_filter('post_type_link', function( $permalink, $post ) use ( $cpt_list ) {

        // 대상 CPT가 아닌 경우에는 그대로 반환
        if ( ! in_array( $post->post_type, $cpt_list, true ) ) {
            return $permalink;
        }

        // 연·월·일·시·분
        $year    = get_the_time('Y', $post);
        $month   = get_the_time('m', $post);
        $day     = get_the_time('d', $post);
        $hour    = get_the_time('H', $post);
        $minute  = get_the_time('i', $post);

        $pid     = $post->ID;
        // slug에서 -2, -3 등 숫자 접미사 제거
        $slug    = preg_replace('/-\d+$/', '', $post->post_name);

        // 최종 URL: /{post_type}/{yyyy}/{mm}/{dd}/{HH}/{ii}/{ID}/{slug}/
        // 예: /blog/2025/03/02/12/35/123/mytitle/
        return home_url( sprintf(
            '/%s/%s/%s/%s/%s/%s/%d/%s/',
            $post->post_type,
            $year,
            $month,
            $day,
            $hour,
            $minute,
            $pid,
            $slug
        ) );
    }, 10, 2);

    // (B) rewrite_rule 등록: 실제 URL → WP 쿼리 매핑
    add_action('init', function() use ( $cpt_list ) {
        foreach ( $cpt_list as $cpt ) {
            add_rewrite_rule(
                '^' . $cpt . '/(\d{4})/(\d{2})/(\d{2})/(\d{2})/(\d{2})/(\d+)/([^/]+)/?$',
                'index.php?post_type=' . $cpt . '&p=$matches[6]',
                'top'
            );
        }
    });
}

// 3) 실제로 적용
register_custom_permalink_rewrites( $my_cpts );

/**
 * 4) 주의: .htaccess(Rewrite rules) 새로고침
 *    - 관리자 -> 설정 -> 고유주소 -> "변경사항 저장" 클릭
 *      (혹은 플러그인/코드로 flush_rewrite_rules() 수행)
 */