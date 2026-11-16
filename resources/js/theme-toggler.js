/**
 * Переключение светлой и тёмной темы
 *
 * Тема хранится в localStorage. Если выбора ещё не было, берём тот режим,
 * который стоит в системе, но дальше решает только кнопка — режима «авто» нет
 */

const KEY = 'theme';

function readTheme() {
    try {
        const stored = localStorage.getItem(KEY);

        if (stored === 'light' || stored === 'dark') {
            return stored;
        }
    } catch {
        // localStorage может быть недоступен, тогда просто спросим систему
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);

    document.querySelectorAll('[data-bs-theme-value]').forEach((toggle) => {
        toggle.classList.toggle('active', toggle.dataset.bsThemeValue === theme);
    });

    document.querySelectorAll('.js-theme-icon').forEach((icon) => {
        icon.className = 'js-theme-icon bi ' + (theme === 'dark' ? 'bi-moon-stars' : 'bi-sun');
    });

    const navbarBg = getComputedStyle(document.documentElement).getPropertyValue('--motor-navbar-bg').trim();

    if (navbarBg) {
        document.querySelector('meta[name="theme-color"]')?.setAttribute('content', navbarBg);
    }
}

applyTheme(readTheme());

document.addEventListener('DOMContentLoaded', () => {
    applyTheme(readTheme());

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-bs-theme-value]');

        if (! toggle) {
            return;
        }

        event.preventDefault();

        const theme = toggle.dataset.bsThemeValue === 'toggle'
            ? (document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark')
            : toggle.dataset.bsThemeValue;

        try {
            localStorage.setItem(KEY, theme);
        } catch {
            // Не смогли запомнить — тема продержится до конца страницы
        }

        applyTheme(theme);
    });
});
