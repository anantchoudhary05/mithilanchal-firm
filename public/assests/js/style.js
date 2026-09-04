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

const offerStorageKey = (id) => `mithilanchal-offer-session-${id}`;
const offerLegacyStorageKey = (id) => `mithilanchal-offer-dismissed-${id}`;

function offerWasDismissed(id) {
    if (!id) {
        return false;
    }

    try {
        window.localStorage.removeItem(offerLegacyStorageKey(id));
        return window.sessionStorage.getItem(offerStorageKey(id)) === '1';
    } catch (error) {
        return false;
    }
}

function rememberOfferDismissed(id) {
    if (!id) {
        return;
    }

    try {
        window.sessionStorage.setItem(offerStorageKey(id), '1');
        window.localStorage.removeItem(offerLegacyStorageKey(id));
    } catch (error) {
        // Ignore private-mode storage failures.
    }
}

function eligibleHeroSlides(root) {
    return [...root.querySelectorAll('.hero-slide')].filter((slide) => {
        if (slide.hasAttribute('hidden')) {
            return false;
        }

        return slide.getAttribute('data-deferred-offer') !== 'true';
    });
}

function releaseOfferSlide(offerId) {
    document.querySelectorAll(`.hero-slide[data-offer-id="${offerId}"]`).forEach((slide) => {
        slide.removeAttribute('hidden');
        slide.removeAttribute('data-deferred-offer');
    });
}

const heroCarousel = {
    root: document.querySelector('[data-hero-carousel]'),
    slides: [],
    dots: [],
    current: 0,
    timer: null,
};

function renderHeroDots() {
    const dotsWrap = heroCarousel.root?.querySelector('.hero-dots');

    if (!dotsWrap) {
        heroCarousel.dots = [];
        return;
    }

    dotsWrap.replaceChildren();

    heroCarousel.slides.forEach((_, index) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = index === heroCarousel.current ? 'is-active' : '';
        dot.setAttribute('aria-label', `Show slide ${index + 1}`);
        dot.addEventListener('click', () => goToHeroSlide(index, true));
        dotsWrap.appendChild(dot);
    });

    heroCarousel.dots = [...dotsWrap.querySelectorAll('button')];
}

function goToHeroSlide(nextIndex, userClick = false) {
    if (!heroCarousel.slides.length) {
        return;
    }

    heroCarousel.slides[heroCarousel.current]?.classList.remove('is-active');
    heroCarousel.dots[heroCarousel.current]?.classList.remove('is-active');
    heroCarousel.current = (nextIndex + heroCarousel.slides.length) % heroCarousel.slides.length;
    heroCarousel.slides[heroCarousel.current]?.classList.add('is-active');
    heroCarousel.dots[heroCarousel.current]?.classList.add('is-active');

    if (userClick) {
        startHeroTimer();
    }
}

function startHeroTimer() {
    window.clearInterval(heroCarousel.timer);

    if (
        reduceMotion
        || heroCarousel.slides.length < 2
        || document.body.classList.contains('offer-popup-open')
    ) {
        return;
    }

    heroCarousel.timer = window.setInterval(() => {
        goToHeroSlide(heroCarousel.current + 1);
    }, 5200);
}

function initHeroCarousel(options = {}) {
    const root = heroCarousel.root;

    if (!root) {
        return;
    }

    window.clearInterval(heroCarousel.timer);
    heroCarousel.slides = eligibleHeroSlides(root);

    const preferredIndex = options.offerId
        ? heroCarousel.slides.findIndex((slide) => slide.getAttribute('data-offer-id') === String(options.offerId))
        : heroCarousel.slides.findIndex((slide) => slide.classList.contains('is-active'));

    heroCarousel.current = preferredIndex >= 0 ? preferredIndex : 0;

    heroCarousel.slides.forEach((slide, index) => {
        slide.classList.toggle('is-active', index === heroCarousel.current);
    });

    renderHeroDots();

    const prevButton = root.querySelector('[data-hero-prev]');
    const nextButton = root.querySelector('[data-hero-next]');
    const showNav = heroCarousel.slides.length > 1;

    if (prevButton) {
        prevButton.hidden = !showNav;
    }

    if (nextButton) {
        nextButton.hidden = !showNav;
    }

    if (!root.dataset.heroBound) {
        prevButton?.addEventListener('click', () => goToHeroSlide(heroCarousel.current - 1, true));
        nextButton?.addEventListener('click', () => goToHeroSlide(heroCarousel.current + 1, true));
        root.dataset.heroBound = 'true';
    }

    startHeroTimer();
}

function setOfferPopupOpen(isOpen) {
    const popup = document.querySelector('[data-offer-popup]');

    if (!popup) {
        return;
    }

    popup.hidden = !isOpen;
    document.body.classList.toggle('offer-popup-open', isOpen);

    if (!isOpen) {
        startHeroTimer();
    } else {
        window.clearInterval(heroCarousel.timer);
    }
}

function dismissOfferPopup() {
    const popup = document.querySelector('[data-offer-popup]');
    const offerId = popup?.getAttribute('data-offer-id');

    rememberOfferDismissed(offerId);
    setOfferPopupOpen(false);
    releaseOfferSlide(offerId);
    initHeroCarousel({ offerId });
}

(function initWelcomeOffer() {
    const popup = document.querySelector('[data-offer-popup]');
    const offerId = popup?.getAttribute('data-offer-id');

    if (!popup || !offerId) {
        return;
    }

    if (offerWasDismissed(offerId)) {
        releaseOfferSlide(offerId);
        setOfferPopupOpen(false);
    } else {
        setOfferPopupOpen(true);
    }

    popup.querySelectorAll('[data-offer-close]').forEach((element) => {
        element.addEventListener('click', dismissOfferPopup);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && document.body.classList.contains('offer-popup-open')) {
            dismissOfferPopup();
        }
    });
})();

initHeroCarousel();

document.querySelectorAll('[data-product-carousel]').forEach((carousel) => {
    const slides = carousel.querySelectorAll('[data-product-slide]');

    if (slides.length < 2) {
        return;
    }

    const track = carousel.querySelector('.related-product-track');
    const dotsWrap = carousel.querySelector('[data-carousel-dots]');
    const prevButton = carousel.querySelector('[data-carousel-prev]');
    const nextButton = carousel.querySelector('[data-carousel-next]');
    let current = 0;
    let timer;

    slides.forEach((_, index) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = index === 0 ? 'is-active' : '';
        dot.setAttribute('aria-label', `Show product ${index + 1}`);
        dot.addEventListener('click', () => goTo(index, true));
        dotsWrap?.appendChild(dot);
    });

    const dots = dotsWrap ? dotsWrap.querySelectorAll('button') : [];

    function goTo(nextIndex, userAction = false) {
        current = (nextIndex + slides.length) % slides.length;

        if (track) {
            track.style.transform = `translateX(-${current * 100}%)`;
        }

        slides.forEach((slide, index) => {
            const isActive = index === current;
            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            dots[index]?.classList.toggle('is-active', isActive);
        });

        if (userAction) {
            startTimer();
        }
    }

    function startTimer() {
        window.clearInterval(timer);

        if (reduceMotion) {
            return;
        }

        timer = window.setInterval(() => {
            goTo(current + 1);
        }, 4500);
    }

    prevButton?.addEventListener('click', () => goTo(current - 1, true));
    nextButton?.addEventListener('click', () => goTo(current + 1, true));

    carousel.addEventListener('mouseenter', () => window.clearInterval(timer));
    carousel.addEventListener('mouseleave', startTimer);
    carousel.addEventListener('focusin', () => window.clearInterval(timer));
    carousel.addEventListener('focusout', startTimer);

    goTo(0);
    startTimer();
});

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

