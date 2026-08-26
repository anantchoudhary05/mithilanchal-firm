@props([
    'title' => null,
    'meta_title' => null,
    'meta_description' => null,
    'meta_keywords' => null,
    'canonical_url' => null,
    'robots' => 'index, follow',
    'custom_schema' => null,
    'og_type' => 'website',
])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $meta_title ?? $title ?? 'Mithilanchal Farms | Premium Makhana Supplier from Bihar' }}</title>

    <meta name="description" content="{{ $meta_description ?? 'Learn about Mithilanchal Farms Private Limited, a premium makhana manufacturer, wholesale supplier and exporter from Mithilanchal, Darbhanga, Bihar, India.' }}">

    @if(!empty($meta_keywords))
        <meta name="keywords" content="{{ $meta_keywords }}">
    @else
        <meta name="keywords" content="Mithilanchal Farms, Makhana Supplier Bihar, Makhana Manufacturer Darbhanga, Makhana Exporter India, Premium Makhana, Fox Nuts Supplier, Mithila Makhana">
    @endif

    <meta name="author" content="Mithilanchal Farms Private Limited">
    <meta name="robots" content="{{ $robots ?? 'index, follow' }}">

    <link rel="canonical" href="{{ $canonical_url ?? url()->current() }}">

    <meta property="og:title" content="{{ $meta_title ?? $title ?? 'About Mithilanchal Farms | Premium Makhana from Bihar' }}">
    <meta property="og:description" content="{{ $meta_description ?? 'Discover the story, farmers, heritage and quality commitment behind Mithilanchal Farms Private Limited.' }}">
    <meta property="og:type" content="{{ $og_type ?? 'website' }}">
    <meta property="og:url" content="{{ $canonical_url ?? url()->current() }}">

    <meta name="theme-color" content="#155b27">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assests/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assests/css/contact.css') }}">
    <link rel="stylesheet" href="{{ asset('assests/css/ourstory.css') }}">
    <link rel="stylesheet" href="{{ asset('assests/css/Product.css') }}">
    <link rel="stylesheet" href="{{ asset('assests/css/WhyChooseUs.css') }}">
    <link rel="stylesheet" href="{{ asset('assests/css/blog.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @if(!empty($custom_schema))
        <script type="application/ld+json">
            {!! $custom_schema !!}
        </script>
    @endif
</head>

<body>
    <x-header />

    <main class="main">
        {{ $slot }}
    </main>

    <footer class="footer">
        <div class="container footer-grid">
            <div class="footer-about">
                <a href="{{ route('home') }}" class="footer-logo">
                    <img src="{{ asset('assests/img/mithanchal firm.html 2.png') }}" alt="Mithilanchal Farms">
                    <div>
                        <strong>MITHILANCHAL</strong>
                        <span>FARMS PRIVATE LIMITED</span>
                    </div>
                </a>
                <p>
                    Premium Makhana from the heart of
                    Mithilanchal — naturally sourced,
                    quality focused and built on trust.
                </p>
                <div class="footer-social">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="footer-column">
                <h4>QUICK LINKS</h4>
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('Product') }}">Products</a>
                <a href="{{ route('WhyChooseUs') }}">Why Choose Us</a>
                <a href="{{ route('OurStory') }}">Our Story</a>
                <a href="{{ route('blog.index') }}">Blog</a>
                <a href="{{ route('ContactUs') }}">Contact Us</a>
            </div>

            <div class="footer-column">
                <h4>PRODUCTS</h4>
                <a href="{{ route('Product') }}">Premium Makhana</a>
                <a href="{{ route('Product') }}">Roasted Makhana</a>
                <a href="{{ route('Product') }}">Flavoured Makhana</a>
                <a href="{{ route('Product') }}">Bulk Makhana</a>
                <a href="{{ route('Product') }}">Private Label</a>
            </div>

            <div class="footer-column">
                <h4>CONTACT</h4>
                <p>
                    Ward No. 6, Motipur,<br>
                    Block Alinagar,<br>
                    Darbhanga, Bihar – 847103
                </p>
                <a href="tel:+919296918101">+91 92969 18101</a>
                <a href="mailto:mallahmakhana@gmail.com">mallahmakhana@gmail.com</a>
            </div>
        </div>

        <div class="copyright">
            <div class="container">
                © {{ date('Y') }} Mithilanchal Farms. All Rights Reserved.
            </div>
        </div>
    </footer>

    <a
        href="https://wa.me/919296918101?text=Hello%20Mithilanchal%20Farms"
        class="floating-whatsapp"
        aria-label="Chat on WhatsApp"
    >
        <span> ☎ </span>
    </a>

    <script src="{{ asset('assests/js/script.js') }}"></script>
</body>
</html>
