document.addEventListener('DOMContentLoaded', () => {
    console.log('scripts.js loaded - FINAL FIX');

    // Initialize particles.js
    particlesJS('particles-js', {
        particles: {
            number: { value: 80, density: { enable: true, value_area: 800 } },
            color: { value: '#2c7a7b' },
            shape: { type: 'circle', stroke: { width: 0 } },
            opacity: { value: 0.4, random: true },
            size: { value: 3, random: true },
            line_linked: { enable: true, distance: 150, color: '#d4a017', opacity: 0.3, width: 1 },
            move: { enable: true, speed: 1.5, direction: 'none', random: false }
        },
        interactivity: {
            detect_on: 'canvas',
            events: { onhover: { enable: true, mode: 'repulse' }, onclick: { enable: true, mode: 'push' } },
            modes: { repulse: { distance: 100 }, push: { particles_nb: 4 } }
        },
        retina_detect: true
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Dark/Light Mode Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const themeIcon = themeToggle.querySelector('i');
    const currentTheme = localStorage.getItem('theme') || 'light-mode';
    body.classList.add(currentTheme);
    if (currentTheme === 'dark-mode') {
        themeIcon.classList.replace('fa-moon', 'fa-sun');
    }

    themeToggle.addEventListener('click', () => {
        body.classList.toggle('light-mode');
        body.classList.toggle('dark-mode');
        const isDarkMode = body.classList.contains('dark-mode');
        themeIcon.classList.replace(isDarkMode ? 'fa-moon' : 'fa-sun', isDarkMode ? 'fa-sun' : 'fa-moon');
        localStorage.setItem('theme', isDarkMode ? 'dark-mode' : 'light-mode');
    });

    // GSAP Typing Effect
    const text = 'Software Engineering Student | Aspiring Cybersecurity & software Systems Intern';
    const typingElement = document.querySelector('.typing-effect');
    if (typingElement) {
        let i = 0;
        function type() {
            if (i < text.length) {
                typingElement.textContent = text.slice(0, i + 1);
                i++;
                setTimeout(type, 80);
            }
        }
        type();
    }

    // GSAP Animations (optional - can be removed if causing issues)
    if (typeof gsap !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);

        // Hero content animation
        gsap.from('.hero-content', {
            opacity: 0,
            y: 80,
            duration: 1.2,
            ease: 'power3.out'
        });
    }

    // ============ SIMPLIFIED CERTIFICATE MODAL ============
    console.log('Setting up certificate modal...');
    
    // Get modal and elements
    const modalElement = document.getElementById('certificateModal');
    if (!modalElement) {
        console.error('Modal not found!');
        return;
    }
    
    const modal = new bootstrap.Modal(modalElement);
    const modalTitle = document.getElementById('certificateModalTitle');
    const modalImg = document.getElementById('certificateModalImg');
    const modalDesc = document.getElementById('certificateModalDescription');
    const modalDownload = document.getElementById('certificateModalDownload');
    
    // Function to open modal with certificate data
    function openCertificate(title, description, imageSrc, downloadUrl) {
        console.log(`Opening: ${title}`);
        
        // Set modal content
        if (modalTitle) modalTitle.textContent = title;
        if (modalDesc) modalDesc.textContent = description;
        
        // Set image
        if (modalImg) {
            modalImg.src = imageSrc;
            modalImg.onerror = function() {
                this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjI0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCxzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSIjNjY2IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Q2VydGlmaWNhdGUgUHJldmlldzwvdGV4dD48L3N2Zz4=';
            };
        }
        
        // Set download button
        if (modalDownload && downloadUrl) {
            modalDownload.href = downloadUrl;
            modalDownload.style.display = 'inline-block';
        } else {
            modalDownload.style.display = 'none';
        }
        
        // Show modal
        modal.show();
    }
    
    // Add click handlers to ALL preview buttons
    const previewButtons = document.querySelectorAll('.preview-btn');
    console.log(`Found ${previewButtons.length} preview buttons`);
    
    previewButtons.forEach(button => {
        // Remove any existing listeners
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        
        newButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get parent card
            const card = this.closest('.certificate-card');
            if (!card) return;
            
            // Get data from card
            const title = card.querySelector('.card-title')?.textContent || 'Certificate';
            const description = card.querySelector('.card-text')?.textContent || 'Preview';
            const image = card.querySelector('img')?.src || '';
            const downloadLink = card.querySelector('a[download]')?.href || '';
            
            // Open modal
            openCertificate(title, description, image, downloadLink);
        });
    });
    
    // Add click handlers to certificate cards
    const certificateCards = document.querySelectorAll('.certificate-card');
    certificateCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking on buttons or links
            if (e.target.closest('button') || e.target.closest('a')) {
                return;
            }
            
            // Find the preview button and click it
            const previewBtn = this.querySelector('.preview-btn');
            if (previewBtn) {
                previewBtn.click();
            }
        });
    });
    
    // SPECIAL DIRECT HANDLER FOR QSK (as backup)
    setTimeout(() => {
        const qskCard = document.querySelector('[data-certificate="qsk"]');
        if (qskCard) {
            console.log('QSK card found, adding direct handler');
            
            const qskBtn = qskCard.querySelector('.preview-btn');
            if (qskBtn) {
                // Add another click handler just for QSK
                qskBtn.addEventListener('click', function(e) {
                    console.log('DIRECT QSK HANDLER TRIGGERED!');
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    
                    // Direct QSK data
                    const title = 'Quality Society of Kenya';
                    const description = 'Full Membership Certificate - Professional Quality Association';
                    const image = 'frontend/certificates/qsk.png';
                    const downloadLink = 'frontend/certificates/qsk.pdf';
                    
                    openCertificate(title, description, image, downloadLink);
                }, true); // Use capture phase to ensure it fires
            }
        }
    }, 100);
    
    console.log('Certificate setup complete');
});