<!DOCTYPE html>
<html lang="en" data-theme="{{ session('theme', 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} – Blog</title>
    <meta name="description" content="{{ Str::limit(strip_tags($post->excerpt ?? $post->body), 160) }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}">
    <style>
        /* ── DARK MODE FIXES ─────────────────────────── */
        [data-theme="dark"] {
            --bg: #0d1117;
            --bg2: #161b22;
            --bg3: #1c2333;
            --border: rgba(255,255,255,0.07);
            --text: #e6edf3;
            --text2: #8b949e;
            --text3: #484f58;
            --hover: rgba(44,122,123,0.12);
            --teal: #2c7a7b;
            --gold: #d4a017;
        }
        [data-theme="light"] {
            --bg: #f6f8fa;
            --bg2: #ffffff;
            --bg3: #f0f2f5;
            --border: rgba(0,0,0,0.08);
            --text: #1f2328;
            --text2: #656d76;
            --text3: #9198a1;
            --hover: rgba(44,122,123,0.06);
            --teal: #2c7a7b;
            --gold: #d4a017;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            transition: background 0.3s, color 0.3s;
        }

        /* ── SIDEBAR ──────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background: var(--bg2);
            border-right: 1px solid var(--border);
            padding: 2rem 1.5rem;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }
        .sidebar-brand {
            font-family: 'Roboto Mono', monospace;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--gold);
            margin-bottom: 2rem;
            display: block;
            text-decoration: none;
        }
        .sidebar-brand i { color: var(--gold); margin-right: 8px; }
        .sidebar-nav { list-style: none; padding: 0; }
        .sidebar-nav li { margin-bottom: 4px; }
        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            color: var(--text2);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav li a:hover,
        .sidebar-nav li a.active {
            color: var(--text);
            background: var(--hover);
            border-left-color: var(--teal);
        }
        .sidebar-nav li a i { width: 18px; text-align: center; font-size: 0.85rem; }
        .sidebar-nav .nav-label {
            padding: 0.8rem 0.8rem 0.3rem;
            font-size: 0.65rem;
            text-transform: uppercase;
            color: var(--text3);
            letter-spacing: 1px;
            font-weight: 600;
        }
        .sidebar-toggle {
            display: none;
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            color: var(--text);
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all 0.3s;
        }
        .sidebar-toggle:hover { transform: scale(1.05); }

        /* ── MAIN CONTENT ────────────────────────────── */
        .main-content { margin-left: 240px; padding: 2rem; min-height: 100vh; }

        /* ── POST CONTENT ────────────────────────────── */
        .post-content {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2.5rem;
        }
        .post-content .meta {
            color: var(--text2);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 1.2rem;
        }
        .post-content .meta .badge {
            background: var(--hover);
            color: var(--teal);
            border: 1px solid rgba(44,122,123,0.25);
            font-weight: 600;
        }
        .post-content h1 {
            font-family: 'Roboto Mono', monospace;
            font-weight: 700;
            font-size: clamp(1.7rem, 4vw, 2.3rem);
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        .post-content .body {
            margin-top: 2rem;
            line-height: 1.85;
            font-size: 1.06rem;
            color: var(--text);
        }
        .post-content .body p { margin-bottom: 1.2rem; }
        .post-content .body img {
            max-width: 100%;
            border-radius: 8px;
            margin: 1.5rem 0;
        }
        .post-content .body h2, .post-content .body h3 {
            margin-top: 2rem;
            font-weight: 700;
            font-family: 'Roboto Mono', monospace;
        }
        .post-content .body a { color: var(--gold); }

        .post-tags .tag {
            display: inline-block;
            background: var(--hover);
            color: var(--teal);
            padding: 0.2rem 0.9rem;
            border-radius: 20px;
            font-size: 0.78rem;
            border: 1px solid rgba(44,122,123,0.2);
            margin-right: 6px;
            margin-bottom: 6px;
        }

        /* ── SIDEBAR CARD ────────────────────────────── */
        .sidebar-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .sidebar-card h6 {
            font-weight: 700;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            padding-bottom: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .sidebar-card ul { list-style: none; padding: 0; margin: 0; }
        .sidebar-card ul li { padding: 0.4rem 0; border-bottom: 1px solid var(--border); }
        .sidebar-card ul li:last-child { border-bottom: none; }
        .sidebar-card ul li a { color: var(--text2); text-decoration: none; transition: color 0.2s; }
        .sidebar-card ul li a:hover { color: var(--text); }

        /* ── RELATED POSTS ────────────────────────────── */
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .related-card {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .related-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .related-card h6 {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text);
            margin-bottom: 0.2rem;
        }
        .related-card small { color: var(--text3); font-size: 0.78rem; }

        /* ── RESPONSIVE ──────────────────────────────── */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-toggle { display: flex; align-items: center; justify-content: center; }
            .main-content { margin-left: 0; padding: 1.5rem 1rem; }
        }
        @media (max-width: 768px) {
            .related-grid { grid-template-columns: repeat(2, 1fr); }
            .post-content { padding: 1.5rem; }
        }
        @media (max-width: 480px) {
            .related-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR TOGGLE (Mobile) -->
<button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <a href="{{ url('/') }}" class="sidebar-brand">
        <i class="fas fa-code"></i> David Kiamba
    </a>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <li><a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="{{ url('/') }}#about"><i class="fas fa-user"></i> About</a></li>
        <li><a href="{{ url('/') }}#projects"><i class="fas fa-project-diagram"></i> Projects</a></li>
        <li><a href="{{ route('blog.index') }}"><i class="fas fa-newspaper"></i> Blog</a></li>
        <li><a href="{{ url('/') }}#contact"><i class="fas fa-envelope"></i> Contact</a></li>

        <div class="nav-label mt-3">Theme</div>
        <li>
            <button id="theme-toggle" style="background:none;border:none;color:var(--text2);display:flex;align-items:center;gap:10px;padding:0.6rem 0.8rem;width:100%;border-radius:8px;cursor:pointer;font-size:0.9rem;font-family:inherit;">
                <i class="fas fa-moon" id="themeIcon"></i>
                <span id="themeLabel">Dark</span>
            </button>
        </li>

        <div class="nav-label mt-3">Resume</div>
        <li><a href="{{ asset('frontend/resume.html') }}"><i class="fas fa-file-alt"></i> View Resume</a></li>
        <li><a href="{{ asset('frontend/resume.pdf') }}" download><i class="fas fa-download"></i> Download PDF</a></li>
    </nav>
</aside>

<!-- MAIN CONTENT -->
<main class="main-content">
    <div class="row">
        <!-- Post Content -->
        <div class="col-lg-8">
            <div class="post-content">
                <div class="meta">
                    <span><i class="far fa-calendar me-1"></i>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                    <span><i class="far fa-eye me-1"></i>{{ $post->view_count ?? 0 }} views</span>
                    @foreach($post->categories as $cat)
                    <span class="badge">{{ $cat->name }}</span>
                    @endforeach
                </div>
                <h1>{{ $post->title }}</h1>
                <div class="body">
                    {!! $post->body !!}
                </div>

                @if($post->tags && $post->tags->count() > 0)
                <div class="post-tags mt-4">
                    <strong style="color:var(--text2)">Tags:</strong>
                    @foreach($post->tags as $tag)
                    <span class="tag">#{{ $tag->name }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Back to Blog -->
            <div class="mt-4">
                <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Blog
                </a>
            </div>

            <!-- Related Posts -->
            @if($relatedPosts->count() > 0)
            <div class="mt-5">
                <h5 style="font-weight:700;color:var(--text);">Related Posts</h5>
                <div class="related-grid">
                    @foreach($relatedPosts as $related)
                    <div class="related-card" onclick="window.location.href='{{ route('blog.show', $related->slug) }}'">
                        <h6>{{ $related->title }}</h6>
                        <small>{{ $related->published_at ? $related->published_at->format('M d, Y') : $related->created_at->format('M d, Y') }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sidebar-card">
                <h6>📖 Recent Posts</h6>
                <ul>
                    @foreach($recentPosts as $post)
                    <li><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="sidebar-card">
                <h6>🔗 Quick Links</h6>
                <ul>
                    <li><a href="{{ route('blog.index') }}"><i class="fas fa-arrow-left me-2"></i>All Posts</a></li>
                    <li><a href="{{ url('/') }}"><i class="fas fa-house me-2"></i>Back to Portfolio</a></li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── THEME ──────────────────────────────────────────────────
const html = document.documentElement;
const themeToggle = document.getElementById('theme-toggle');
const themeIcon = document.getElementById('themeIcon');
const themeLabel = document.getElementById('themeLabel');

function applyTheme(t) {
    html.setAttribute('data-theme', t);
    localStorage.setItem('blogTheme', t);
    themeIcon.className = t === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
    themeLabel.textContent = t === 'dark' ? 'Dark' : 'Light';
}
const savedTheme = localStorage.getItem('blogTheme') || 'dark';
applyTheme(savedTheme);

themeToggle.addEventListener('click', () => {
    const current = html.getAttribute('data-theme');
    applyTheme(current === 'dark' ? 'light' : 'dark');
});

// ── SIDEBAR TOGGLE ──────────────────────────────────────
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    if (window.innerWidth < 992 && sidebar.classList.contains('open')) {
        if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    }
});
</script>
</body>
</html>