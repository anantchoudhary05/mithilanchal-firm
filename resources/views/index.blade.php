<x-layout>

    <!-- =========================
     Hero section
    ========================= -->

    <section class="hero hero-slideshow">

        <div class="hero-slides" aria-hidden="true">
            <div class="hero-slide is-active" style="background-image: url('{{ asset('assests/img/hq-roasted.jpg') }}')"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('assests/img/hq-bowl.jpg') }}')"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('assests/img/hq-roast-bihar.jpg') }}')"></div>
            <div class="hero-slide" style="background-image: url('{{ asset('assests/img/hq-white.jpg') }}')"></div>
        </div>

        <div class="hero-overlay"></div>

        <div class="container hero-content">

            <div class="eyebrow light">
                FROM MITHILA'S PONDS
            </div>

            <h1>
                Rooted in Mithilanchal.
                <span>Grown with care.</span>
            </h1>

            <p>
                Premium fox nuts from Darbhanga — popped by local hands, graded with honesty, and shared with homes and businesses that value the real taste of Bihar.
            </p>

            <div class="hero-buttons">

                <a href="{{ route('OurStory') }}" class="btn btn-primary">
                    Discover Our Story
                </a>

                <a href="{{ route('Product') }}" class="btn btn-white">
                    Explore Products
                </a>

            </div>

        </div>

        <div class="hero-dots"></div>

    </section>

    <section class="section">

        <div class="container about-grid">


            <div class="about-image">

                <img src="{{ asset('assests/img/hq-roast-bihar.jpg') }}"
                    alt="Makhana being roasted in Bihar"
                    loading="lazy"
                    decoding="async">

                <div class="image-label">

                    <strong>
                        MITHILANCHAL
                    </strong>

                    <span>
                        BIHAR, INDIA
                    </span>

                </div>

            </div>


            <div class="about-content">

                <div class="eyebrow">
                    WHO WE ARE
                </div>

                <h2>
                    Premium Makhana, Authentic Mithilanchal
                </h2>

                <p>
                    <strong>
                        Mithilanchal Farms Private Limited,
                    </strong> a unit of Mallah Makhana, is a trusted manufacturer, wholesale supplier and exporter of
                    premium-quality makhana from Bihar, India.
                </p>

                <p>
                    With over four years of experience in the makhana industry, we focus on quality, hygiene,
                    transparency and customer satisfaction.
                </p>

                <p>
                    Located in the renowned makhana-producing region of Mithilanchal, we work closely with local farmers
                    and producers while combining traditional knowledge with modern quality standards.
                </p>


                <div class="highlight">

                    <span class="highlight-icon">
                        🌾
                    </span>

                    <div>

                        <strong>
                            Directly connected to the source
                        </strong>

                        <p>
                            We work with local producers to create value where the journey of makhana begins.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================
     STATS
========================= -->

    <section class="stats" aria-label="Mithilanchal Farms at a glance">

        <div class="container stats-grid">

            <article class="stat">
                <div class="stat-icon" aria-hidden="true">
                    <i class="fa-solid fa-seedling"></i>
                </div>
                <strong class="stat-number" data-count="4" data-suffix="+">4+</strong>
                <span>Years of care</span>
            </article>

            <article class="stat">
                <div class="stat-icon" aria-hidden="true">
                    <i class="fa-solid fa-certificate"></i>
                </div>
                <strong class="stat-number" data-count="100" data-suffix="%">100%</strong>
                <span>Quality & grading</span>
            </article>

            <article class="stat">
                <div class="stat-icon" aria-hidden="true">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <strong class="stat-number" data-count="6">6</strong>
                <span>Makhana ranges</span>
            </article>

            <article class="stat">
                <div class="stat-icon" aria-hidden="true">
                    <i class="fa-solid fa-earth-asia"></i>
                </div>
                <strong class="stat-number" data-count="2">2</strong>
                <span>India & export markets</span>
            </article>

        </div>

    </section>



    <!-- =========================
     FARMERS
========================= -->

    <section class="section farmer-section">

        <div class="container farmer-grid">


            <div class="farmer-content">

                <div class="eyebrow">
                    OUR FARMERS
                </div>

                <h2>
                    From Mithila's Ponds to the World
                </h2>

                <p>
                    Behind every grain of makhana is the hard work, knowledge and dedication of local farming
                    communities.
                </p>

                <p>
                    Mithilanchal's traditional makhana cultivation is closely connected with ponds and wetlands. We
                    believe that a successful makhana business should also create value for the people who make it
                    possible.
                </p>


                <ul class="check-list">

                    <li>
                        <span>✓</span> Support local farmers and producers
                    </li>

                    <li>
                        <span>✓</span> Create rural employment opportunities
                    </li>

                    <li>
                        <span>✓</span> Encourage value addition at source
                    </li>

                    <li>
                        <span>✓</span> Strengthen local supply chains
                    </li>

                    <li>
                        <span>✓</span> Preserve traditional knowledge
                    </li>

                </ul>

            </div>


            <div class="farmer-image">

                <img src="{{ asset('assests/img/hq-masala.jpg') }}"
                    alt="Traditional roasted makhana from Bihar"
                    loading="lazy"
                    decoding="async">

                <div class="photo-caption">
                    Traditional makhana harvesting in Bihar
                </div>

            </div>

        </div>

    </section>



    <!-- =========================
     OUR VALUES
========================= -->

    <section class="section">

        <div class="container">

            <div class="section-heading">

                <div class="eyebrow">
                    OUR VALUES
                </div>

                <h2>
                    What We Stand For
                </h2>

                <p>
                    Our values guide every sourcing decision, every batch and every customer relationship.
                </p>

            </div>


            <div class="values-grid values-grid-3">


                <article class="value-card">

                    <div class="value-icon">
                        🌱
                    </div>

                    <h3>
                        Authenticity
                    </h3>

                    <p>
                        Staying connected with the farming heritage and traditions of Mithilanchal.
                    </p>

                </article>


                <article class="value-card">

                    <div class="value-icon">
                        ⭐
                    </div>

                    <h3>
                        Quality
                    </h3>

                    <p>
                        Careful sourcing, cleaning, grading, inspection and packaging.
                    </p>

                </article>


                <article class="value-card">

                    <div class="value-icon">
                        🤝
                    </div>

                    <h3>
                        Trust
                    </h3>

                    <p>
                        Building long-term relationships through transparent communication and service.
                    </p>

                </article>


                <article class="value-card">

                    <div class="value-icon">
                        🌾
                    </div>

                    <h3>
                        Sustainability
                    </h3>

                    <p>
                        Supporting responsible agriculture and stronger rural supply chains.
                    </p>

                </article>


                <article class="value-card">

                    <div class="value-icon">
                        📦
                    </div>

                    <h3>
                        Reliability
                    </h3>

                    <p>
                        Consistent quality, dependable supply and timely delivery.
                    </p>

                </article>


                <article class="value-card">

                    <div class="value-icon">
                        🌍
                    </div>

                    <h3>
                        Global Vision
                    </h3>

                    <p>
                        Taking authentic Mithilanchal makhana to customers around the world.
                    </p>

                </article>

            </div>

        </div>

    </section>



    <!-- =========================
     PRODUCT IMAGE SHOWCASE
========================= -->

    <section class="showcase">

        <div class="container">

            <div class="section-heading">

                <div class="eyebrow light">
                    OUR PRODUCT
                </div>

                <h2>
                    The Makhana We Are Proud Of
                </h2>

                <p>
                    Naturally light, crisp and versatile — makhana is at the heart of everything we do.
                </p>

            </div>


            <div class="gallery">


                <div class="gallery-main">

                    <img src="{{ asset('assests/img/hq-roasted.jpg') }}"
                        alt="Roasted makhana fox nuts"
                        loading="lazy"
                        decoding="async">

                    <div class="gallery-text">

                        <span>
                            PREMIUM FOX NUTS
                        </span>

                        <h3>
                            Carefully processed. Naturally delicious.
                        </h3>

                    </div>

                </div>


                <div class="gallery-small">

                    <img src="{{ asset('assests/img/hq-white.jpg') }}"
                        alt="Premium white makhana"
                        loading="lazy"
                        decoding="async">

                </div>


                <div class="gallery-small">

                    <img src="{{ asset('assests/img/hq-bowl.jpg') }}"
                        alt="Makhana served as a snack"
                        loading="lazy"
                        decoding="async">

                </div>

            </div>

        </div>

    </section>



    <!-- =========================
     MISSION & VISION
========================= -->

    <section class="section mission-section">

        <div class="container mission-grid">


            <div class="mission-card">

                <div class="mission-number">
                    01
                </div>

                <div class="eyebrow">
                    OUR MISSION
                </div>

                <h2>
                    Empowering Farmers. Promoting Makhana.
                </h2>

                <p>
                    Our mission is to promote premium-quality makhana while empowering farming communities of
                    Mithilanchal.
                </p>

                <p>
                    We aim to support local farmers through direct sourcing, encourage sustainable practices, create
                    employment and deliver quality products without compromising authenticity.
                </p>

            </div>


            <div class="mission-card">

                <div class="mission-number">
                    02
                </div>

                <div class="eyebrow">
                    OUR VISION
                </div>

                <h2>
                    From Mithilanchal to the World
                </h2>

                <p>
                    Our vision is to become one of India's most trusted and globally recognized makhana brands.
                </p>

                <p>
                    We want to combine traditional farming heritage with modern quality standards, ethical sourcing and
                    customer satisfaction.
                </p>

            </div>

        </div>

    </section>



    <!-- =========================
     OUR STORY
========================= -->

    <section class="section" id="story">

        <div class="container story-grid">


            <div class="story-image">

                <img src="{{ asset('assests/img/hq-phool.jpg') }}"
                    alt="Fresh phool makhana from Bihar"
                    loading="lazy"
                    decoding="async">

            </div>


            <div class="story-content">

                <div class="eyebrow">
                    OUR STORY
                </div>

                <h2>
                    This Is More Than a Business
                </h2>

                <p>
                    Born and raised in Mithilanchal, we have always been inspired by the people, traditions and natural
                    wealth of this region.
                </p>

                <p>
                    For us, makhana is much more than a healthy snack. It represents our culture, heritage and the
                    livelihood of farming communities.
                </p>

                <p>
                    Through Mithilanchal Farms Private Limited, we are committed to creating value where it begins — by
                    working closely with local growers and producers.
                </p>

                <p>
                    Every pack of makhana represents the dedication, craftsmanship and traditions of Mithilanchal.
                </p>


                <blockquote>
                    “This is our home. This is our heritage. This is the story we are proud to share with the world.”
                </blockquote>

            </div>

        </div>

    </section>



    <!-- =========================
     QUALITY PROCESS
========================= -->

    <section class="section quality">

        <div class="container">

            <div class="section-heading">

                <div class="eyebrow">
                    QUALITY COMMITMENT
                </div>

                <h2>
                    From Source to Delivery
                </h2>

                <p>
                    Every batch is handled with care to maintain freshness, quality and consistency.
                </p>

            </div>


            <div class="process-grid">


                <div class="process-card">

                    <span>
                        01
                    </span>

                    <h3>
                        Sourcing
                    </h3>

                    <p>
                        Careful sourcing from trusted producers.
                    </p>

                </div>


                <div class="process-card">

                    <span>
                        02
                    </span>

                    <h3>
                        Cleaning
                    </h3>

                    <p>
                        Careful preparation and cleaning.
                    </p>

                </div>


                <div class="process-card">

                    <span>
                        03
                    </span>

                    <h3>
                        Grading
                    </h3>

                    <p>
                        Sorting according to size and quality.
                    </p>

                </div>


                <div class="process-card">

                    <span>
                        04
                    </span>

                    <h3>
                        Inspection
                    </h3>

                    <p>
                        Quality checks before packaging.
                    </p>

                </div>


                <div class="process-card">

                    <span>
                        05
                    </span>

                    <h3>
                        Packaging
                    </h3>

                    <p>
                        Secure packaging to protect freshness.
                    </p>

                </div>


                <div class="process-card">

                    <span>
                        06
                    </span>

                    <h3>
                        Delivery
                    </h3>

                    <p>
                        Reliable transportation and timely dispatch.
                    </p>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================
     CTA
========================= -->

    <section class="cta">

        <div class="container">

            <div class="eyebrow light">
                WHOLESALE • DISTRIBUTION • EXPORT
            </div>

            <h2>
                Looking for a Reliable Makhana Supplier?
            </h2>

            <p>
                Talk to Mithilanchal Farms for wholesale supply, bulk orders, private-label packaging and export
                requirements.
            </p>

            <div class="cta-buttons">

                <a href="https://wa.me/919296918101?text=Hello%20Mithilanchal%20Farms%2C%20I%20am%20interested%20in%20your%20makhana."
                    target="_blank" class="btn whatsapp-btn">

                    WhatsApp Us

                </a>

                <a href="tel:+919296918101" class="btn btn-white">

                    Call +91 92969 18101

                </a>

            </div>

        </div>

    </section>

</x-layout>