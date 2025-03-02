<?php
/*
Template Name: Post
*/

$current_user   = wp_get_current_user();
$is_admin       = current_user_can('administrator');
$is_editor      = current_user_can('editor');
$is_author      = current_user_can('author');
$is_contributor = current_user_can('contributor');
$is_subscriber  = current_user_can('subscriber');

// (1) 비로그인 시 로그인 페이지로 이동
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( home_url('/') ) );
    exit;
}

// (2) CPT별 접근 권한
$allowed_pages = array(
    'blog'      => ($is_admin),
    'novel'     => ($is_admin),
    'spinoff'   => ($is_contributor || $is_admin),
    'community' => true
);

// (3) 카테고리 맵
$page_category_map = array(
    'blog'      => array(8, 9, 10, 11, 12),
    'novel'     => array(38, 18),
    'spinoff'   => array(20),
    'community' => array(9, 10, 11, 12, 34)
);
$page_category_detailed = array();
foreach ($page_category_map as $page_slug => $cat_ids) {
    $page_category_detailed[$page_slug] = array();
    foreach ($cat_ids as $cid) {
        $term = get_category($cid);
        $page_category_detailed[$page_slug][] = array(
            'id'   => $cid,
            'name' => ($term && ! is_wp_error($term)) ? $term->name : $cid
        );
    }
}

// (4) 모든 태그
$all_tags = get_tags(array('hide_empty' => false));

get_header();
?>

<div class="max-w-[90rem] mt-4 cardComponent mx-auto p-6 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4">글쓰기</h2>
    <!-- 내부에 중첩된 form 태그 제거 -->
    <form method="post" enctype="multipart/form-data" id="postForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <!-- 1) 제목 -->
                <label class="block mb-2 font-semibold">제목 <span class="text-red-500">*</span></label>
                <input type="text" name="post_title" required class="w-full p-2 border rounded-md">
            </div>
            <div>
                <!-- 2) Description -->
                <label class="block mt-4 md:mt-0 mb-2 font-semibold">설명</label>
                <input name="post_description" class="w-full border p-2 rounded-md" placeholder="글 설명 (선택)"></input>
            </div>
        </div>
        <!-- 3) 썸네일 -->
        <label class="block mt-4 mb-2 font-semibold">썸네일(대표이미지)</label>
        <!-- 숨겨진 파일 입력 -->
        <input type="file" name="thumbnail" id="thumbnailInput" accept="image/*" style="display:none;">
        <!-- 커스텀 버튼 -->
        <button type="button" id="customThumbnailButton" class="p-2 border hoveronlyButton rounded-md">
        대표이미지 선택
        </button>
        <!-- 선택된 파일명 표시 영역 -->
        <span id="fileNameDisplay" class="ml-2"></span>

        <!-- 4) CPT 선택 -->
        <label class="block mt-4 mb-2 font-semibold">게시할 페이지 선택</label>
        <div class="flex space-x-4">
            <?php foreach($allowed_pages as $page_slug => $allowed): ?>
                <?php if($allowed): ?>
                    <button type="button" 
                            class="hoveronlyButton page-select-btn px-4 py-2 border rounded-md"
                            data-page="<?php echo esc_attr($page_slug); ?>">
                        <?php echo ucfirst($page_slug); ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="post_page" id="selected_page" required>

        <!-- 5) 카테고리 -->
        <label class="block mt-4 mb-2 font-semibold">카테고리 선택 (하나만)</label>
        <div id="category-buttons" class="flex flex-wrap gap-2"></div>
        <input type="hidden" name="selected_category" id="selected_category" required>

        <!-- 6) 태그 -->
        <label class="block mt-4 mb-2 font-semibold">태그 선택</label>
        <div id="tag-buttons" class="flex flex-wrap gap-2">
            <?php foreach($all_tags as $tag): ?>
                <button type="button" class="hoveronlyButton tag-btn px-3 py-1 border rounded-md"
                        data-tag="<?php echo esc_attr($tag->name); ?>">
                    <?php echo esc_html($tag->name); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="selected_tags" id="selected_tags">

        <!-- 새 태그 추가 -->
        <label class="block mt-4 mb-2 font-semibold">새 태그 추가</label>
        <input type="text" id="new_tag_input" 
               class="w-full p-2 border rounded-md"
               placeholder="새 태그 입력 후 엔터">

        <!-- 7) 본문 (Summernote) -->
        <label class="block mt-4 mb-2 font-semibold">내용</label>
        <!-- 중첩 form 제거 후 textarea 사용 -->
        <textarea id="summernote" name="post_content"></textarea>

        <!-- 한마디글 (Novel/Spinoff 전용) - 기본 숨김 --> <!-- ← 추가 -->
        <div id="one-liner-container" class="mb-4" style="display:none;">
            <label class="block mb-1 font-semibold">한마디글</label>
            <input type="text" name="one_liner_value" id="one_liner_value"
                class="w-full p-2 border rounded-md" 
                placeholder="작가의 한마디를 적어주세요!">
        </div>

        <!-- 추가 기능: (A) 올리기 종류, (B) 예약 날짜/시간, (C) 비밀글 -->
        <label class="block mt-4 mb-2 font-semibold">예약 여부</label>
        <div id="publish-option-buttons" class="flex space-x-4 mb-2">
            <button type="button" class="hoveronlyButton publish-option-btn px-4 py-2 border rounded-md" data-option="immediate">즉시</button>
            <button type="button" class="hoveronlyButton publish-option-btn px-4 py-2 border rounded-md" data-option="schedule">예약</button>
        </div>
        <!-- 숨겨진 input에 선택 값 저장 -->
        <input type="hidden" name="publish_option" id="publish_option" value="immediate">

        <!-- 예약 날짜/시간 입력 (예약 선택 시 보임) -->
        <div id="schedule-options" class="mb-4" style="display:none;">
            <label class="block mb-1 font-semibold">예약 날짜/시간</label>
            <input type="datetime-local" name="scheduled_time" class="p-2 border rounded-md w-60">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <!-- 댓글 설정 -->
                <label class="block mt-4 mb-2 font-semibold">댓글 설정</label>
                <div id="comment-status-buttons" class="flex space-x-4 mb-2">
                    <button type="button" class="hoveronlyButton comment-status-btn px-4 py-2 border rounded-md" data-status="open">
                        댓글 허용
                    </button>
                    <button type="button" class="hoveronlyButton comment-status-btn px-4 py-2 border rounded-md" data-status="closed">
                        댓글 막기
                    </button>
                </div>
            </div>
            <div>
                <!-- (C) 비밀글: 커스텀 토글 -->
                <label class="block mb-2 font-semibold">비밀글 여부</label>
                <div id="private-post-toggle" class="hoveronlyButton p-2 border rounded-md inline-block cursor-pointer">
                    공개글
                </div>
                <input type="hidden" name="private_post" id="private_post" value="0">
            </div>
        </div>
        <input type="hidden" name="comment_status" id="comment_status" value="open">


        <!-- 라이선스 선택 -->
        <label class="block mt-4 mb-2 font-semibold">라이선스 선택</label>
        <div id="license-main-buttons" class="flex space-x-4 mb-2">
            <!-- 메인 버튼: CC -->
            <button type="button" 
                    class="hoveronlyButton license-main-btn px-4 py-2 border rounded-md"
                    data-option="cc">
                CC
            </button>
            <!-- 메인 버튼: 이외(기타) -->
            <button type="button" 
                    class="hoveronlyButton license-main-btn px-4 py-2 border rounded-md"
                    data-option="other">
                이외
            </button>
        </div>

        <!-- CC 하위 옵션 (기본 숨김) -->
        <div id="cc-suboptions" class="mb-4" style="display:none;">
            <label class="block mb-1 font-semibold">출처 범위(기본은 CC0)</label>
            <div class="flex flex-wrap gap-2">
                <!-- 각각 클릭 시 토글 -->
                <button type="button" class="hoveronlyButton cc-sub-btn px-3 py-1 border rounded-md" data-value="0">0</button>
                <button type="button" class="hoveronlyButton cc-sub-btn px-3 py-1 border rounded-md" data-value="BY">BY</button>
                <button type="button" class="hoveronlyButton cc-sub-btn px-3 py-1 border rounded-md" data-value="NC">NC</button>
                <button type="button" class="hoveronlyButton cc-sub-btn px-3 py-1 border rounded-md" data-value="ND">ND</button>
                <button type="button" class="hoveronlyButton cc-sub-btn px-3 py-1 border rounded-md" data-value="SA">SA</button>
            </div>
        </div>

        <!-- 이외(기타) 라이선스 직접 입력 (기본 숨김) -->
        <div id="other-license-container" class="mb-4" style="display:none;">
            <label class="block mb-1 font-semibold">라이선스 직접 입력</label>
            <input type="text" id="other-license-input" 
                class="w-full p-2 border rounded-md" 
                placeholder="직접 작성">
        </div>

        <!-- 최종 라이선스 값 저장용 (숨김) -->
        <input type="hidden" name="license_value" id="license_value" value="">

        <!-- 8) 버튼들: 입력 완료, 임시저장, 취소 -->
        <div class="flex justify-end gap-3 mt-6">
            <button type="submit" name="submit_post "
                    class="p-3 availableButton hoveronlyButton text-white rounded-md">
                게시
            </button>
            <button type="submit" name="save_draft"
                    class="btn-disable disabledButton p-3 text-white rounded-md">
                임시저장(미완)
            </button>
            <a href="<?php echo home_url(); ?>" 
               class="p-3 smallBoxComponent hoveronlyButton text-white rounded-md">
                취소
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('customThumbnailButton').addEventListener('click', () => {
    document.getElementById('thumbnailInput').click();
});

document.getElementById('thumbnailInput').addEventListener('change', (e) => {
    const fileName = e.target.files.length > 0 ? e.target.files[0].name : '';
    document.getElementById('fileNameDisplay').textContent = fileName;
});

const pageCategoryMap = <?php echo json_encode($page_category_detailed); ?>;

// CPT 선택 로직
document.querySelectorAll('.page-select-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.page-select-btn').forEach(b => 
            b.classList.remove('activatedButton','text-white')
        );
        this.classList.add('activatedButton','text-white');
        const selectedPage = this.getAttribute('data-page');
        document.getElementById('selected_page').value = selectedPage;
        
        // 1) 카테고리 버튼 업데이트
        const catContainer = document.getElementById('category-buttons');
        catContainer.innerHTML = '';
        if (pageCategoryMap[selectedPage]) {
            pageCategoryMap[selectedPage].forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'hoveronlyButton cat-btn px-3 py-1 border rounded-md';
                btn.setAttribute('data-cat-id', item.id);
                btn.textContent = item.name;
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.cat-btn').forEach(b =>
                        b.classList.remove('activatedButton','text-white')
                    );
                    this.classList.add('activatedButton','text-white');
                    document.getElementById('selected_category').value = this.getAttribute('data-cat-id');
                });
                catContainer.appendChild(btn);
            });
        }

        // 2) Novel/Spinoff면 한마디글 표시, 아니면 숨김  ← 추가
        const oneLinerContainer = document.getElementById('one-liner-container');
        if (selectedPage === 'novel' || selectedPage === 'spinoff') {
            oneLinerContainer.style.display = 'block';
        } else {
            oneLinerContainer.style.display = 'none';
        }
    });
});

// 5) 기본값들 초기 설정
jQuery(document).ready(() => {
    // (B) 예약 여부: 즉시
    jQuery('#publish_option').val('immediate');
    jQuery('.publish-option-btn[data-option="immediate"]').addClass('activatedButton text-white');
    jQuery('#schedule-options').hide();

    // (C) 댓글 설정: closed
    jQuery('#comment_status').val('closed');
    jQuery('.comment-status-btn[data-status="closed"]').addClass('activatedButton text-white');

    // (D) 비밀글 여부: 공개글(0)
    jQuery('#private_post').val('0');
    // "공개글" UI 이미 표시 상태

    // (E) 라이선스 기본 CC BY-NC-ND
    // 1) CC 메인버튼 활성화
    jQuery('.license-main-btn[data-option="cc"]').addClass('activatedButton text-white');
    // 2) CC 서브옵션 패널 보이기
    jQuery('#cc-suboptions').show();
    jQuery('#other-license-container').hide();

    // 3) 기본 ccSelected = ['BY','NC','ND']
    // → UI 표시 & license_value = "CC BY-NC-ND"
    const ccSelected = ['BY','NC','ND'];

    // 각각 버튼 찾아서 활성화
    ccSelected.forEach(val => {
        jQuery(`.cc-sub-btn[data-value="${val}"]`).addClass('activatedButton text-white');
    });
    // 실제 값
    jQuery('#license_value').val('CC ' + ccSelected.join('-'));
});

// 태그 선택
let selectedTags = [];
document.querySelectorAll('.tag-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tag = this.getAttribute('data-tag');
        if (this.classList.contains('activatedButton')) {
            this.classList.remove('activatedButton','text-white');
            selectedTags = selectedTags.filter(t => t !== tag);
        } else {
            this.classList.add('activatedButton','text-white');
            selectedTags.push(tag);
        }
        document.getElementById('selected_tags').value = selectedTags.join(',');
    });
});

// 새 태그 추가
document.getElementById('new_tag_input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const tag = this.value.trim();
        if (!tag) return;
        if (tag.includes(' ')) {
            alert('공백은 허용되지 않습니다.');
            this.value = '';
            return;
        }
        if (selectedTags.includes(tag)) {
            alert('이미 선택된 태그입니다.');
            this.value = '';
            return;
        }
        const existingBtn = document.querySelector(`.tag-btn[data-tag="${tag}"]`);
        if (existingBtn) {
            if (!existingBtn.classList.contains('activatedButton')) {
                existingBtn.classList.add('activatedButton','text-white');
                selectedTags.push(tag);
                document.getElementById('selected_tags').value = selectedTags.join(',');
            }
            this.value = '';
            return;
        }
        // 새 태그 버튼 생성
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'tag-btn px-3 py-1 border rounded-md activatedButton text-white';
        btn.setAttribute('data-tag', tag);
        btn.setAttribute('data-new', 'true');
        btn.textContent = tag;
        btn.addEventListener('click', function() {
            const t = this.getAttribute('data-tag');
            if (this.classList.contains('activatedButton')) {
                this.classList.remove('activatedButton','text-white');
                selectedTags = selectedTags.filter(item => item !== t);
                document.getElementById('selected_tags').value = selectedTags.join(',');
                if (this.getAttribute('data-new') === 'true') {
                    this.remove();
                }
            } else {
                this.classList.add('activatedButton','text-white');
                selectedTags.push(t);
                document.getElementById('selected_tags').value = selectedTags.join(',');
            }
        });
        selectedTags.push(tag);
        document.getElementById('selected_tags').value = selectedTags.join(',');
        document.getElementById('tag-buttons').appendChild(btn);
        this.value = '';
    }
});

// (E) 예약 vs 즉시 옵션 (미해결)
document.querySelectorAll('input[name="publish_option"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === 'schedule') {
            document.getElementById('schedule-options').style.display = 'block';
        } else {
            document.getElementById('schedule-options').style.display = 'none';
        }
    });
});

// (F) Summernote 초기화
jQuery(document).ready(() => {
    jQuery('#summernote').summernote({
        height: 200,
        lang: "ko-KR",
        focus: false,
        placeholder: '여기에 글을 쓰시면 돼요.',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        fontNames: [
            'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New',
            '맑은 고딕', '궁서', '굴림체', '굴림', '돋움체', '바탕체'
        ],
        fontNamesIgnoreCheck: [
            '맑은 고딕', '궁서', '굴림체', '굴림', '돋움체', '바탕체'
        ],
        callbacks: {
            onInit: function() {
                // (예시) 배경/글자색 간단 설정
                jQuery('.note-editable').css({
                    'background-color': '#dddddd',
                    'color': 'black'
                });
            }
        }
    });
});

// (G) AJAX를 이용한 폼 제출 처리
jQuery(document).ready(() => {
    let isSubmitted = false; 

    jQuery('#postForm').on('submit', (e) => {
        e.preventDefault();

        // 1) 필수 항목 검증 (제목, 페이지 선택, 카테고리, 내용)
        const title = jQuery('input[name="post_title"]').val().trim();
        const postPage = jQuery('#selected_page').val().trim();
        const category = jQuery('#selected_category').val().trim();
        let content = jQuery('#summernote').summernote('code').trim();
        if (content === '<p><br></p>') {
            content = '';
        }

        if (!title) {
            alert('제목을 입력해 주세요.');
            return false;
        }
        if (!postPage) {
            alert('게시할 페이지를 선택해 주세요.');
            return false;
        }
        if (!category) {
            alert('카테고리를 선택해 주세요.');
            return false;
        }
        if (!content) {
            alert('내용을 입력해 주세요.');
            return false;
        }

        // 1-2) 라이선스 필수 검사  ← 추가
        const licenseVal = jQuery('#license_value').val().trim();
        if (!licenseVal) {
            alert('라이선스를 선택(또는 입력)해 주세요.');
            return false;
        }

        // 2) 중복 제출 방지
        if (isSubmitted) return;
        isSubmitted = true;

        // 3) FormData 생성
        const formData = new FormData(e.currentTarget);
        formData.append('action', 'submit_post_ajax');

        // 4) 예약 옵션이 'schedule'이면 예약 시간도 추가
        const publishOption = jQuery('#publish_option').val();
        if (publishOption === 'schedule') {
            const scheduledTime = jQuery('input[name="scheduled_time"]').val().trim();
            if (!scheduledTime) {
                alert('예약 날짜/시간을 입력해 주세요.');
                isSubmitted = false;
                return false;
            }
            formData.append('scheduled_time', scheduledTime);
        }

        // 5) Ajax 요청
        jQuery.ajax({
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    if (response.data.scheduled) {
                        alert('글 게시가 예약되었습니다!'); 
                    } else {
                        alert('글을 올렸습니다!'); 
                    }
                    const pageSlug = jQuery('#selected_page').val();
                    window.location.href = '/' + pageSlug + '/?success=1';
                } else {
                    alert('오류: ' + response.data);
                    isSubmitted = false;
                }
            },
            error: () => {
                alert('글 등록 중 AJAX 오류가 발생했습니다.');
                isSubmitted = false;
            }
        });
    });
});

// (A) 커스텀 올리기 종류 버튼 동작
jQuery(document).ready(() => {
    jQuery('#publish-option-buttons .publish-option-btn').on('click', (e) => {
         jQuery('#publish-option-buttons .publish-option-btn').removeClass('activatedButton text-white');
         jQuery(e.currentTarget).addClass('activatedButton text-white');
         jQuery('#publish_option').val(jQuery(e.currentTarget).attr('data-option'));

         if (jQuery(e.currentTarget).attr('data-option') === 'schedule') {
             jQuery('#schedule-options').show();
         } else {
             jQuery('#schedule-options').hide();
         }
    });
});

// (B) 커스텀 비밀글 토글 동작
jQuery(document).ready(() => {
    jQuery('#private-post-toggle').on('click', () => {
        // 현재 값이 0이면 1로, 1이면 0으로 토글
        const currentVal = jQuery('#private_post').val();
        if (currentVal === '0') {
            jQuery('#private_post').val('1');
            jQuery('#private-post-toggle').text('비밀글');
            jQuery('#private-post-toggle').addClass('activatedButton text-white');
        } else {
            jQuery('#private_post').val('0');
            jQuery('#private-post-toggle').text('공개글');
            jQuery('#private-post-toggle').removeClass('activatedButton text-white');
        }
    });
});
// 댓글 활성화 여부
jQuery(document).ready(() => {
    jQuery('#comment-status-buttons .comment-status-btn').on('click', (e) => {
        jQuery('#comment-status-buttons .comment-status-btn').removeClass('activatedButton text-white');
        jQuery(e.currentTarget).addClass('activatedButton text-white');
        jQuery('#comment_status').val(jQuery(e.currentTarget).attr('data-status'));
    });
});
// 라이센스
jQuery(document).ready(() => {
    const mainBtns = jQuery('.license-main-btn'); 
    const ccSuboptions = jQuery('#cc-suboptions');
    const otherLicenseContainer = jQuery('#other-license-container');
    const otherLicenseInput = jQuery('#other-license-input');
    const licenseValueInput = jQuery('#license_value');

    let licenseMode = '';
    let ccSelected = [];

    // **기본 라이선스 = CC BY-NC-ND**  ← 추가
    // 즉, CC 모드로 시작 + ccSelected = ['BY','NC','ND']
    licenseMode = 'cc';
    ccSelected = ['BY','NC','ND'];
    // UI 초기 표시
    ccSuboptions.style.display = 'block'; 
    otherLicenseContainer.style.display = 'none';
    updateCCLicenseValue();
    updateCCButtonStyles();
    // 메인 버튼: CC를 활성화 스타일
    jQuery('.license-main-btn[data-option="cc"]').addClass('activatedButton text-white');

    // 메인 버튼 클릭
    mainBtns.on('click', function() {
        mainBtns.removeClass('activatedButton text-white');
        jQuery(this).addClass('activatedButton text-white');

        licenseMode = jQuery(this).attr('data-option');
        if (licenseMode === 'cc') {
            ccSuboptions.style.display = 'block';
            otherLicenseContainer.style.display = 'none';
            otherLicenseInput.val('');
            // 만약 기본값을 유지하려면 ccSelected를 그대로 둘 수도,
            // 혹은 초기화하려면 ccSelected = [];
            // 여기서는 "버튼 다시 누르면 초기화"로 가정:
            ccSelected = ['BY','NC','ND']; 
            updateCCLicenseValue();
            updateCCButtonStyles();
        } else {
            ccSuboptions.style.display = 'none';
            otherLicenseContainer.style.display = 'block';
            // CC 배열 비움
            ccSelected = [];
            updateCCLicenseValue();
            updateCCButtonStyles();
            licenseValueInput.val('');
        }
    });

    // CC 하위 옵션 클릭
    jQuery('.cc-sub-btn').on('click', function() {
        const val = jQuery(this).attr('data-value');
        if (val === '0') {
            // ccSelected = ['0']
            ccSelected = ['0'];
        } else {
            ccSelected = ccSelected.filter(item => item !== '0');
            // ND와 SA 동시 불가
            if (val === 'ND' && ccSelected.includes('SA')) {
                ccSelected = ccSelected.filter(item => item !== 'SA');
            }
            if (val === 'SA' && ccSelected.includes('ND')) {
                ccSelected = ccSelected.filter(item => item !== 'ND');
            }
            if (ccSelected.includes(val)) {
                ccSelected = ccSelected.filter(item => item !== val);
            } else {
                ccSelected.push(val);
            }
        }
        updateCCLicenseValue();
        updateCCButtonStyles();
    });

    function updateCCLicenseValue() {
        if (ccSelected.length === 0) {
            licenseValueInput.val('');
        } else {
            if (ccSelected.includes('0')) {
                licenseValueInput.val('CC0');
            } else {
                licenseValueInput.val('CC ' + ccSelected.join('-'));
            }
        }
    }

    function updateCCButtonStyles() {
        jQuery('.cc-sub-btn').each(function() {
            const val = jQuery(this).attr('data-value');
            if (ccSelected.includes(val)) {
                jQuery(this).addClass('activatedButton text-white');
            } else {
                jQuery(this).removeClass('activatedButton text-white');
            }
        });
    }

    // 이외(기타) 입력
    otherLicenseInput.on('input', function() {
        if (licenseMode === 'other') {
            licenseValueInput.val(jQuery(this).val().trim());
        }
    });
});
</script>
<?php get_footer(); ?>