<?php if ( have_posts() ): ?>
    <ul class="space-y-4">
        <?php while ( have_posts() ): the_post(); ?>
            <!-- 카드 컨테이너 -->
            <li class="p-4 bg-base-100 rounded-lg shadow-md flex flex-col md:flex-row gap-4 
                       border-b border-gray-300 pb-4 sm:border-none last:border-b-0">
                
                <!-- 썸네일(대표이미지) - sm에서는 위, md 이상에서는 오른쪽 -->
                <div class="w-full h-40 md:w-40 md:h-auto overflow-hidden rounded order-1 md:order-2">
                    <?php if ( has_post_thumbnail() ): ?>
                        <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover']); ?>
                    <?php else: ?>
                        <!-- 이미지 없는 경우: 화살표 아이콘 -->
                        <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
                            <!-- 화살표 아이콘 예시 (Tailwind Heroicons “arrow-right”) -->
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" 
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 텍스트/메타 정보 -->
                <div class="flex-1 order-2 md:order-1">
                    <!-- 날짜 & 카테고리 -->
                    <div class="flex items-center text-sm text-gray-400 mb-2">
                        <!-- 날짜 아이콘 -->
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 2a1 1 0 012 0v1h4V2a1 1 0 012 0v1h1.5A1.5 1.5 0 0118 4.5v1.757A2.494 2.494 0 0017 6H3a2.494 2.494 0 00-1 .257V4.5A1.5 1.5 0 013.5 3H5V2a1 1 0 011-1zM3 8v7.5A1.5 1.5 0 004.5 17h11a1.5 1.5 0 001.5-1.5V8H3z"/>
                        </svg>
                        <!-- 글 날짜 -->
                        <span><?php echo get_the_date('Y-m-d'); ?></span>

                        <!-- 구분자 -->
                        <span class="mx-2">·</span>

                        <!-- 카테고리 아이콘 -->
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-4.472 10.063l-1.341 2.632A1 1 0 005 16h10a1 1 0 00.813-1.583l-1.341-2.354A6 6 0 0010 2z"/>
                        </svg>
                        <!-- 카테고리 목록 -->
                        <span>
                            <?php the_category(', '); ?>
                        </span>
                    </div>

                    <!-- 제목 -->
                    <a href="<?php the_permalink(); ?>" class="block text-xl font-semibold mb-2 hover:underline">
                        <?php the_title(); ?>
                    </a>

                    <!-- 글 요약 (Excerpt) -->
                    <p class="text-gray-500 mb-2">
                        <?php the_excerpt(); ?>
                    </p>

                    <!-- “Read More” 버튼 (선택) -->
                    <a href="<?php the_permalink(); ?>" 
                       class="inline-block mt-2 px-3 py-1 text-sm text-white bg-primary rounded hover:bg-primary-focus">
                       Read More
                    </a>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>이 카테고리에 글이 없습니다.</p>
<?php endif; ?>