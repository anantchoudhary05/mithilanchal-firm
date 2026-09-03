<!-- =========================
     TOP BAR
========================= -->

<div class="topbar">
    <div class="container topbar-inner">
        <span>Pond-grown makhana from Mithila — welcome to Mithilanchal Farms</span>

        <div class="top-contact">
            <a href="tel:+919296918101">+91 92969 18101</a>
            <span>|</span>
            <a href="mailto:mallahmakhana@gmail.com">mallahmakhana@gmail.com</a>
        </div>
    </div>
</div>

<!-- =========================
NAVBAR
========================= -->

<header class="header">
    <div class="container navbar">
        <a href="{{ route('home') }}" class="brand">
            <div class="brand-mark">
                <img src="{{ asset('assests/img/mithanchal firm.html 2.png') }}" alt="Mithilanchal Farms">
            </div>
            <div>
                <strong>MITHILANCHAL</strong>
                <span>FARMS PRIVATE LIMITED</span>
                <small>A Unit of Mallah Makhana</small>
            </div>
        </a>

        <button class="menu-toggle" id="menuToggle" type="button" aria-label="Open navigation" aria-controls="nav" aria-expanded="false">
            ☰
        </button>

        <div class="nav-backdrop" id="navBackdrop" hidden></div>

        <nav class="nav" id="nav">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('Product') }}" class="{{ request()->routeIs('Product') ? 'active' : '' }}">Products</a>
            <a href="{{ route('WhyChooseUs') }}" class="{{ request()->routeIs('WhyChooseUs') ? 'active' : '' }}">Why Choose Us</a>
            <a href="{{ route('OurStory') }}" class="{{ request()->routeIs('OurStory') ? 'active' : '' }}">Our Story</a>
            <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Blog</a>
            <a href="{{ route('ContactUs') }}" class="nav-cta {{ request()->routeIs('ContactUs', 'contact.thankYou') ? 'active' : '' }}">Contact Us</a>
        </nav>
    </div>
</header>
