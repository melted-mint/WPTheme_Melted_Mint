<?php
/**
 * single-blog.php
 * 블로그 단일 페이지 템플릿
 * (글 정보, 이전/다음 글, 댓글 목록+폼)
 */

// 보통은 get_header()를 가장 위에 호출
get_header();
?>

<div class="grid grid-cols-1 lg:grid-cols-[17.5rem_1fr] gap-4 py-4 max-w-[80rem] mx-auto sm:px-4 items-start">
    <!-- 사이드바 (카테고리/태그) -->
    <aside class="order-last lg:order-none lg:sticky lg:top-12 self-start">
        <div class="flex flex-col">
            <div class="flex cardComponent rounded-xl px-2 py-2 mb-4">
                <?php get_template_part('templates/blog/sidebar-left-category'); ?>
            </div>
            <div class="flex cardComponent rounded-xl px-2 py-2">
                <?php get_template_part('templates/blog/sidebar-left-tag'); ?>
            </div>
        </div>
    </aside>

    <!-- 메인 영역 -->
    <main class="order-first lg:order-none rounded-xl">

    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            
            <?php
            // (A) 글자수/읽기시간/댓글상태 계산
            $content_raw = get_the_content(null, false);
            $content_stripped = wp_strip_all_tags($content_raw);
            $content_no_spaces = preg_replace('/\s+/', '', $content_stripped);
            $char_count = mb_strlen($content_no_spaces, 'UTF-8');
            $word_count = str_word_count($content_stripped);
            $reading_time = (int)($word_count / 200 + 1);

            $comment_status_text = ( 'open' === get_post()->comment_status ) ? '댓글 허용' : '댓글 불가';
            ?>

            <!-- 카드 컨테이너 (글 정보) -->
            <article class="p-4 rounded-lg shadow-md cardComponent mb-6">
                
                <!-- 1) 글자수/읽기시간/댓글상태 -->
                <div class="flex items-center gap-x-4 mb-2">
                    <!-- 글자수 -->
                    <div class="flex items-center gap-x-1">
                        <div class="w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center smallBoxComponent rounded">
                            <!-- 예시 아이콘 -->
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" viewBox="0 -960 960 960" fill="currentColor">
                                <path d="M120-240v-80h480v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base"><?php echo number_format($char_count); ?> 글자</span>
                    </div>
                    <!-- 읽기시간 -->
                    <div class="flex items-center gap-x-1">
                        <div class="w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center smallBoxComponent rounded">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" viewBox="0 -960 960 960" fill="currentColor">
                            <path d="M320-160h320v-120q0-66-47-113t-113-47q-66 0-113 47t-47 113v120ZM160-80v-80h80v-120q0-61 28.5-114.5T348-480q-51-32-79.5-85.5T240-680v-120h-80v-80h640v80h-80v120q0 61-28.5 114.5T612-480q51 32 79.5 85.5T720-280v120h80v80H160Z"/>                            </svg>
                        </div>
                        <span class="text-sm sm:text-base"><?php echo $reading_time; ?> 분</span>
                    </div>
                    <!-- 댓글 상태 -->
                    <div class="flex items-center gap-x-1">
                        <div class="w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center smallBoxComponent rounded">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" viewBox="0 -960 960 960" fill="currentColor">
                                <path d="M880-80 720-240H320q-33 0-56.5-23.5T240-320v-40h440q33 0 56.5-23.5T760-440v-280h40q33 0 56.5 23.5T880-640v560ZM160-473l47-47h393v-280H160v327ZM80-280v-520q0-33 23.5-56.5T160-880h440q33 0 56.5 23.5T680-800v280q0 33-23.5 56.5T600-440H240L80-280Zm80-240v-280 280Z"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base"><?php echo $comment_status_text; ?></span>
                    </div>
                </div>

                <!-- 2) 글 제목 -->
                <div class="block font-semibold text-2xl sm:text-3xl mb-2">
                    <?php the_title(); ?>
                </div>

                <!-- 3) 날짜/카테고리/태그 -->
                <div class="flex flex-wrap items-center gap-x-4 text-xs sm:text-sm grayTextThings mb-2">
                    
                    <!-- 작성일/수정일 -->
                    <div class="flex items-center gap-x-1">
                        <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class=" fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                        </div>
                        <span class="mr-2"><?php echo get_the_date('Y-m-d'); ?></span>
                        <?php if ( get_the_date() != get_the_modified_date() ) : ?>
                            <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v200h-80v-40H200v400h280v80H200Zm0-560h560v-80H200v80Zm0 0v-80 80ZM560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z"/></svg>
                            </div>
                            <span class="-mr-1"><?php echo get_the_modified_date('Y-m-d'); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- 카테고리 -->
                    <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent">
                        <svg viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6">
                            <path d="M300-80q-58 0-99-41t-41-99v-520q0-58 41-99t99-41h500v600q-25 0-42.5 17.5T740-220q0 25 17.5 42.5T800-160v80H300Zm-60-267q14-7 29-10t31-3h20v-440h-20q-25 0-42.5 17.5T240-740v393Zm160-13h320v-440H400v440Zm-160 13v-453 453Zm60 187h373q-6-14-9.5-28.5T660-220q0-16 3-31t10-29H300q-26 0-43 17.5T240-220q0 26 17 43t43 17Z"/>
                        </svg>
                    </div>
                    <div class="btn btn-ghost text-xs sm:text-sm rounded-lg h-7 sm:h-8 w-fit -mx-3 px-1 hoveronlyButton">
                        <?php the_category(''); ?>
                    </div>
                </div>

                <!-- 태그 -->
                <div class="text-xs sm:text-sm grayTextThings mb-6">
                    <div class="mt-1 sm:mt-2 flex w-fit items-center text-xs sm:text-sm grayTextThings">
                        <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-5 h-5 sm:w-6 sm:h-6"><path d="m240-160 40-160H120l20-80h160l40-160H180l20-80h160l40-160h80l-40 160h160l40-160h80l-40 160h160l-20 80H660l-40 160h160l-20 80H600l-40 160h-80l40-160H360l-40 160h-80Zm140-240h160l40-160H420l-40 160Z"/></svg>
                        </div>
                        <div>
                            <?php
                            $tags = get_the_tags();
                            if ( $tags ) {
                                echo '<div class="flex flex-wrap items-center">';
                                foreach ( $tags as $index => $tag ) {
                                    $tag_link = get_tag_link( $tag->term_id );
                                    if ( $index > 0 ) echo '<span class="mx-0">/</span>';
                                    ?>
                                    <a href="<?php echo esc_url($tag_link); ?>" 
                                       class="btn btn-ghost rounded-lg px-1 h-7 sm:h-8 lg:h-9 hoveronlyButton">
                                       <?php echo esc_html($tag->name); ?>
                                    </a>
                                    <?php
                                }
                                echo '</div>';
                            } else {
                                echo '<span class="grayTextThings ml-1">No tags</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- 점선 구분선 -->
                <hr class="h-[3px] border-0 my-[3px] sm:h-[3px] sm:my-[3px] bg-gray-800"
                    style="
                    background: repeating-linear-gradient(
                        to right,
                        #ccc 0,
                        #ccc 10px,
                        transparent 10px,
                        transparent 14px
                    );
                    ">
                
                <!-- 본문 -->
                <div class="prose max-w-none my-4">
                    <?php the_content(); ?>
                </div>
            </article>

            <!-- 이전글/다음글 -->
            <?php
            $prev_post = get_previous_post();
            $next_post = get_next_post();
            if ( $prev_post || $next_post ) : ?>
                <div class="mb-6" id="neighbors">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <!-- 이전 글 -->
                        <div class="cardComponent hoveronlyButton rounded-lg w-full sm:w-auto flex-1">
                            <?php if ( $prev_post ) : ?>
                                <a href="<?php echo get_permalink($prev_post->ID); ?>"
                                   class="block text-base-content transition-colors
                                          rounded-md py-6 px-6
                                          flex items-center justify-between
                                          text-2xl">
                                    <!-- 왼쪽 화살표 아이콘 -->
                                    <svg class="w-14 h-14 -mx-3 -my-5 flex-shrink-0 mr-3" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                                    </svg>
                                    <span class="flex-1">
                                        <?php echo get_the_title($prev_post->ID); ?>
                                    </span>
                                </a>
                            <?php else : ?>
                                <span class="block py-6 px-6 rounded-md disabledButton text-center text-xl">
                                    이전 글이 없습니다.
                                </span>
                            <?php endif; ?>
                        </div>
                        <!-- 다음 글 -->
                        <div class="cardComponent hoveronlyButton rounded-lg w-full sm:w-auto flex-1">
                            <?php if ( $next_post ) : ?>
                                <a href="<?php echo get_permalink($next_post->ID); ?>"
                                   class="block text-base-content transition-colors
                                          rounded-md py-6 px-6
                                          flex items-center justify-between
                                          text-2xl">
                                    <span class="flex-1 text-right">
                                        <?php echo get_the_title($next_post->ID); ?>
                                    </span>
                                    <!-- 오른쪽 화살표 아이콘 -->
                                    <svg class="w-14 h-14 -mx-3 -my-5 flex-shrink-0 ml-3" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/>
                                    </svg>
                                </a>
                            <?php else : ?>
                                <span class="block py-6 px-6 rounded-md disabledButton text-center text-xl">
                                    다음 글이 없습니다.
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ★ 댓글 섹션 (comments_template) ★ -->
            <?php
            /**
             * (중요) 워드프레스 기본 comments.php 파일을 불러와
             * wp_list_comments(), comment_form() 등을 실행하도록 함.
             *
             * 조건: 댓글이 열려있거나(comment_open), 댓글이 하나 이상 있으면
             * comments_template() 호출
             */
            if ( comments_open() || get_comments_number() ) {
                comments_template();
            }
            ?>

        <?php endwhile; ?>
    <?php else : ?>
        <p>해당 글을 찾을 수 없습니다.</p>
    <?php endif; ?>

    </main>
</div>

<?php
get_template_part('templates/blog/footer-navigation');
get_template_part('footer-scroll');
get_footer();