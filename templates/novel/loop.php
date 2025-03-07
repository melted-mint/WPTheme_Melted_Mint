<?php if ( have_posts() ): ?>
    <ul class="space-y-3">
        <?php while ( have_posts() ): the_post(); ?>
            <!-- 카드 컨테이너 -->
            <li class="p-2 rounded-lg shadow-md grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-4 cardComponent">
                
                <!-- 왼쪽: 텍스트/메타 -->
                <div class="pl-2 order-2 sm:order-1">
                    <!-- 제목 -->
                    <a href="<?php the_permalink(); ?>" 
                    class="block font-semibold group hoveronlyText text-xl sm:text-2xl"> <!-- hover 시 텍스트 색상 변경 -->

                        <?php the_title(); ?>

                        <!-- 기본적으로 보이지 않다가, group-hover 시 나타나도록 -->
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 inline-block transition-all opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 duration-100 fill-current -mt-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                            <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                        </svg>
                    </a>

                    <!-- Description 메타데이터 표시 -->
                    <div class="ml-2 text-md sm:text-lg">
                        <p>
                            <?php
                            // 'description' 메타데이터 가져오기
                            $description = get_post_meta( get_the_ID(), 'description', true );

                            if ( ! empty( $description ) ) {
                                // description이 존재하면 그대로 출력
                                echo esc_html( $description );
                            } else {
                                // description이 없으면 대체 문구 출력
                                echo '';
                            }
                            ?>
                        </p>
                    </div>
                    <div class="flex flex-row">
                        <!-- 날짜 -->
                        <div class="flex mt-1 sm:mt-2 flex items-center text-xs sm:text-sm grayTextThings">
                            <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-2">
                                <!-- 날짜 아이콘 -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class=" fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                            </div>
                            <!-- 글 날짜 -->
                            <span class="mr-2"><?php echo get_the_date('Y-m-d'); ?></span>
                        <?php if ( get_the_date() != get_the_modified_date() ): ?>
                            <!-- 마지막 수정일 -->
                            <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v200h-80v-40H200v400h280v80H200Zm0-560h560v-80H200v80Zm0 0v-80 80ZM560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z"/></svg>
                            </div>
                            <span class="mr-2"><?php echo get_the_modified_date('Y-m-d'); ?></span>
                        <?php endif; ?>
                        </div>

                        <div class="flex mt-1 sm:mt-2 flex w-fit items-center text-xs sm:text-sm grayTextThings">
                            <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                                <!-- 카테고리 아이콘 -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M300-80q-58 0-99-41t-41-99v-520q0-58 41-99t99-41h500v600q-25 0-42.5 17.5T740-220q0 25 17.5 42.5T800-160v80H300Zm-60-267q14-7 29-10t31-3h20v-440h-20q-25 0-42.5 17.5T240-740v393Zm160-13h320v-440H400v440Zm-160 13v-453 453Zm60 187h373q-6-14-9.5-28.5T660-220q0-16 3-31t10-29H300q-26 0-43 17.5T240-220q0 26 17 43t43 17Z"/></svg>
                            </div>
                            <!-- 카테고리 목록 -->
                            <div class="btn btn-ghost text-xs sm:text-sm rounded-lg h-7 sm:h-8 w-fit px-1 hoveronlyButton">
                                <?php the_category(''); ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-1 sm:mt-2 flex w-fit items-center text-xs sm:text-sm grayTextThings">
                        <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                            <!-- 태그 아이콘 -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="m240-160 40-160H120l20-80h160l40-160H180l20-80h160l40-160h80l-40 160h160l40-160h80l-40 160h160l-20 80H660l-40 160h160l-20 80H600l-40 160h-80l40-160H360l-40 160h-80Zm140-240h160l40-160H420l-40 160Z"/></svg>
                        </div>
                        <!-- 태그 목록 -->
                        <div>
                            <?php
                            $tags = get_the_tags(); // 현재 글의 태그 배열 가져오기
                            if ( $tags ) :
                                echo '<div class="flex flex-wrap items-center">'; // 컨테이너
                                foreach ( $tags as $index => $tag ) :
                                    // 태그 링크(아카이브 페이지) 생성
                                    $tag_link = get_tag_link( $tag->term_id );
                                    
                                    // 첫 번째 버튼이 아니라면, 앞에 슬래시를 표시
                                    if ( $index > 0 ) {
                                        // 슬래시를 버튼과 버튼 사이에만 출력
                                        echo '<span class="mx-0">/</span>';
                                    }
                                    ?>
                                    <a href="<?php echo esc_url( $tag_link ); ?>" 
                                    class="btn btn-ghost rounded-lg px-1 h-7 sm:h-8 lg:h-9 hoveronlyButton">
                                    <?php echo esc_html( $tag->name ); ?>
                                    </a>
                                    <?php
                                endforeach;
                                echo '</div>';
                            else:
                                echo '<span class="grayTextThings ml-1">태그 없음</span>';
                            endif;
                            ?>
                        </div>
                    </div>

                    
                    <!-- 글자수(공백 제외), 읽기 시간 -->
                    <?php
                    $content_raw = get_the_content(null, false);
                    $content_stripped = wp_strip_all_tags($content_raw);
                    // 공백 제거
                    $content_no_spaces = preg_replace('/\s+/', '', $content_stripped);
                    // 글자수
                    $char_count = mb_strlen($content_no_spaces, 'UTF-8');
                    // 단어 수(영어 전용)
                    $word_count = str_word_count($content_stripped);
                    // 200 WPM
                    $reading_time = (int)($word_count / 200 + 1);
                    ?>
                    <div class="p-2 mt-1 sm:mt-2 text-md sm:text-lg grayTextThings">
                        <?php echo number_format($char_count); ?> 글자&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                        <?php echo $reading_time; ?>분
                    </div>
                </div>

                <!-- 썸네일(대표이미지) -->
                <?php if ( has_post_thumbnail() ): ?>
                    <div class="-mb-2 sm:mb-0 relative group overflow-hidden rounded order-1 sm:order-2"> <!-- 작은화면: order-1, 큰화면: order-2 -->
                        <div class="sm:w-50 w-full h-full">
                            <a href="<?php the_permalink(); ?>" class="block w-full h-full relative">
                                <?php the_post_thumbnail('medium', [
                                    'class' => 'rounded-lg w-full h-40 sm:h-full object-cover transition ease-in-out duration-300 group-hover:opacity-40'
                                ]); ?>
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition ease-in-out duration-200">
                                    <svg class="w-16 h-16 text-white" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                                        <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                                    </svg>
                                </div>
                            </a>
                        </div>
                    <?php else: ?>
                    <div class="hidden sm:block relative group overflow-hidden rounded order-1 sm:order-2"> <!-- 작은화면: order-1, 큰화면: order-2 -->
                        <div class="sm:w-24 sm:h-full">
                            <a href="<?php the_permalink(); ?>" class="btn btn-ghost rounded-lg tagButton w-full sm:h-full sm:flex items-center justify-center text-base-content">
                                <svg class="w-16 h-16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                                    <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                                </svg>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p >이 카테고리에 글이 없습니다.</p>
<?php endif; ?>