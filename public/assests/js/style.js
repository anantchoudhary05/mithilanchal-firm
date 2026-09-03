/* =========================================================
   MITHILANCHAL FARMS — site interactions
========================================================= */

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const menuToggle = document.getElementById('menuToggle');
const nav = document.getElementById('nav');
const navBackdrop = document.getElementById('navBackdrop');
const header = document.querySelector('.header');

function setMenuOpen(isOpen) {
    if (!nav || !menuToggle) {
        return;
    }

    nav.classList.toggle('open', isOpen);
    navBackdrop?.classList.toggle('is-open', isOpen);
    document.body.classList.toggle('nav-open', isOpen);
    menuToggle.setAttribute('aria-expanded', String(isOpen));
    menuToggle.setAttribute('aria-label', isOpen ? 'Close navigation' : 'Open navigation');
    menuToggle.textContent = isOpen ? '✕' : '☰';

    if (navBackdrop) {
        navBackdrop.hidden = !isOpen;
    }
}

if (menuToggle && nav) {
    menuToggle.addEventListener('click', () => {
        setMenuOpen(!nav.classList.contains('open'));
    });

    navBackdrop?.addEventListener('click', () => setMenuOpen(false));

    nav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setMenuOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setMenuOpen(false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 800) {
            setMenuOpen(false);
        }
    });
}

function updateHeader() {
    if (!header) {
        return;
    }

    header.classList.toggle('is-scrolled', window.scrollY > 12);
}

window.addEventListener('scroll', updateHeader, { passive: true });
updateHeader();

const revealSelector = [
    '.value-card',
    '.process-card',
    '.mission-card',
    '.product-card',
    '.why-card',
    '.info-card',
    '.form-card',
    '.blog-card',
    '.about-grid > *',
    '.farmer-grid > *',
    '.story-grid > *',
    '.intro-grid > *',
    '.contact-grid > *',
    '.section-heading',
    '.cta .container',
    '.timeline-item',
    '.farmers-grid > *',
].join(',');

if (!reduceMotion) {
    const revealElements = document.querySelectorAll(revealSelector);

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.01, rootMargin: '80px 0px 25% 0px' });

    revealElements.forEach((element, index) => {
        if (!element.hasAttribute('data-reveal')) {
            element.setAttribute('data-reveal', '');
        }
        element.style.setProperty('--reveal-delay', `${(index % 3) * 40}ms`);
        revealObserver.observe(element);
    });
}

document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', function (event) {
        const targetId = this.getAttribute('href');

        if (!targetId || targetId === '#') {
            return;
        }

        const target = document.querySelector(targetId);

        if (!target) {
            return;
        }

        event.preventDefault();

        const headerHeight = header ? header.offsetHeight : 0;

        window.scrollTo({
            top: target.getBoundingClientRect().top + window.scrollY - headerHeight - 12,
            behavior: reduceMotion ? 'auto' : 'smooth',
        });
    });
});

document.querySelectorAll('img').forEach((image) => {
    image.addEventListener('error', () => {
        image.style.background = '#eaf4e7';
        image.style.objectFit = 'contain';
    });
});

function formatStatValue(value, prefix, suffix) {
    return `${prefix}${value}${suffix}`;
}

function animateStat(element) {
    const target = Number(element.dataset.count);

    if (!Number.isFinite(target)) {
        return;
    }

    const prefix = element.dataset.prefix || '';
    const suffix = element.dataset.suffix || '';
    const duration = Number(element.dataset.duration) || (target >= 50 ? 1600 : 1100);

    if (reduceMotion) {
        element.textContent = formatStatValue(target, prefix, suffix);
        element.classList.add('is-counted');
        return;
    }

    const start = performance.now();

    element.textContent = formatStatValue(0, prefix, suffix);

    function tick(now) {
        const progress = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = Math.round(eased * target);

        element.textContent = formatStatValue(current, prefix, suffix);

        if (progress < 1) {
            requestAnimationFrame(tick);
            return;
        }

        element.textContent = formatStatValue(target, prefix, suffix);
        element.classList.add('is-counted');
    }

    requestAnimationFrame(tick);
}

const statNumbers = document.querySelectorAll('.stat-number[data-count]');

if (statNumbers.length) {
    const statsObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            const delay = Number(entry.target.dataset.delay) || 0;

            window.setTimeout(() => animateStat(entry.target), delay);
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.4 });

    statNumbers.forEach((element, index) => {
        element.dataset.delay = String(index * 140);
        statsObserver.observe(element);
    });
}

const heroSlides = document.querySelectorAll('.hero-slideshow .hero-slide');

if (heroSlides.length > 1) {
    const dotsWrap = document.querySelector('.hero-slideshow .hero-dots');
    let currentSlide = 0;
    let timer;

    heroSlides.forEach((_, index) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = index === 0 ? 'is-active' : '';
        dot.setAttribute('aria-label', `Show slide ${index + 1}`);
        dot.addEventListener('click', () => goToSlide(index, true));
        dotsWrap?.appendChild(dot);
    });

    const dots = dotsWrap ? dotsWrap.querySelectorAll('button') : [];

    function goToSlide(nextIndex, userClick = false) {
        heroSlides[currentSlide].classList.remove('is-active');
        dots[currentSlide]?.classList.remove('is-active');
        currentSlide = (nextIndex + heroSlides.length) % heroSlides.length;
        heroSlides[currentSlide].classList.add('is-active');
        dots[currentSlide]?.classList.add('is-active');

        if (userClick) {
            startHeroTimer();
        }
    }

    function startHeroTimer() {
        window.clearInterval(timer);

        if (reduceMotion) {
            return;
        }

        timer = window.setInterval(() => {
            goToSlide(currentSlide + 1);
        }, 5200);
    }

    startHeroTimer();
}

document.querySelectorAll('[data-digits-only]').forEach((input) => {
    const maxLength = Number(input.getAttribute('data-digits-only')) || 10;

    function digitsOnly(value) {
        let digits = String(value || '').replace(/\D/g, '');

        if (digits.length === 12 && digits.startsWith('91')) {
            digits = digits.slice(2);
        }

        if (digits.length === 11 && digits.startsWith('0')) {
            digits = digits.slice(1);
        }

        return digits.slice(0, maxLength);
    }

    function applyDigits() {
        const next = digitsOnly(input.value);

        if (input.value !== next) {
            input.value = next;
        }
    }

    input.addEventListener('input', applyDigits);
    input.addEventListener('blur', applyDigits);
    input.addEventListener('paste', () => {
        window.setTimeout(applyDigits, 0);
    });
    input.addEventListener('keypress', (event) => {
        if (event.ctrlKey || event.metaKey || event.key.length !== 1) {
            return;
        }

        if (!/[0-9]/.test(event.key)) {
            event.preventDefault();
        }
    });
});

