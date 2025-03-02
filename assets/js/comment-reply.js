/**
 * 대댓글(스레드 댓글)을 위한 comment-reply.js 로드
 */
function yourtheme_enqueue_comment_reply_script() {
    // 조건: 단일 글이고, 댓글이 열려 있으며, 'thread_comments' 옵션이 활성화된 경우
    if ( is_singular() && comments_open() && get_option('thread_comments') ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'yourtheme_enqueue_comment_reply_script' );