// Управляет переключением светлой и тёмной темы и сохраняет выбор пользователя.

(() => {
    const root = document.documentElement;
    const button = document.getElementById('theme-toggle');
    const storageKey = 'airclick-theme';

    function readTheme() {
        try {
            const saved = localStorage.getItem(storageKey);
            return saved === 'dark' || saved === 'light' ? saved : 'light';
        } catch (error) {
            return 'light';
        }
    }

    function applyTheme(theme) {
        const normalizedTheme = theme === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-theme', normalizedTheme);

        if (button) {
            const darkEnabled = normalizedTheme === 'dark';
            button.textContent = darkEnabled ? '☀️' : '🌙';
            button.setAttribute('aria-label', darkEnabled ? 'Включить светлую тему' : 'Включить тёмную тему');
            button.setAttribute('title', darkEnabled ? 'Светлая тема' : 'Тёмная тема');
            button.setAttribute('aria-pressed', String(darkEnabled));
        }
    }

    let currentTheme = readTheme();
    applyTheme(currentTheme);

    if (button) {
        button.addEventListener('click', () => {
            currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(currentTheme);

            try {
                localStorage.setItem(storageKey, currentTheme);
            } catch (error) {
                // Переключение продолжит работать и без сохранения в хранилище.
            }
        });
    }
})();
