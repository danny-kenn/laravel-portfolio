{{--
    resources/views/partials/sidebar.blade.php

    Shared sidebar used on the portfolio homepage, blog index and blog post pages.
    Include with:  @include('partials.sidebar', ['active' => 'home'])
    $active accepts: home | about | education | affiliations | experience | skills | certificates | projects | contact | blog
--}}
@php
    $active = $active ?? null;
    $displayName = $user->full_name ?? 'David Kiamba';
    $initials = collect(preg_split('/\s+/', trim($displayName)))
        ->filter()
        ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');
    $initials = $initials !== '' ? $initials : 'DK';
@endphp

<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="sidebar">
    <i class="fas fa-bars"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" id="sidebarClose" aria-label="Close navigation">
        <i class="fas fa-xmark"></i>
    </button>

    <a href="{{ url('/') }}" class="sidebar-brand">
        <span class="brand-avatar" aria-hidden="true">{{ $initials }}</span>
        <span class="brand-meta">
            <span class="brand-text">{{ $displayName }}</span>
            <span class="brand-sub">Software Engineer</span>
        </span>
    </a>

    <nav class="sidebar-nav">
        <div class="nav-label">Navigate</div>
        <ul>
            <li><a href="{{ url('/') }}" class="{{ $active === 'home' ? 'active' : '' }}" data-scroll="hero"><i class="fas fa-house"></i><span>Home</span></a></li>
            <li><a href="{{ url('/') }}#about" class="{{ $active === 'about' ? 'active' : '' }}" data-scroll="about"><i class="fas fa-user"></i><span>About</span></a></li>
            <li><a href="{{ url('/') }}#education" class="{{ $active === 'education' ? 'active' : '' }}" data-scroll="education"><i class="fas fa-graduation-cap"></i><span>Education</span></a></li>
            <li><a href="{{ url('/') }}#affiliations" class="{{ $active === 'affiliations' ? 'active' : '' }}" data-scroll="affiliations"><i class="fas fa-users"></i><span>Affiliations</span></a></li>
            <li><a href="{{ url('/') }}#experience" class="{{ $active === 'experience' ? 'active' : '' }}" data-scroll="experience"><i class="fas fa-briefcase"></i><span>Experience</span></a></li>
            <li><a href="{{ url('/') }}#skills" class="{{ $active === 'skills' ? 'active' : '' }}" data-scroll="skills"><i class="fas fa-layer-group"></i><span>Skills</span></a></li>
            <li><a href="{{ url('/') }}#certificates" class="{{ $active === 'certificates' ? 'active' : '' }}" data-scroll="certificates"><i class="fas fa-certificate"></i><span>Certificates</span></a></li>
            <li><a href="{{ url('/') }}#projects" class="{{ $active === 'projects' ? 'active' : '' }}" data-scroll="projects"><i class="fas fa-diagram-project"></i><span>Projects</span></a></li>
            <li><a href="{{ url('/') }}#contact" class="{{ $active === 'contact' ? 'active' : '' }}" data-scroll="contact"><i class="fas fa-envelope"></i><span>Contact</span></a></li>
        </ul>

        <div class="nav-label mt-3">Read</div>
        <ul>
            <li><a href="{{ route('blog.index') }}" class="{{ $active === 'blog' ? 'active' : '' }}"><i class="fas fa-newspaper"></i><span>Blog</span></a></li>
        </ul>

        <div class="nav-label mt-3">Resume</div>
        <ul>
            <li><a href="{{ asset('frontend/resume.html') }}" target="_blank"><i class="fas fa-file-lines"></i><span>View Resume</span></a></li>
            <li><a href="{{ asset('frontend/resume.pdf') }}" download><i class="fas fa-download"></i><span>Download PDF</span></a></li>
        </ul>
    </nav>

    <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle light and dark theme">
        <span class="theme-toggle-track">
            <span class="theme-toggle-option" data-mode="light"><i class="fas fa-sun"></i></span>
            <span class="theme-toggle-option" data-mode="dark"><i class="fas fa-moon"></i></span>
            <span class="theme-toggle-pill"></span>
        </span>
        <span id="themeLabel">Dark mode</span>
    </button>
</aside>