/**
 * APS Advertising - Language Toggle (English / Hindi)
 * Swipe-style toggle switch - works on desktop and mobile across all pages
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'apsPreferredLanguage';

    function getLang() {
        return localStorage.getItem(STORAGE_KEY) || 'en';
    }

    function setLang(lang) {
        localStorage.setItem(STORAGE_KEY, lang);
    }

    function applyTranslation(lang) {
        const elements = document.querySelectorAll('[data-en]');
        elements.forEach(el => {
            const text = el.dataset[lang];
            if (text) {
                if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                    el.placeholder = text;
                } else if (el.tagName === 'SELECT') {
                    const opts = el.querySelectorAll('option');
                    opts.forEach(opt => {
                        if (opt.dataset[lang]) opt.textContent = opt.dataset[lang];
                    });
                } else {
                    el.textContent = text;
                }
            }
        });
    }

    function getToggleHTML(extraClass) {
        const lang = getLang();
        const isHindi = lang === 'hi';
        return `
            <div class="aps-lang-toggle ${extraClass || ''}" role="switch" aria-checked="${isHindi}" aria-label="Language: English or Hindi">
                <button type="button" class="aps-lang-option" data-lang="en" aria-pressed="${!isHindi}">
                    <span>English</span>
                </button>
                <button type="button" class="aps-lang-option" data-lang="hi" aria-pressed="${isHindi}">
                    <span>हिन्दी</span>
                </button>
                <div class="aps-lang-slider ${isHindi ? 'aps-lang-hi' : 'aps-lang-en'}"></div>
            </div>
        `;
    }

    function initToggle(container) {
        if (!container) return;
        const lang = getLang();
        container.innerHTML = getToggleHTML(container.dataset.variant || '');
        const toggle = container.querySelector('.aps-lang-toggle');
        const slider = container.querySelector('.aps-lang-slider');
        const options = container.querySelectorAll('.aps-lang-option');

        function selectLang(l) {
            setLang(l);
            slider.classList.remove('aps-lang-en', 'aps-lang-hi');
            slider.classList.add(l === 'hi' ? 'aps-lang-hi' : 'aps-lang-en');
            options.forEach(btn => {
                btn.setAttribute('aria-pressed', btn.dataset.lang === l);
            });
            toggle.setAttribute('aria-checked', l === 'hi');
            applyTranslation(l);

            // Sync all toggles on page
            document.querySelectorAll('.aps-lang-toggle').forEach(t => {
                if (t === toggle) return;
                const s = t.querySelector('.aps-lang-slider');
                const opts = t.querySelectorAll('.aps-lang-option');
                if (s) {
                    s.classList.remove('aps-lang-en', 'aps-lang-hi');
                    s.classList.add(l === 'hi' ? 'aps-lang-hi' : 'aps-lang-en');
                }
                opts.forEach(btn => btn.setAttribute('aria-pressed', btn.dataset.lang === l));
                t.setAttribute('aria-checked', l === 'hi');
            });
        }

        options.forEach(btn => {
            btn.addEventListener('click', () => selectLang(btn.dataset.lang));
        });
    }

    function injectStyles() {
        if (document.getElementById('aps-lang-toggle-styles')) return;
        const css = `
            .aps-lang-toggle {
                display: inline-flex;
                position: relative;
                background: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
                border-radius: 999px;
                padding: 4px;
                gap: 2px;
                min-width: 130px;
            }
            .aps-lang-toggle .aps-lang-option {
                flex: 1;
                padding: 6px 12px;
                border: none;
                background: transparent;
                color: rgba(255,255,255,0.7);
                font-family: inherit;
                font-weight: 600;
                font-size: 0.8rem;
                cursor: pointer;
                border-radius: 999px;
                position: relative;
                z-index: 2;
                transition: color 0.25s ease;
            }
            .aps-lang-toggle .aps-lang-option[aria-pressed="true"] { color: #fff; }
            .aps-lang-toggle .aps-lang-slider {
                position: absolute;
                top: 4px;
                bottom: 4px;
                width: calc(50% - 6px);
                background: #D2042D;
                border-radius: 999px;
                z-index: 1;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .aps-lang-toggle .aps-lang-slider.aps-lang-en { transform: translateX(4px); }
            .aps-lang-toggle .aps-lang-slider.aps-lang-hi { transform: translateX(calc(100% + 6px)); }
            .aps-lang-toggle.mobile { min-width: 140px; }
            .aps-lang-toggle.mobile .aps-lang-option { padding: 10px 14px; font-size: 0.9rem; }
        `;
        const style = document.createElement('style');
        style.id = 'aps-lang-toggle-styles';
        style.textContent = css;
        document.head.appendChild(style);
    }

    function init() {
        injectStyles();
        applyTranslation(getLang());
        document.querySelectorAll('.aps-lang-toggle-container').forEach(initToggle);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.APSapplyLanguage = applyTranslation;
    window.APSgetLanguage = getLang;
})();
