<?php if ( have_posts() ): ?>
    <ul class="space-y-4">
        <?php while ( have_posts() ): the_post(); ?>
            <!-- 카드 컨테이너 -->
            <li class="p-4 rounded-lg shadow-md grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-4 cardComponent">
                
                <!-- 왼쪽: 텍스트/메타 -->
                <div>
                    <!-- 제목 -->
                    <a href="<?php the_permalink(); ?>" 
                    class="block font-semibold mb-2 -mt-2 group hoveronlyText text-3xl"> <!-- hover 시 텍스트 색상 변경 -->

                        <?php the_title(); ?>

                        <!-- 기본적으로 보이지 않다가, group-hover 시 나타나도록 -->
                        <svg class="w-14 h-14 -mt-1 inline-block transition-all opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 duration-100 fill-current" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                            <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                        </svg>
                    </a>

                    <!-- 글 요약 (Excerpt) -->
                    <div class="mb-2 -mt-2 ml-2 text-sm">
                        <p>
                            <?php the_excerpt(); ?>
                        </p>
                    </div>
                    <!-- 날짜 -->
                    <div class="flex items-center text-sm mb-2">
                        <!-- 날짜 아이콘 -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-4 h-4 mr-1"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                        <!-- 글 날짜 -->
                        <span class="mr-2"><?php echo get_the_date('Y-m-d'); ?></span>
                        <!-- 마지막 수정일 -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-4 h-4 mr-1"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v200h-80v-40H200v400h280v80H200Zm0-560h560v-80H200v80Zm0 0v-80 80ZM560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z"/></svg>
                        <span><?php echo get_the_modified_date('Y-m-d'); ?></span>
                    </div>

                    <div class="flex hoveronlyButton w-fit items-center text-sm mb-2">
                        <!-- 카테고리 아이콘 -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-4 h-4 mr-1"><path d="M300-80q-58 0-99-41t-41-99v-520q0-58 41-99t99-41h500v600q-25 0-42.5 17.5T740-220q0 25 17.5 42.5T800-160v80H300Zm-60-267q14-7 29-10t31-3h20v-440h-20q-25 0-42.5 17.5T240-740v393Zm160-13h320v-440H400v440Zm-160 13v-453 453Zm60 187h373q-6-14-9.5-28.5T660-220q0-16 3-31t10-29H300q-26 0-43 17.5T240-220q0 26 17 43t43 17Z"/></svg>
                        <!-- 카테고리 목록 -->
                        <span>
                            <?php the_category('/'); ?>
                        </span>
                    </div>

                    <div class="flex hoveronlyButton w-fit items-center text-sm">
                        <!-- 태그 아이콘 -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-4 h-4 mr-1"><path d="m240-160 40-160H120l20-80h160l40-160H180l20-80h160l40-160h80l-40 160h160l40-160h80l-40 160h160l-20 80H660l-40 160h160l-20 80H600l-40 160h-80l40-160H360l-40 160h-80Zm140-240h160l40-160H420l-40 160Z"/></svg>
                        <!-- 태그 목록 -->
                        <span>
                            <?php if ( has_tag() ): ?>
                                <?php the_tags('', '/'); ?>
                        </span>
                            <?php else: ?>
                                <span>태그 없음</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- 오른쪽: 썸네일(대표이미지) -->
                <div class="w-full sm:w-40 h-24 sm:h-auto overflow-hidden rounded">
                    <?php if ( has_post_thumbnail() ): ?>
                        <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover']); ?>
                    <?php else: ?>
                        <!-- 대표이미지가 없을 때 대체 이미지 -->
                    <?php endif; ?>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>이 카테고리에 글이 없습니다.</p>
<?php endif; ?>