<?php
/**
 * Template Name: Split Timeline with Fixed Boxes
 *
 * - 상단 네비: 생략(있다고 가정)
 * - 왼쪽 상단 "블로그" 박스(fixed), 오른쪽 상단 "커뮤니티" 박스(fixed)
 * - 중앙 세로 라인
 * - 가운데에 글들이 쌓이는데, blog면 왼쪽으로, community면 오른쪽으로 배치
 * - 무한 스크롤
 */

get_header();
?>

<!-- 간단한 CSS (Tailwind + DaisyUI를 쓰지만, 약간의 custom CSS도 필요) -->
<style>
  .timeline-container {
    position: relative;
    min-height: 100vh;
    padding-top: 120px; /* 상단 네비/박스 높이만큼 여백 */
  }
  /* 중앙 세로 라인 */
  .center-line {
    position: absolute;
    left: 50%;
    top: 0;
    width: 2px;
    background-color: var(--tw-prose-body, #d4d4d4);
    /* 아래 높이는 적당히 크게 주거나 JS로 계산 */
    height: 9999px; 
    z-index: 0; /* 뒤로 */
  }

  /* post-item 은 기본적으로 가운데(dot) 기준이 되도록 relative */
  .post-item {
    width: 40rem; /* 카드 폭 예시 */
    margin: 2rem auto; /* 수직 간격 */
  }

  /* 블로그는 왼쪽으로 밀기 */
  .post-item[data-post-type="blog"] {
    transform: translateX(-50%); /* 왼쪽 */
  }
  /* 커뮤니티는 오른쪽으로 밀기 */
  .post-item[data-post-type="community"] {
    transform: translateX(calc(-50% + 20rem));
    /* 필요에 따라 계산 조정 (예: 카드 폭 절반 만큼 + 여백) */
  }

  /* dot, connector는 이미 .absolute left-1/2 처리가 되어 있음 */

</style>

<div class="timeline-container">

  <!-- 좌측 상단 고정 박스 -->
  <div class="fixed top-24 left-10">
    <div class="bg-blue-500 text-white p-4 rounded shadow-md">
      <span class="font-bold">블로그</span>
    </div>
  </div>

  <!-- 우측 상단 고정 박스 -->
  <div class="fixed top-24 right-10">
    <div class="bg-red-500 text-white p-4 rounded shadow-md">
      <span class="font-bold">커뮤니티</span>
    </div>
  </div>

  <!-- 중앙 세로 라인 -->
  <div class="center-line"></div>

  <!-- 글 목록 담는 영역 -->
  <div id="timeline-posts" class="relative z-10">
    <!-- 여기에 Ajax 로드된 .post-item들이 들어옴 -->
  </div>

  <!-- 로딩중/더보기 표시 -->
  <div id="loading-indicator" class="text-center mt-6 hidden">
    <span class="btn btn-ghost loading">로딩중...</span>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let currentPage = 0;
  let maxPage = 1;
  let isLoading = false;

  const timelineContainer = document.getElementById('timeline-posts');
  const loadingIndicator  = document.getElementById('loading-indicator');

  // 첫 로드
  loadMorePosts();

  window.addEventListener('scroll', function() {
    if (isLoading) return;
    const scrollY = window.scrollY;
    const windowH = window.innerHeight;
    const docH    = document.body.scrollHeight;

    if (scrollY + windowH >= docH - 300) {
      // 페이지 하단 근처
      if (currentPage < maxPage) {
        loadMorePosts();
      }
    }
  });

  function loadMorePosts() {
    isLoading = true;
    loadingIndicator.classList.remove('hidden');
    currentPage++;

    jQuery.ajax({
      url: "<?php echo admin_url('admin-ajax.php'); ?>",
      method: 'POST',
      data: {
        action: 'my_infinite_scroll_timeline',
        paged: currentPage
      },
      success: function(res) {
        isLoading = false;
        loadingIndicator.classList.add('hidden');
        if (res.success) {
          timelineContainer.insertAdjacentHTML('beforeend', res.data.html);
          maxPage = res.data.max_page;
        } else {
          // 끝
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

<?php
get_footer();