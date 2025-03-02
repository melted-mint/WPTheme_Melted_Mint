document.addEventListener('DOMContentLoaded', () => {
    const toggleCheckbox = document.getElementById('themeToggle');
    // 혹시 null이면 -> 아이디 불일치
    if (!toggleCheckbox) {
        console.warn('themeToggle checkbox not found!');
        return;
    }

    const htmlEl = document.documentElement;

    // 사용자 저장된 테마 확인
    const userTheme = localStorage.getItem('theme');
    if (userTheme) {
        htmlEl.setAttribute('data-theme', userTheme);
        toggleCheckbox.checked = (userTheme === 'dark');
    } else {
        // 기본값을 light
        htmlEl.setAttribute('data-theme', 'light');
    }

    // 체크박스 변화 감지
    toggleCheckbox.addEventListener('change', () => {
        if (toggleCheckbox.checked) {
        htmlEl.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
        } else {
        htmlEl.setAttribute('data-theme', 'light');
        localStorage.setItem('theme', 'light');
        }
    });
  /* Hue Slider */
  const hueSlider = document.getElementById("hueSlider");
    const hueValue = document.getElementById("hueValue");
    const resetHue = document.getElementById("resetHue");
    const defaultHue = 165; // 기본 Hue 값

    // 🌟 저장된 Hue 값 가져오기 (없으면 기본값 165)
    const savedHue = localStorage.getItem("hue") || defaultHue;
    hueSlider.value = savedHue;
    hueValue.textContent = savedHue;
    document.documentElement.style.setProperty("--hue", savedHue);

    // 초기화 버튼 상태 업데이트
    toggleResetButton(savedHue);

    // 🎨 Hue 값 변경 시 업데이트
    hueSlider.addEventListener("input", () => {
        const newHue = hueSlider.value;
        hueValue.textContent = newHue;
        document.documentElement.style.setProperty("--hue", newHue);
        localStorage.setItem("hue", newHue); // 저장
        toggleResetButton(newHue);
    });

    // 🔄 초기화 버튼 클릭 시 Hue 리셋
    resetHue.addEventListener("click", () => {
        hueSlider.value = defaultHue;
        hueValue.textContent = defaultHue;
        document.documentElement.style.setProperty("--hue", defaultHue);
        localStorage.setItem("hue", defaultHue); // 저장
        toggleResetButton(defaultHue);
    });

    // 🚀 초기화 버튼 표시 여부 관리 함수
    function toggleResetButton(hue) {
        if (Number.parseInt(hue) === defaultHue) {
            resetHue.classList.add("hidden"); // 기본값이면 숨김
        } else {
            resetHue.classList.remove("hidden"); // 변경되면 표시
        }
    }
});
