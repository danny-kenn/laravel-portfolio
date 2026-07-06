/* frontend/js/theme.js
   Shared across index, blog index and blog show pages.
   Requires the markup produced by partials/sidebar.blade.php + partials/theme-init.blade.php */

document.addEventListener('DOMContentLoaded', () => {
    const html = document.documentElement;
    const themeToggle = document.getElementById('theme-toggle');
    const themeLabel = document.getElementById('themeLabel');

    function applyTheme(t) {
        html.setAttribute('data-theme', t);
        try { localStorage.setItem('theme', t); } catch (e) {}
        if (themeLabel) themeLabel.textContent = t === 'dark' ? 'Dark mode' : 'Light mode';
        // The sun/moon pill position and highlighted label are driven
        // purely by the [data-theme] attribute in CSS — nothing else to
        // toggle here, which keeps this in sync even if the attribute
        // changes from elsewhere (e.g. theme-init.blade.php on load).
    }

    // theme-init.blade.php already set data-theme before paint; just sync the toggle UI.
    applyTheme(html.getAttribute('data-theme') || 'dark');

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(next);
        });
    }

    // ── MOBILE SIDEBAR ──────────────────────────────────────
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');
    const overlay = document.getElementById('sidebarOverlay');

    function closeSidebar() {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('open');
        toggleBtn?.setAttribute('aria-expanded', 'false');
    }
    function openSidebar() {
        sidebar?.classList.add('open');
        overlay?.classList.add('open');
        toggleBtn?.setAttribute('aria-expanded', 'true');
    }

    toggleBtn?.addEventListener('click', () => {
        sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // Esc closes the mobile sidebar too, for keyboard users.
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar?.classList.contains('open')) closeSidebar();
    });

    // Close sidebar automatically after tapping a nav link on mobile
    if (window.innerWidth < 992) {
        document.querySelectorAll('.sidebar-nav a').forEach(a => a.addEventListener('click', closeSidebar));
    }
});