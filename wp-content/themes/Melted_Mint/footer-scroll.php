<!-- 우측 상단/하단 이동 버튼 -->
<div class="fixed right-4 bottom-12 md:bottom-4 max-w-[80rem] flex flex-col space-y-2 z-100">
    <!-- 최상단 이동 버튼 -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="btn btn-ghost scrollButton btn-circle btn-lg hoveronlyButton rounded-lg">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56-184-184Z"/></svg>    </button>
    <!-- 최하단 이동 버튼 -->
    <button onclick="window.scrollTo({top: 9999999999999, behavior: 'smooth'})" class="btn btn-ghost scrollButton btn-circle btn-lg hoveronlyButton rounded-lg">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M480-344 240-584l56-56 184 184 184-184 56 56-240 240Z"/></svg>
    </button>
</div>