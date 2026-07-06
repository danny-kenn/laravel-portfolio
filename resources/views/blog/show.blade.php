<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} – Blog</title>
    <meta name="description" content="{{ Str::limit(strip_tags($post->excerpt ?? $post->body), 160) }}">

    @include('partials.theme-init')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link rel="stylesheet" href="{{ asset('frontend/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}">

    <style>
        .post-wrap { padding: 2.5rem 2rem; }
        .post-content { padding: 2.5rem; }
        .post-content .meta { color: var(--text2); font-size: 0.9rem; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 1.2rem; }
        .post-content .meta .badge { background: var(--hover); color: var(--teal); border: 1px solid rgba(44,122,123,0.25); font-weight: 600; }
        .post-content h1 { font-family: var(--font-mono); font-weight: 700; font-size: clamp(1.7rem, 4vw, 2.3rem); color: var(--text); margin-bottom: 0.5rem; }
        .post-content .body { margin-top: 2rem; line-height: 1.85; font-size: 1.06rem; color: var(--text); }
        .post-content .body p { margin-bottom: 1.2rem; }
        .post-content .body img { max-width: 100%; border-radius: 8px; margin: 1.5rem 0; }
        .post-content .body h2, .post-content .body h3 { margin-top: 2rem; font-weight: 700; font-family: var(--font-mono); }
        .post-content .body a { color: var(--gold); }

        .post-tags .tag {
            display: inline-block; background: var(--hover); color: var(--teal);
            padding: 0.2rem 0.9rem; border-radius: 20px; font-size: 0.78rem;
            border: 1px solid rgba(44,122,123,0.2); margin-right: 6px; margin-bottom: 6px;
        }

        .sidebar-card { padding: 1.5rem; margin-bottom: 1.5rem; }
        .sidebar-card h6 { font-weight: 700; font-family: var(--font-mono); border-bottom: 1px solid var(--border); padding-bottom: 0.7rem; margin-bottom: 0.7rem; color: var(--text); }
        .sidebar-card ul { list-style: none; padding: 0; margin: 0; }
        .sidebar-card ul li { padding: 0.4rem 0; border-bottom: 1px solid var(--border); }
        .sidebar-card ul li:last-child { border-bottom: none; }
        .sidebar-card ul li a { color: var(--text2); text-decoration: none; font-size: 0.92rem; }
        .sidebar-card ul li a:hover { color: var(--text); }

        .related-card { 
            padding: 1rem; 
            cursor: pointer; 
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }
        .related-card:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
        .related-card .related-img { 
            width: 100%; 
            height: 120px; 
            object-fit: cover; 
            border-radius: 6px; 
            margin-bottom: 0.5rem;
            background: var(--bg3);
        }
        .related-card .related-placeholder {
            height: 120px;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--teal-soft), var(--gold-soft));
        }
        .related-card h6 { font-weight: 600; color: var(--text); margin-bottom: 0.2rem; font-size: 0.95rem; }
        .related-card small { color: var(--text3); font-size: 0.75rem; }

        footer.post-footer { padding: 2rem 0; border-top: 1px solid var(--border); text-align: center; color: var(--text3); }

        /* 🔥 Mobile: horizontal scroll for related posts */
        @media (max-width: 576px) {
            .related-grid {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 1rem;
                padding-bottom: 1rem;
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
            }
            .related-grid .related-col {
                flex: 0 0 75%;
                max-width: 75%;
                scroll-snap-align: start;
            }
            .related-grid::-webkit-scrollbar {
                height: 4px;
            }
            .related-grid::-webkit-scrollbar-thumb {
                background: var(--teal);
                border-radius: 4px;
            }
            .related-grid::-webkit-scrollbar-track {
                background: var(--border);
                border-radius: 4px;
            }
            .post-wrap { padding: 1.5rem 1rem; }
            .post-content { padding: 1.5rem; }
        }
    </style>
</head>
<body>

@include('partials.sidebar', ['active' => 'blog'])

<main class="main-content">
    <div class="post-wrap">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="ui-card post-content" data-aos="fade-up">
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

                <div class="mt-4">
                    <a href="{{ route('blog.index') }}" class="ui-btn-outline"><i class="fas fa-arrow-left me-2"></i>Back to Blog</a>
                </div>

                @if($relatedPosts->count() > 0)
                <div class="mt-5">
                    <h5 style="font-weight:700;color:var(--text);font-family:var(--font-mono)">Related Posts</h5>
                    <div class="related-grid row mt-3">
                        @foreach($relatedPosts as $related)
                        <div class="related-col mb-3">
                            <div class="ui-card related-card" onclick="window.location.href='{{ route('blog.show', $related->slug) }}'">
                                @if($related->featured_image)
                                <img src="{{ $related->featured_image }}" alt="{{ $related->title }}" class="related-img" onerror="this.style.display='none'; this.parentElement.querySelector('.related-placeholder')?.style.removeProperty('display');">
                                <div class="related-placeholder" style="display:none;">
                                    <i class="fas fa-newspaper" style="color:var(--gold);opacity:0.4;"></i>
                                </div>
                                @else
                                <div class="related-placeholder">
                                    <i class="fas fa-newspaper" style="color:var(--gold);opacity:0.4;"></i>
                                </div>
                                @endif
                                <h6>{{ $related->title }}</h6>
                                <small>{{ $related->published_at ? $related->published_at->format('M d, Y') : $related->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar widgets -->
            <div class="col-lg-4">
                <div class="ui-card sidebar-card" data-aos="fade-up">
                    <h6><i class="fas fa-book-open me-2" style="color:var(--gold)"></i>Recent Posts</h6>
                    <ul>
                        @foreach($recentPosts as $recent)
                        <li><a href="{{ route('blog.show', $recent->slug) }}">{{ $recent->title }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="ui-card sidebar-card" data-aos="fade-up" data-aos-delay="80">
                    <h6><i class="fas fa-link me-2" style="color:var(--gold)"></i>Quick Links</h6>
                    <ul>
                        <li><a href="{{ route('blog.index') }}"><i class="fas fa-arrow-left me-2"></i>All Posts</a></li>
                        <li><a href="{{ url('/') }}"><i class="fas fa-house me-2"></i>Back to Portfolio</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <footer class="post-footer">
        <p class="mb-0">© {{ date('Y') }} {{ $user->full_name ?? 'David Kiamba' }}. Built with ❤️</p>
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script src="{{ asset('frontend/js/theme.js') }}"></script>
<script>document.addEventListener('DOMContentLoaded', () => AOS.init({ duration: 700, once: true, offset: 60 }));</script>
</body>
</html>