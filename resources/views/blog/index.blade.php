<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog – David Kiamba</title>
    <meta name="description" content="Thoughts, insights, and stories from my journey as a Software Engineer">

    @include('partials.theme-init')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link rel="stylesheet" href="{{ asset('frontend/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}">

    <style>
        .page-head { margin-bottom: 2.5rem; }
        .page-head h1 { font-family: var(--font-mono); font-weight: 700; font-size: 2.1rem; color: var(--text); margin-bottom: 0.4rem; }
        .page-head p { color: var(--text2); font-size: 1.05rem; }

        .blog-card { overflow: hidden; height: 100%; cursor: pointer; }
        .blog-card .card-body { padding: 1.5rem; }
        .blog-card .card-title { font-weight: 700; font-size: 1.15rem; color: var(--text); font-family: var(--font-mono); }
        .blog-card .card-text { color: var(--text2); font-size: 0.95rem; }
        .blog-card .meta { color: var(--text3); font-size: 0.8rem; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 0.6rem; }
        .blog-card .meta .badge { background: var(--hover); color: var(--teal); border: 1px solid rgba(44,122,123,0.25); font-weight: 600; }
        .blog-img { width: 100%; height: 210px; object-fit: cover; }
        .blog-img-placeholder { height: 210px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--teal-soft), var(--gold-soft)); }

        .sidebar-card { padding: 1.5rem; margin-bottom: 1.5rem; }
        .sidebar-card h6 { font-weight: 700; font-family: var(--font-mono); border-bottom: 1px solid var(--border); padding-bottom: 0.7rem; margin-bottom: 0.7rem; color: var(--text); }
        .sidebar-card ul { list-style: none; padding: 0; margin: 0; }
        .sidebar-card ul li { padding: 0.4rem 0; border-bottom: 1px solid var(--border); }
        .sidebar-card ul li:last-child { border-bottom: none; }
        .sidebar-card ul li a { color: var(--text2); text-decoration: none; font-size: 0.92rem; }
        .sidebar-card ul li a:hover { color: var(--text); }

        .empty-state { text-align: center; padding: 4rem 0; color: var(--text2); }
        .empty-state i { color: var(--text3); }

        .pagination { justify-content: center; margin-top: 2.5rem; }
        .pagination .page-link { background: var(--bg2); border: 1px solid var(--border); color: var(--text2); }
        .pagination .active .page-link { background: var(--teal); border-color: var(--teal); color: #fff; }
    </style>
</head>
<body>

<!-- 🔥 USE THE SHARED SIDEBAR -->
@include('partials.sidebar')

<main class="main-content">
    <div class="container-fluid" style="padding: 2.5rem 2rem;">
        <div class="page-head" data-aos="fade-up">
            <h1><i class="fas fa-newspaper me-2" style="color:var(--gold)"></i>Blog</h1>
            <p>Thoughts, insights, and stories from my journey as a software engineer.</p>
        </div>

        <div class="row">
            <!-- Blog Posts -->
            <div class="col-lg-9">
                <div class="row">
                    @forelse($posts as $post)
                    <div class="col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index % 4 * 60 }}">
                        <div class="ui-card blog-card" onclick="window.location.href='{{ route('blog.show', $post->slug) }}'">
                            @if($post->featured_image)
                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="blog-img">
                            @else
                            <div class="blog-img-placeholder"><i class="fas fa-newspaper fa-2x" style="color:var(--gold);opacity:0.5"></i></div>
                            @endif
                            <div class="card-body">
                                <div class="meta">
                                    <span><i class="far fa-calendar me-1"></i>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                                    @foreach($post->categories as $cat)
                                    <span class="badge">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                                <h5 class="card-title">{{ $post->title }}</h5>
                                <p class="card-text">{{ Str::limit(strip_tags($post->excerpt ?? $post->body), 120) }}</p>
                                <a href="{{ route('blog.show', $post->slug) }}" class="ui-btn-outline btn-sm mt-2">Read More <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 empty-state">
                        <i class="fas fa-newspaper fa-3x d-block mb-3"></i>
                        <p class="mb-0">No blog posts yet. Check back soon!</p>
                    </div>
                    @endforelse
                </div>
                {{ $posts->links() }}
            </div>

            <!-- Sidebar Widgets -->
            <div class="col-lg-3">
                @if($categories->count() > 0)
                <div class="ui-card sidebar-card" data-aos="fade-up">
                    <h6><i class="fas fa-folder-open me-2" style="color:var(--gold)"></i>Categories</h6>
                    <ul>
                        @foreach($categories as $cat)
                        <li><a href="#">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($recentPosts->count() > 0)
                <div class="ui-card sidebar-card" data-aos="fade-up" data-aos-delay="80">
                    <h6><i class="fas fa-book-open me-2" style="color:var(--gold)"></i>Recent Posts</h6>
                    <ul>
                        @foreach($recentPosts as $post)
                        <li><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script src="{{ asset('frontend/js/theme.js') }}"></script>
<script>document.addEventListener('DOMContentLoaded', () => AOS.init({ duration: 700, once: true, offset: 60 }));</script>
</body>
</html>