// ============================================
// MarketStudent - Main JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', () => {

    // ── Cookie Consent ──
    const COOKIE_CONSENT_KEY = 'marketstudent_cookie_consent';
    const cookieBanner = document.getElementById('cookieConsent');

    if (cookieBanner) {
        const saved = localStorage.getItem(COOKIE_CONSENT_KEY);
        if (!saved) {
            cookieBanner.classList.add('show');
        }

        document.getElementById('cookieAccept')?.addEventListener('click', () => {
            localStorage.setItem(COOKIE_CONSENT_KEY, 'accepted');
            cookieBanner.classList.remove('show');
        });

        document.getElementById('cookieDecline')?.addEventListener('click', () => {
            localStorage.setItem(COOKIE_CONSENT_KEY, 'declined');
            cookieBanner.classList.remove('show');
        });
    }

    // ── Dropdown Toggle (Click-based) ──
    const dropdownBtn = document.getElementById('dropdownToggleBtn');
    const dropdownContainer = document.getElementById('userDropdown');

    if (dropdownBtn && dropdownContainer) {
        dropdownBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropdownContainer.classList.toggle('open');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownContainer.contains(e.target)) {
                dropdownContainer.classList.remove('open');
            }
        });

        // Close dropdown when pressing Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                dropdownContainer.classList.remove('open');
            }
        });
    }

    // ── Mobile Menu Toggle ──
    const menuToggle = document.getElementById('menuToggle');
    const navActions = document.getElementById('navActions');

    if (menuToggle && navActions) {
        menuToggle.addEventListener('click', () => {
            navActions.classList.toggle('mobile-open');
            menuToggle.classList.toggle('active');
        });
    }

    // ── Auto-dismiss flash alerts ──
    const flash = document.getElementById('flashAlert');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-10px)';
            setTimeout(() => flash.remove(), 300);
        }, 5000);
    }

    // ── Navbar shrink on scroll ──
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // ── Intersection Observer for scroll animations ──
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.product-card, .stat-card, .feature-card, .order-card, .verify-card, .action-card').forEach(el => {
        el.classList.add('animate-target');
        observer.observe(el);
    });

    // ── Smooth scroll for anchor links ──
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // ── Confirm dialogs with data-confirm ──
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // ── File input preview ──
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', (e) => {
            const preview = document.querySelector(input.dataset.preview);
            if (preview && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    });

    // ── Carousel Slider ──
    const carousel = document.getElementById('heroCarousel');
    if (carousel) {
        const slides = carousel.querySelectorAll('.carousel-slide');
        const dots = carousel.querySelectorAll('.carousel-dot');
        const prevBtn = carousel.querySelector('.carousel-prev');
        const nextBtn = carousel.querySelector('.carousel-next');
        let current = 0;
        let autoSlide;

        function goToSlide(index) {
            slides[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = (index + slides.length) % slides.length;
            slides[current].classList.add('active');
            dots[current].classList.add('active');
        }

        function startAuto() {
            autoSlide = setInterval(() => goToSlide(current + 1), 4000);
        }

        function stopAuto() {
            clearInterval(autoSlide);
        }

        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                stopAuto();
                goToSlide(parseInt(dot.dataset.slide));
                startAuto();
            });
        });

        if (prevBtn) prevBtn.addEventListener('click', () => { stopAuto(); goToSlide(current - 1); startAuto(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { stopAuto(); goToSlide(current + 1); startAuto(); });

        carousel.addEventListener('mouseenter', stopAuto);
        carousel.addEventListener('mouseleave', startAuto);

        startAuto();
    }

});

// ── Global helper: Quantity changer ──
function changeQty(delta) {
    const input = document.getElementById('quantity');
    if (!input) return;
    const max = parseInt(input.getAttribute('max')) || 999;
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
}

// ── Global helper: Terms modal ──
function openTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) modal.classList.add('modal-open');
}
function closeTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) modal.classList.remove('modal-open');
}
