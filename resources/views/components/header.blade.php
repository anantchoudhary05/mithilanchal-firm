<!-- =========================
     TOP BAR
========================= -->

<div class="topbar">

<div class="container topbar-inner">

    <span>
    Welcome to mithilanchal farms
</span>

    <div class="top-contact">

        <a href="tel:+919296918101">
        +91 92969 18101
    </a>

        <span>|</span>

        <a href="mailto:mallahmakhana@gmail.com">
        mallahmakhana@gmail.com
    </a>

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
            <img src="{{ asset('assests/img/mithanchal firm.html 2.png') }}" alt="" srcset="">
        </div>

        <div>

            <strong>
            MITHILANCHAL
        </strong>

            <span>
            FARMS PRIVATE LIMITED
        </span>

            <small>
            A Unit of Mallah Makhana
        </small>

        </div>

    </a>


    <button class="menu-toggle" id="menuToggle" aria-label="Open navigation">

    ☰

</button>


    <nav class="nav" id="nav">

        <a href="/">
        Home
    </a>



        <a href="{{ route('Product') }}">
        Products
    </a>

        <a href="{{ route('WhyChooseUs') }}">
        Why Choose Us
    </a>



        <a href="{{ route('OurStory') }}">
        Our Story
    </a>

    <a href="{{ route('blog.index') }}">
        Blog
    </a>

    <a href="{{ route('ContactUs') }}">
        Contact Us
    </a>

    </nav>

</div>

</header>