<?php
/**
 * Template Name: Split Timeline with Fixed Boxes
 *
 * 요구사항 정리:
 *   - 중앙 세로 라인
 *   - 좌측 상단 "블로그" 박스, 우측 상단 "커뮤니티" 박스는 position:fixed
 *   - 블로그 글 => 가운데 선 기준 왼쪽
 *   - 커뮤니티 글 => 가운데 선 기준 오른쪽
 *   - 무한 스크롤 (함수는 functions.php에 있다고 가정)
 *   - 카드 모양/레이아웃은 질문 주신 예시 코드 사용
 */

get_header(); 
?>

<div class="relative min-h-screen pt-4"> <!-- 상단 여백 -->

  <!-- 중앙 세로 라인 -->
  <div class="absolute left-1/2 top-0 w-0.5 h-full bg-base-300 -translate-x-1/2"></div>

  <!-- 글 목록을 담는 컨테이너 (무한 스크롤로 글이 append) -->
  <div id="timeline-posts" class="relative z-10 container mx-auto mt-12">
    <!-- Ajax로 추가되는 .post-item들이 들어갑니다. -->
  </div>

  <!-- 로딩중 표시 -->
  <div id="loading-indicator" class="flex justify-center mt-6 mb-8 hidden">
    <button class="btn btn-ghost loading">
      로딩중 <span class="loading loading-dots loading-md"></span>
    </button>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let currentPage = 0;
  let maxPage = 1;
  let isLoading = false;

  const timelineContainer = document.getElementById('timeline-posts');
  const loadingIndicator = document.getElementById('loading-indicator');

  // 첫 로드
  loadMorePosts();

  // 스크롤 이벤트
  window.addEventListener('scroll', function() {
    if (isLoading) return;

    const scrollY = window.scrollY;
    const windowH = window.innerHeight;
    const docH = document.body.scrollHeight;
    
    // 스크롤이 끝에서 300px 남았을 때 추가 로드
    if (scrollY + windowH >= docH - 300) {
      if (currentPage < maxPage) {
        loadMorePosts();
      }
    }
  });

  function loadMorePosts() {
    isLoading = true;
    loadingIndicator.classList.remove('hidden');
    currentPage++;

    // 무한 스크롤 Ajax
    jQuery.ajax({
      url: "<?php echo admin_url('admin-ajax.php'); ?>",
      type: "POST",
      data: {
        action: 'my_infinite_scroll_timeline', // functions.php에 정의된 action
        paged: currentPage
      },
      success: function(response) {
        isLoading = false;
        loadingIndicator.classList.add('hidden');

        if (response.success) {
          // 새로운 post-item HTML 추가
          timelineContainer.insertAdjacentHTML('beforeend', response.data.html);
          maxPage = response.data.max_page;
        }
      },
      error: function() {
        isLoading = false;
        loadingIndicator.classList.add('hidden');
      }
    });
  }
});
</script>

<?php get_footer(); ?>