<?php
/**
 * single-blog.php
 * 일반 포스트 단일 페이지
 */
get_header(); ?>

<div class="max-w-3xl mx-auto p-6 bg-base-100 shadow-lg rounded-lg">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>

            <!-- 카드 컨테이너 (글 내용 전용) -->
            <article class="p-2 rounded-lg shadow-md grid grid-cols-1 gap-4 cardComponent">

                <!-- 제목 -->
                <header>
                    <h1 class="text-2xl sm:text-3xl font-semibold mb-2">
                        <?php the_title(); ?>
                    </h1>
                </header>

                <!-- 날짜/수정일/카테고리/태그 등 메타 정보 -->
                <div class="flex flex-wrap items-center text-xs sm:text-sm grayTextThings mb-4">
                    <!-- 작성일 아이콘 + 날짜 -->
                    <div class="flex items-center mr-4">
                        <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-4 h-4 sm:w-5 sm:h-5">
                                <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/>
                            </svg>
                        </div>
                        <span><?php echo get_the_date('Y-m-d'); ?></span>
                    </div>

                    <!-- 수정일 (작성일과 다를 때만) -->
                    <?php if ( get_the_date() != get_the_modified_date() ) : ?>
                        <div class="flex items-center mr-4">
                            <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-4 h-4 sm:w-5 sm:h-5">
                                    <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v200h-80v-40H200v400h280v80H200Zm0-560h560v-80H200v80Zm0 0v-80 80ZM560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38Zm141-141-19-18 37 37-18-19Z"/>
                                </svg>
                            </div>
                            <span><?php echo get_the_modified_date('Y-m-d'); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- 카테고리 -->
                    <div class="flex items-center mr-4">
                        <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-4 h-4 sm:w-5 sm:h-5">
                                <path d="M300-80q-58 0-99-41t-41-99v-520q0-58 41-99t99-41h500v600q-25 0-42.5 17.5T740-220q0 25 17.5 42.5T800-160v80H300Zm-60-267q14-7 29-10t31-3h20v-440h-20q-25 0-42.5 17.5T240-740v393Zm160-13h320v-440H400v440Zm-160 13v-453 453Zm60 187h373q-6-14-9.5-28.5T660-220q0-16 3-31t10-29H300q-26 0-43 17.5T240-220q0 26 17 43t43 17Z"/>
                            </svg>
                        </div>
                        <span><?php the_category(', '); ?></span>
                    </div>

                    <!-- 태그 -->
                    <div class="flex items-center">
                        <div class="btn btn-ghost btn-xs sm:btn-sm btn-disabled btn-circle rounded-lg buttonComponent mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="fill-current w-4 h-4 sm:w-5 sm:h-5">
                                <path d="m240-160 40-160H120l20-80h160l40-160H180l20-80h160l40-160h80l-40 160h160l40-160h80l-40 160h160l-20 80H660l-40 160h160l-20 80H600l-40 160h-80l40-160H360l-40 160h-80Zm140-240h160l40-160H420l-40 160Z"/>
                            </svg>
                        </div>
                        <?php
                        $tags = get_the_tags();
                        if ( $tags ) {
                            echo '<div class="flex flex-wrap items-center">';
                            foreach ( $tags as $index => $tag ) {
                                if ( $index > 0 ) {
                                    echo '<span class="mx-0">/</span>';
                                }
                                $tag_link = get_tag_link($tag->term_id);
                                echo '<a href="'.esc_url($tag_link).'" class="btn btn-ghost rounded-lg px-1 h-7 sm:h-8 lg:h-9 hoveronlyButton">'.esc_html($tag->name).'</a>';
                            }
                            echo '</div>';
                        } else {
                            echo '<span class="grayTextThings ml-1">No tags</span>';
                        }
                        ?>
                    </div>
                </div>

                <!-- 썸네일(대표이미지) -->
                <?php if ( has_post_thumbnail() ): ?>
                    <div class="relative group overflow-hidden rounded w-full max-h-[30rem] mb-4">
                        <?php the_post_thumbnail('large', [
                            'class' => 'rounded-lg w-full h-auto object-cover transition ease-in-out duration-300 group-hover:opacity-40'
                        ]); ?>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition ease-in-out duration-200">
                            <svg class="w-16 h-16 text-white" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                                <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                            </svg>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 본문 -->
                <div class="prose max-w-none">
                    <?php the_content(); ?>
                </div>

                <!-- 글자수(공백 제외), 예상 읽기 시간 -->
                <?php
                $content_raw = get_the_content(null, false);
                $content_stripped = wp_strip_all_tags($content_raw);
                $content_no_spaces = preg_replace('/\s+/', '', $content_stripped);
                $char_count = mb_strlen($content_no_spaces, 'UTF-8');
                $word_count = str_word_count($content_stripped);
                $reading_time = ceil($word_count / 200 + 1);
                ?>
                <div class="p-2 mt-2 text-md sm:text-lg grayTextThings">
                    <?php echo number_format($char_count); ?> 글자 | <?php echo $reading_time; ?>분
                </div>

                <!-- (옵션) 글 수정 버튼: 작성자나 관리자만 보이도록 -->
                <?php if ( current_user_can('edit_post', get_the_ID()) ) : ?>
                    <div class="mt-4">
                        <a href="<?php echo get_edit_post_link(get_the_ID()); ?>" class="btn btn-primary">
                            글 수정하기
                        </a>
                    </div>
                <?php endif; ?>

            </article>

            <!-- 이전글/다음글 (옵션) -->
            <nav class="flex justify-between mt-6 text-sm">
                <div class="prev">
                    <?php previous_post_link('%link', '← 이전 글'); ?>
                </div>
                <div class="next">
                    <?php next_post_link('%link', '다음 글 →'); ?>
                </div>
            </nav>

        <?php endwhile; ?>
    <?php else: ?>
        <p>해당 글을 찾을 수 없습니다.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>