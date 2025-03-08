<?php
// 보호된 글이면 리턴
if ( post_password_required() ) {
    return;
}
?>
<div class="cardComponent px-4 py-4 bg-base-200 text-base-content rounded-lg">
    <div id="comments">
        <?php if ( have_comments() ) : ?>
            <h2 class="text-xl font-semibold mb-4">
                <?php
                $comments_number = get_comments_number();
                echo $comments_number . '개의 댓글';
                ?>
            </h2>

            <ol class="comment-list space-y-4">
                <?php
                // 댓글 리스트 (대댓글 콜백)
                wp_list_comments(array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 48,
                    'callback'    => 'my_custom_comment_callback',
                    'max_depth'   => 5,
                ));
                ?>
            </ol>

            <?php 
            // 댓글 페이지 네비게이션
            if ( get_comment_pages_count() > 1 && get_option('page_comments') ) : ?>
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
        // (A) 댓글 열려있으면 입력 폼
        if ( comments_open() ) :
            // comment_form()에 Summernote 적용
            comment_form(array(
                'title_reply'          => '댓글 쓰기',
                'label_submit'         => '등록',
                // (중요) textarea → Summernote로 교체
                'comment_field'        => '
                    <p class="comment-form-comment mb-4">
                        <label for="summernote-comment" class="font-semibold mb-1 inline-block">댓글 내용</label>
                        <textarea id="summernote-comment" name="comment"
                                  class="w-full p-2 border rounded-md"
                                  rows="5" required></textarea>
                    </p>
                    <script>
                    jQuery(document).ready(function($){
                        $("#summernote-comment").summernote({
                            height: 150,
                            lang: "ko-KR",
                            placeholder: "댓글을 입력하세요...",
                            toolbar: [
                                ["style", ["bold", "italic", "underline", "clear"]],
                            ],
                            callbacks: {
                                onInit: function() {
                                    jQuery(".note-editable").css({
                                        "background-color": "#dddddd",
                                        "color": "black"
                                    });
                                }
                            }
                        });
                    });
                    </script>
                ',
                'submit_button'        => '<button name="%1$s" type="%2$s" id="%3$s" class="btn btn-primary">%4$s</button>',
                'class_submit'         => 'btn',
                'comment_notes_before' => '',
                'comment_notes_after'  => '',
            ));
        endif;
        ?>
    </div>
</div>

<!-- (B) 대댓글 펼치기/접기 기능 (위와 동일 로직) -->
<script>
jQuery(document).ready(function($){
  // 자식 ul.children 숨기기 (CSS / JS 어느 쪽이든 가능)
  // $( ".comment-list ul.children" ).hide(); // 필요 시 해제

  // depth=1 이상의 댓글에서 자식 ul이 있으면 "펼치기" 버튼
  $(".comment-list li:has(> ul.children)").each(function(){
    let $parentLi = $(this);
    let $childUl = $parentLi.children("ul.children");

    let $toggleBtn = $('<button class="toggle-children ml-2 text-sm text-blue-500 underline">답글 펼치기</button>');
    $parentLi.find(".reply").append($toggleBtn);

    $childUl.hide();

    $toggleBtn.on("click", function(e){
      e.preventDefault();
      if ($childUl.is(":visible")) {
        $childUl.slideUp();
        $(this).text("답글 펼치기");
      } else {
        $childUl.slideDown();
        $(this).text("답글 접기");
      }
    });
  });
});
</script>