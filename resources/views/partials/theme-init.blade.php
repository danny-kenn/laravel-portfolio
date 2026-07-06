{{-- resources/views/partials/theme-init.blade.php
     Include as the FIRST thing inside <head>, before any stylesheet.
     One localStorage key ("theme") is now shared by every public page. --}}
<script>
    (function () {
        try {
            var t = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        } catch (e) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    })();
</script>