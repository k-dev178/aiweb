(function () {
    const root = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    const savedTheme = localStorage.getItem('theme');
    const initialTheme = savedTheme === 'dark' ? 'dark' : 'light';

    function applyTheme(theme) {
        root.dataset.theme = theme;
        localStorage.setItem('theme', theme);

        if (toggle) {
            const isDark = theme === 'dark';
            toggle.setAttribute('aria-pressed', String(isDark));
            toggle.setAttribute('aria-label', isDark ? '라이트 모드로 전환' : '다크 모드로 전환');
        }
    }

    applyTheme(initialTheme);

    if (toggle) {
        toggle.addEventListener('click', function () {
            applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark');
        });
    }
})();
