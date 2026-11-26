/**
 * NovaPulse Theme JavaScript
 * Reading progress bar, dark/light mode toggle, scroll-to-top & UI interactions.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Dark / Light Mode Switcher
    const themeToggleBtn = document.getElementById('npThemeToggle');
    const themeIcon = document.getElementById('npThemeIcon');
    const currentTheme = localStorage.getItem('np_theme') || 
        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    document.documentElement.setAttribute('data-theme', currentTheme);
    updateThemeIcon(currentTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const active = document.documentElement.getAttribute('data-theme');
            const target = active === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', target);
            localStorage.setItem('np_theme', target);
            updateThemeIcon(target);
        });
    }

    function updateThemeIcon(theme) {
        if (!themeIcon) return;
        if (theme === 'dark') {
            themeIcon.className = 'ri-sun-line';
        } else {
            themeIcon.className = 'ri-moon-line';
        }
    }

    // 2. Reading Progress Indicator
    const progressBar = document.getElementById('reading-progress');
    window.addEventListener('scroll', () => {
        if (!progressBar) return;
        const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
        if (totalHeight > 0) {
            const progress = (window.scrollY / totalHeight) * 100;
            progressBar.style.width = Math.min(progress, 100) + '%';
        }
    }, { passive: true });

    // 3. Scroll to Top Button
    const scrollTopBtn = document.getElementById('npScrollTop');
    if (scrollTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        }, { passive: true });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // 4. Code Blocks Copy Snippet Button
    document.querySelectorAll('pre code').forEach((block) => {
        const pre = block.parentNode;
        if (!pre || pre.querySelector('.np-copy-btn')) return;

        const copyBtn = document.createElement('button');
        copyBtn.className = 'np-copy-btn';
        copyBtn.innerText = 'Copiar';
        copyBtn.style.cssText = 'position:absolute; top:8px; right:8px; font-size:12px; padding:2px 8px; border-radius:4px; border:1px solid #334155; background:#1e293b; color:#94a3b8; cursor:pointer;';

        pre.style.position = 'relative';
        pre.appendChild(copyBtn);

        copyBtn.addEventListener('click', () => {
            navigator.clipboard.writeText(block.innerText).then(() => {
                copyBtn.innerText = 'Copiado!';
                setTimeout(() => { copyBtn.innerText = 'Copiar'; }, 2000);
            });
        });
    });
});
