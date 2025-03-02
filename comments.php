<!-- comments.php -->
<?php
// 보호된 글 체크
if ( post_password_required() ) {
    return;
}
?>
<div class="cardComponent px-4 rounded-lg">
    <div id="comments" class="mt-4">

        <?php if ( have_comments() ) : ?>
            <h2 class="text-xl font-semibold mb-4">
                <?php
                $comments_number = get_comments_number();
                echo $comments_number . '개의 댓글';
                ?>
            </h2>

            <ol class="comment-list space-y-4">
                <?php
                // 리스트 표시 (대댓글 콜백)
                wp_list_comments(array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 48,
                    'callback'    => 'my_custom_comment_callback',
                    'max_depth'   => 5,
                ));
                ?>
            </ol>

            <?php if ( get_comment_pages_count() > 1 && get_option('page_comments') ) : ?>
                <nav class="comment-navigation flex justify-between mt-4" role="navigation">
                    <div class="nav-previous"><?php previous_comments_link('← 이전 댓글'); ?></div>
                    <div class="nav-next"><?php next_comments_link('다음 댓글 →'); ?></div>
                </nav>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        // 댓글이 닫혀 있고, 댓글이 있는 경우
        if ( ! comments_open() && get_comments_number() ) {
            echo '<p class="text-gray-500">댓글이 닫혀 있습니다.</p>';
        }
        ?>

        <?php
        // 댓글 열려있다면 입력 폼
        if ( comments_open() ) :
            // 기존 comment_form() 호출
            comment_form(array(
                'title_reply'          => '댓글 쓰기',
                'label_submit'         => '등록',
                'comment_field'        => '
                    <p class="comment-form-comment mb-4">
                        <label for="comment" class="font-semibold mb-1 inline-block">댓글 내용</label>
                        <textarea id="comment" name="comment" class="w-full p-2 border rounded-md" rows="5" required></textarea>
                    </p>',
                'submit_button'        => '<button name="%1$s" type="%2$s" id="%3$s" class="availableButton hoveronlyButton text-white p-3 rounded-md">%4$s</button>',
                'class_submit'         => 'btn',
                'comment_notes_before' => '',
                'comment_notes_after'  => '',
                // 만약 nonce를 직접 사용하려면 'id_form' => 'commentform', 'nonce_field' => ...
            ));
        endif;
        ?>
    </div>
</div>
<!-- 예: comments.php 하단 -->
<script>
jQuery(document).ready(function($){
  // 1) 대댓글(자식) ul 기본 숨김(이미 CSS에서 display:none 처리했다면 생략 가능)
  // $('.comment-list ul.children').hide(); // CSS에서 했으면 생략

  // 2) depth=1 이상의 댓글에, 자식이 있으면 "펼치기" 버튼 삽입
  //    하지만 WP가 자동으로 <ul class="children"> 삽입하므로,
  //    "부모 li" 내부에 button을 넣어야 합니다.
  //    -> 쉽지 않은 점: callback 내부에서 해줘야 하거나,
  //       또는 DOM 스캐닝을 통해 자식이 있으면 동적으로 버튼을 삽입할 수도.
  
  // 여기서는 간단히 "ul.children"을 찾고, 그 '직계부모 li'에 toggle버튼 달기 예시
  $('.comment-list li:has(> ul.children)').each(function(){
    // this = 부모 li
    let $parentLi = $(this);
    // 자식 ul
    let $childUl = $parentLi.children('ul.children');

    // 펼치기 버튼 삽입 (맨 아래 예시)
    // 원하는 위치(예: .reply 아래)에 append하거나, 맨 끝에 붙이거나
    let $toggleBtn = $('<button class="toggle-children text-sm text-blue-500 underline">답글 펼치기</button>');
    // 버튼을 .reply 아래에 넣어본다 (댓글 구조에 따라 조정)
    $parentLi.find('.reply').append($toggleBtn);

    // 초기 상태: 숨김
    $childUl.hide();

    // 클릭 이벤트
    $toggleBtn.on('click', function(e){
      e.preventDefault();
      if ($childUl.is(':visible')) {
        // 접기
        $childUl.slideUp();
        $(this).text('답글 펼치기');
      } else {
        // 펼치기
        $childUl.slideDown();
        $(this).text('답글 접기');
      }
    });
  });
});
</script>