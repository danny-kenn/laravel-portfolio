<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->full_name ?? 'David Kiamba Ngunzu' }} | Software Engineer & Quality Systems Professional</title>
    <meta name="description" id="metaDesc" content="{{ $profile->tagline ?? 'David Kiamba Ngunzu — Software Engineer, Full-Stack Developer, QSK Member' }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    @include('partials.theme-init')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link rel="stylesheet" href="{{ asset('frontend/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}">

    <style>
        /* ── HERO ─────────────────────────────────────────────── */
        #particles-js { position: fixed; inset: 0; left: var(--sidebar-w); z-index: 0; pointer-events: none; }
        @media (max-width: 991px) { #particles-js { left: 0; } }

        .hero {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            text-align: center;
            position: relative; z-index: 1;
            padding: 6rem 1.5rem 3rem;
            background: radial-gradient(circle at 20% 20%, var(--teal-soft), transparent 55%),
                        radial-gradient(circle at 80% 70%, var(--gold-soft), transparent 55%);
        }
        .hero-eyebrow {
            font-family: var(--font-mono); font-size: 0.8rem; letter-spacing: 3px;
            text-transform: uppercase; color: var(--teal); font-weight: 700; margin-bottom: 1rem;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .hero-eyebrow .dot { width: 8px; height: 8px; border-radius: 50%; background: #2ecc71; box-shadow: 0 0 8px #2ecc71; }
        .hero h1 {
            font-family: var(--font-mono); font-weight: 700;
            font-size: clamp(2.4rem, 6vw, 4.4rem);
            margin-bottom: 0.9rem; color: var(--text); letter-spacing: -1px;
        }
        .hero h1 span { color: var(--gold); }
        .hero p.tagline { font-size: clamp(1.1rem, 2.4vw, 1.5rem); color: var(--text2); margin-bottom: 1.75rem; min-height: 2.2em; }
        .typing-effect::after { content: '|'; color: var(--gold); animation: blink 0.9s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }

        .availability-banner {
            background: var(--teal-soft); color: var(--teal);
            border: 1px solid rgba(44,122,123,0.35);
            border-radius: 30px; padding: 0.5rem 1.1rem;
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 0.88rem; font-weight: 600; margin-top: 0.5rem;
        }
        .availability-banner.unavailable { background: rgba(197,72,72,0.12); color: #e07a7a; border-color: rgba(197,72,72,0.3); }
        .availability-banner .pulse { width: 8px; height: 8px; border-radius: 50%; background: currentColor; }

        .hero-buttons { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; margin-top: 1.75rem; }

        /* ── SECTIONS ─────────────────────────────────────────── */
        .section { padding: 6.5rem 2rem; position: relative; z-index: 1; }
        .section:nth-of-type(even) { background: var(--bg2); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
        .section-head { text-align: center; margin-bottom: 3rem; }
        .section-head .section-title, .section-head .section-sub { margin-left: auto; margin-right: auto; }

        .ui-card .card-body { padding: 1.9rem; }
        .ui-card .card-title { font-family: var(--font-mono); font-weight: 700; font-size: 1.25rem; color: var(--text); }
        .ui-card .card-text { color: var(--text2); font-size: 1rem; line-height: 1.75; }

        /* Affiliations */
        .qsk-badge { background: linear-gradient(45deg, var(--teal), var(--gold)); color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 0.82rem; font-weight: 700; display: inline-block; margin-left: 8px; }
        .affiliation-icon { width: 64px; height: 64px; border-radius: 16px; background: var(--gold-soft); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: var(--gold); font-size: 1.6rem; }
        .affiliation-details ul { padding-left: 18px; margin: 0; text-align: left; }
        .affiliation-details li { margin-bottom: 6px; color: var(--text2); }
        .affiliation-details li::marker { color: var(--gold); }

        /* Skills */
        .skill-item { margin-bottom: 14px; }
        .skill-item small { color: var(--text2); }
        .skill-bar-track { background: var(--bg3); border-radius: 4px; height: 6px; overflow: hidden; }
        .skill-bar-fill { background: linear-gradient(90deg, var(--teal), var(--gold)); border-radius: 4px; height: 100%; transition: width 1.2s ease; }

        /* Certificates */
        .certificate-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 1.6rem; }
        .certificate-card { position: relative; cursor: pointer; overflow: hidden; }
        .certificate-img { height: 190px; object-fit: cover; width: 100%; background: var(--bg3); }
        .certificate-badge { position: absolute; top: 12px; right: 12px; background: rgba(13,17,23,0.75); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; z-index: 2; }
        .certificate-actions { display: flex; gap: 10px; margin-top: 1rem; }
        .certificate-modal .modal-content { background: var(--bg2); border: 1px solid var(--border); color: var(--text); }
        .certificate-modal-img { width: 100%; max-height: 75vh; object-fit: contain; border-radius: 8px; }

        /* Experience / Education / Projects */
        .timeline-item { border-left: 2px solid var(--border); padding-left: 1.5rem; position: relative; margin-bottom: 1.5rem; }
        .timeline-item::before { content: ''; position: absolute; left: -7px; top: 6px; width: 12px; height: 12px; border-radius: 50%; background: var(--teal); border: 2px solid var(--bg2); }
        .badge-tag { background: var(--hover); color: var(--teal); border: 1px solid rgba(44,122,123,0.25); font-weight: 600; padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; }

        /* Contact */
        .contact-info a { color: var(--gold); text-decoration: none; font-weight: 600; }
        .contact-info a:hover { text-decoration: underline; }
        .form-control {
            background: var(--surface); border: 1px solid var(--border); color: var(--text);
            border-radius: var(--radius-sm); padding: 0.75rem 1rem; font-size: 0.98rem;
        }
        .form-control::placeholder { color: var(--text3); }
        .form-control:focus { background: var(--bg2); border-color: var(--teal); box-shadow: 0 0 0 3px var(--teal-soft); color: var(--text); }

        .whatsapp-btn {
            position: fixed; bottom: 26px; right: 26px; z-index: 1000;
            width: 58px; height: 58px; border-radius: 50%;
            background: #25d366; color: #fff; display: flex; align-items: center; justify-content: center;
            font-size: 1.9rem; box-shadow: var(--shadow); transition: transform 0.25s;
        }
        .whatsapp-btn:hover { transform: scale(1.08); color: #fff; }

        footer { padding: 2.5rem 0; text-align: center; color: var(--text3); border-top: 1px solid var(--border); background: var(--bg); position: relative; z-index: 1; }

        #toast-container { position: fixed; top: 20px; right: 20px; z-index: 1200; }

        .badge-tag {
            display: inline-block;
            background: var(--hover);
            color: var(--teal);
            border: 1px solid rgba(44,122,123,0.25);
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
        }
        
        .cert-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 190px;
            background: var(--bg3);
        }
        .cert-placeholder i {
            color: var(--gold);
            opacity: 0.4;
            font-size: 2.5rem;
        }
    </style>
</head>
<body>

<div id="particles-js"></div>

@include('partials.sidebar', ['active' => 'home'])

<main class="main-content">

    <!-- Hero -->
    <section class="hero" id="hero">
        <div class="hero-content">
            <div class="hero-eyebrow"><span class="dot"></span> Available for opportunities</div>
            <h1 data-aos="fade-down" id="heroName">{{ $user->full_name ?? 'David Ngunzu Kiamba' }}</h1>
            <p class="tagline typing-effect" id="heroTagline" data-full-text="{{ $profile->tagline ?? 'Software Engineering Student' }}"></p>

            <div id="heroBanner">
                @if($profile && $profile->availability_note)
                    <div class="availability-banner {{ $profile->availability_status == 1 ? '' : 'unavailable' }}">
                        <span class="pulse"></span>{{ $profile->availability_note }}
                    </div>
                @endif
            </div>

            <div class="hero-buttons">
                <a href="{{ asset('frontend/resume.pdf') }}" class="ui-btn-primary" download>
                    <i class="fas fa-download me-2"></i>Download Resume
                </a>
                <a href="{{ asset('frontend/resume.html') }}" class="ui-btn-outline" target="_blank">
                    <i class="fas fa-eye me-2"></i>Preview Resume
                </a>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="section">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">Get to know me</div>
                <h2 class="section-title">About Me</h2>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="ui-card" data-aos="zoom-in">
                        <div class="card-body" id="aboutContent">
                            <p class="card-text mb-0">{{ $profile->bio ?? 'Software Engineer passionate about building quality solutions.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Education -->
    <section id="education" class="section">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">Academic background</div>
                <h2 class="section-title">Education &amp; Certifications</h2>
            </div>
            <div class="row" id="educationContent">
                @forelse($education as $e)
                <div class="col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 80 }}">
                    <div class="ui-card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $e->degree }}</h5>
                            <p class="card-text mb-1"><strong style="color:var(--text)">{{ $e->institution }}</strong></p>
                            <p class="card-text mb-2" style="color:var(--text3);font-size:0.9rem">{{ $e->start_year }} – {{ $e->end_year ?? 'Present' }}</p>
                            <p class="card-text mb-0"><small>{{ $e->description ?? '' }}</small></p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center" style="color:var(--text3)">No education entries yet.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Affiliations -->
    <section id="affiliations" class="section">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">Professional standing</div>
                <h2 class="section-title">Affiliations</h2>
            </div>
            @forelse($affiliations as $aff)
            <div class="row justify-content-center mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 80 }}">
                <div class="col-lg-8">
                    <div class="ui-card">
                        <div class="card-body text-center">
                            <div class="affiliation-icon">
                                <i class="{{ $aff->icon_class ?? 'fas fa-users' }}"></i>
                            </div>
                            <h4 class="card-title mb-1">{{ $aff->organization }}
                                @if($aff->badge_text)<span class="qsk-badge">{{ $aff->badge_text }}</span>@endif
                            </h4>
                            <p class="card-text mb-3">{{ $aff->description ?? '' }}</p>
                            <div class="affiliation-details">
                                <div class="row mb-2">
                                    <div class="col-md-6"><small style="color:var(--text3)">Status</small><p class="mb-0" style="color:var(--text)">{{ $aff->status ?? '–' }}</p></div>
                                    <div class="col-md-6"><small style="color:var(--text3)">Member Since</small><p class="mb-0" style="color:var(--text)">{{ $aff->member_since ?? '–' }}</p></div>
                                </div>
                                @if($aff->benefits)
                                <p class="mt-3 mb-1"><strong style="color:var(--text)">Benefits</strong></p>
                                <ul>
                                    @foreach($aff->benefits as $benefit)
                                    <li>{{ $benefit }}</li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center" style="color:var(--text3)">No affiliations yet.</div>
            @endforelse
        </div>
    </section>

    <!-- Experience -->
    <section id="experience" class="section">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">Where I've worked</div>
                <h2 class="section-title">Work Experience</h2>
            </div>
            <div class="row" id="experienceContent">
                <div class="col-lg-8 mx-auto">
                    @forelse($experiences as $exp)
                    <div class="timeline-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                        <div class="ui-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between flex-wrap align-items-start mb-1">
                                    <h5 class="card-title mb-0">{{ $exp->job_title }}</h5>
                                    @if($exp->is_current == 1)<span class="badge-tag">Present</span>@endif
                                </div>
                                <p class="mb-1" style="color:var(--gold);font-weight:600">{{ $exp->company }}</p>
                                <p style="color:var(--text3);font-size:0.88rem" class="mb-2">{{ $exp->start_date }} – {{ $exp->is_current == 1 ? 'Present' : $exp->end_date }}</p>
                                <p class="card-text mb-0">{{ $exp->description ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center" style="color:var(--text3)">No experience entries yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- Skills -->
    <section id="skills" class="section">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">What I bring</div>
                <h2 class="section-title">Skills &amp; Expertise</h2>
            </div>
            <div class="row" id="skillsContent">
                @php $skillsByCategory = $skills->groupBy('category'); @endphp
                @forelse($skillsByCategory as $category => $categorySkills)
                <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 80 }}">
                    <div class="ui-card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ $category }}</h5>
                            @foreach($categorySkills as $skill)
                            <div class="skill-item">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>{{ $skill->skill_name }}</small>
                                    <small>{{ $skill->proficiency_level }}%</small>
                                </div>
                                <div class="skill-bar-track">
                                    <div class="skill-bar-fill" data-width="{{ $skill->proficiency_level }}%" style="width:0"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center" style="color:var(--text3)">No skills added yet.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Certificates -->
    <section id="certificates" class="section">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">Verified credentials</div>
                <h2 class="section-title">Certificates</h2>
                <p class="section-sub mx-auto">Click any certificate to preview, download, or view full details.</p>
            </div>
            <div class="certificate-grid" id="certsContent">
                @forelse($certificates as $cert)
                <?php
                    // 🔥 Build image URL - check if file exists
                    $imgSrc = '';
                    $hasValidImage = false;
                    if ($cert->image_path) {
                        if (str_starts_with($cert->image_path, 'certificates/')) {
                            $imgSrc = asset($cert->image_path);
                        } else {
                            $imgSrc = asset('certificates/' . $cert->image_path);
                        }
                        // Check if it's an old missing file (from previous system)
                        $isOldMissing = $cert->image_path && str_contains($cert->image_path, 'cert_6a211');
                        $hasValidImage = !$isOldMissing;
                    }
                    
                    $pdfSrc = '';
                    if ($cert->pdf_path) {
                        if (str_starts_with($cert->pdf_path, 'certificates/')) {
                            $pdfSrc = asset($cert->pdf_path);
                        } else {
                            $pdfSrc = asset('certificates/' . $cert->pdf_path);
                        }
                    }
                ?>
                <div class="certificate-card ui-card" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 60 }}" onclick="previewCert({{ $cert->id }})">
                    @if($cert->badge_label)<span class="certificate-badge">{{ $cert->badge_label }}</span>@endif
                    @if($hasValidImage && $imgSrc)
                        <img src="{{ $imgSrc }}" alt="{{ $cert->title }}" class="certificate-img" 
                             onerror="this.style.display='none'; this.parentElement.querySelector('.cert-placeholder')?.style.removeProperty('display');">
                        <div class="cert-placeholder" style="display:none;">
                            <i class="fas fa-certificate"></i>
                        </div>
                    @else
                        <div class="cert-placeholder">
                            <i class="fas fa-certificate"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $cert->title }}</h5>
                        <p class="card-text">{{ $cert->description ?? '' }}</p>
                        <div class="certificate-actions">
                            <button class="ui-btn-outline btn-sm" onclick="event.stopPropagation(); previewCert({{ $cert->id }})"><i class="fas fa-eye me-1"></i>Preview</button>
                            @if($pdfSrc)
                                <a href="{{ $pdfSrc }}" class="ui-btn-outline btn-sm" download="{{ Str::slug($cert->title) }}.pdf" onclick="event.stopPropagation()"><i class="fas fa-download me-1"></i>PDF</a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center" style="color:var(--text3)">No certificates yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Projects -->
    <section id="projects" class="section">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">Things I've built</div>
                <h2 class="section-title">Projects</h2>
            </div>
            <div class="row" id="projectsContent">
                <div class="col-lg-8 mx-auto">
                    @forelse($projects as $p)
                    <div class="ui-card mb-3" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 80 }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $p->is_featured == 1 ? '⭐ ' : '' }}{{ $p->title }}</h5>
                            <p class="card-text">{{ $p->description ?? '' }}</p>
                            <div class="mb-3">
                                @foreach($p->tags as $tag)
                                    <span class="badge-tag me-1">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                            @if($p->github_url)
                                <a href="{{ $p->github_url }}" target="_blank" class="ui-btn-outline btn-sm me-2"><i class="fab fa-github me-1"></i>GitHub</a>
                            @endif
                            @if($p->live_url)
                                <a href="{{ $p->live_url }}" target="_blank" class="ui-btn-primary btn-sm"><i class="fas fa-external-link-alt me-1"></i>Live Demo</a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center" style="color:var(--text3)">No projects yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="section">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">Let's talk</div>
                <h2 class="section-title">Contact Me</h2>
            </div>
            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="ui-card h-100" data-aos="zoom-in">
                        <div class="card-body" id="contactInfo">
                            <h5 class="card-title mb-3">Get in Touch</h5>
                            <p class="contact-info mb-0" style="line-height:2.1">
                                @if($profile && $profile->address)<i class="fas fa-location-dot me-2" style="color:var(--teal)"></i>{{ $profile->address }}<br>@endif
                                @if($profile && $profile->phone)<i class="fas fa-phone me-2" style="color:var(--teal)"></i><a href="tel:{{ $profile->phone }}">{{ $profile->phone }}</a><br>@endif
                                @if($user && $user->email)<i class="fas fa-envelope me-2" style="color:var(--teal)"></i><a href="mailto:{{ $user->email }}">{{ $user->email }}</a><br>@endif
                                @if($profile && $profile->github_url)<i class="fab fa-github me-2" style="color:var(--teal)"></i><a href="{{ $profile->github_url }}" target="_blank">{{ $profile->github_url }}</a>@endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 mb-4">
                    <div class="ui-card h-100" data-aos="zoom-in" data-aos-delay="100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Send a Message</h5>
                            <div id="contactFormMsg"></div>
                            <form id="contactForm">
                                @csrf
                                <div class="mb-3"><input type="text" class="form-control w-100" name="name" placeholder="Your Name" required></div>
                                <div class="mb-3"><input type="email" class="form-control w-100" name="email" placeholder="Your Email" required></div>
                                <div class="mb-3"><textarea class="form-control w-100" name="message" rows="4" placeholder="Your message..." required></textarea></div>
                                <button type="submit" class="ui-btn-primary w-100" id="submitBtn"><i class="fas fa-paper-plane me-2"></i>Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- Certificate Preview Modal -->
<div class="modal fade certificate-modal" id="certificateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="border-color:var(--border)">
                <h5 class="modal-title" id="certModalTitle">Certificate Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:var(--btn-close-filter, none)"></button>
            </div>
            <div class="modal-body text-center" id="certModalBody">
                <img id="certModalImg" src="" alt="Certificate" class="certificate-modal-img" style="display:block;max-width:100%;max-height:70vh;margin:0 auto;">
                <p id="certModalDesc" class="mt-3 mb-0" style="color:var(--text2)"></p>
            </div>
            <div class="modal-footer justify-content-center" style="border-color:var(--border)">
                <a id="certModalPdf" href="#" class="ui-btn-primary" download style="display:none"><i class="fas fa-download me-2"></i>Download PDF</a>
                <button type="button" class="ui-btn-outline" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Button -->
@if($profile && $profile->whatsapp)
<a id="whatsappBtn" href="https://wa.me/{{ $profile->whatsapp }}?text=Hello%20{{ urlencode($user->full_name ?? 'David') }},%20I%20am%20contacting%20you%20regarding%20an%20internship%20opportunity" class="whatsapp-btn" target="_blank" title="Message on WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>
@endif

<footer>
    <div class="container">
        <p id="footerText" class="mb-0">© {{ date('Y') }} {{ $user->full_name ?? 'David Ngunzu Kiamba' }} · Software Engineer &amp; QSK Member · All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/ScrollTrigger.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script src="{{ asset('frontend/js/theme.js') }}"></script>
<script>
    // ── CERTIFICATES ────────────────────────────────────────
    window._certs = @json($certificates);

    function previewCert(id) {
        const c = window._certs.find(x => x.id == id);
        if (!c) return;
        
        document.getElementById('certModalTitle').textContent = c.title;
        document.getElementById('certModalDesc').textContent = c.description || '';
        
        const img = document.getElementById('certModalImg');
        const pdfBtn = document.getElementById('certModalPdf');
        
        // 🔥 Remove existing placeholder
        const body = document.getElementById('certModalBody');
        const existingPlaceholder = body.querySelector('.no-image-placeholder');
        if (existingPlaceholder) existingPlaceholder.remove();
        
        // 🔥 Build image URL
        let imageUrl = '';
        let hasValidImage = false;
        if (c.image_path) {
            if (c.image_path.startsWith('certificates/')) {
                imageUrl = '{{ url('/') }}' + '/' + c.image_path;
            } else {
                imageUrl = '{{ url('/') }}' + '/certificates/' + c.image_path;
            }
            // Check if it's an old missing file
            const isOldMissing = c.image_path && c.image_path.includes('cert_6a211');
            hasValidImage = !isOldMissing;
        }
        
        // 🔥 Show image or placeholder
        if (imageUrl && hasValidImage) {
            img.src = imageUrl;
            img.style.display = 'block';
            img.onerror = function() {
                this.style.display = 'none';
                showPlaceholder(body, 'Image file not found for this certificate');
            };
            // Verify image exists
            fetch(imageUrl, { method: 'HEAD' })
                .then(response => {
                    if (!response.ok) {
                        img.onerror();
                    }
                })
                .catch(() => img.onerror());
        } else {
            img.style.display = 'none';
            showPlaceholder(body, 'No image available for this certificate');
        }
        
        // 🔥 Handle PDF button
        let pdfUrl = '';
        if (c.pdf_path) {
            if (c.pdf_path.startsWith('certificates/')) {
                pdfUrl = '{{ url('/') }}' + '/' + c.pdf_path;
            } else {
                pdfUrl = '{{ url('/') }}' + '/certificates/' + c.pdf_path;
            }
            pdfBtn.href = pdfUrl;
            pdfBtn.download = (c.title || 'certificate').replace(/[^a-z0-9]/gi, '_') + '.pdf';
            pdfBtn.style.display = 'inline-block';
        } else {
            pdfBtn.style.display = 'none';
        }
        
        new bootstrap.Modal(document.getElementById('certificateModal')).show();
    }

    function showPlaceholder(parent, message) {
        const placeholder = document.createElement('div');
        placeholder.className = 'no-image-placeholder';
        placeholder.style.cssText = 'padding:3rem;color:var(--text2);text-align:center;';
        placeholder.innerHTML = '<i class="fas fa-image fa-3x d-block mb-3" style="opacity:0.3;"></i>' + message;
        parent.appendChild(placeholder);
    }

    // ── CONTACT FORM ────────────────────────────────────────
    document.getElementById('contactForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const msgDiv = document.getElementById('contactFormMsg');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending…';

        const fd = new FormData(this);
        try {
            const res = await fetch('{{ route("contact") }}', {
                method: 'POST',
                body: fd,
                headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
            });
            const json = await res.json();
            msgDiv.innerHTML = `<div class="alert alert-${json.success ? 'success' : 'danger'} py-2">${json.message || 'Message sent!'}</div>`;
            if (json.success) this.reset();
        } catch {
            msgDiv.innerHTML = '<div class="alert alert-danger py-2">Something went wrong. Try again.</div>';
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Message';
    });

    // ── TYPING EFFECT ───────────────────────────────────────
    (function typeTagline() {
        const el = document.getElementById('heroTagline');
        if (!el) return;
        const text = el.dataset.fullText || '';
        let i = 0;
        (function type() {
            if (i <= text.length) { el.textContent = text.slice(0, i); i++; setTimeout(type, 45); }
        })();
    })();

    // ── SKILL BARS ANIMATE ON SCROLL ────────────────────────
    const skillObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.width = entry.target.dataset.width;
                skillObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });
    document.querySelectorAll('.skill-bar-fill').forEach(el => skillObserver.observe(el));

    // ── SCROLLSPY: HIGHLIGHT ACTIVE SIDEBAR LINK ────────────
    const sections = document.querySelectorAll('main section[id]');
    const navLinks = document.querySelectorAll('.sidebar-nav a[data-scroll]');
    const spyObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const link = document.querySelector(`.sidebar-nav a[data-scroll="${entry.target.id}"]`);
            if (!link) return;
            if (entry.isIntersecting) {
                navLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            }
        });
    }, { rootMargin: '-40% 0px -50% 0px', threshold: 0 });
    sections.forEach(s => spyObserver.observe(s));

    // ── INIT ─────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        AOS.init({ duration: 700, once: true, offset: 80 });

        if (typeof particlesJS !== 'undefined') {
            particlesJS('particles-js', {
                particles: {
                    number: { value: 60, density: { enable: true, value_area: 900 } },
                    color: { value: '#2c7a7b' }, shape: { type: 'circle' },
                    opacity: { value: 0.35, random: true }, size: { value: 3, random: true },
                    line_linked: { enable: true, distance: 150, color: '#d4a017', opacity: 0.25, width: 1 },
                    move: { enable: true, speed: 1.2 }
                },
                interactivity: {
                    detect_on: 'canvas',
                    events: { onhover: { enable: true, mode: 'repulse' }, onclick: { enable: true, mode: 'push' } },
                    modes: { repulse: { distance: 90 }, push: { particles_nb: 3 } }
                },
                retina_detect: true
            });
        }

        if (typeof gsap !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
            gsap.from('.hero-content', { opacity: 0, y: 60, duration: 1, ease: 'power3.out' });
        }
    });
</script>
</body>
</html>