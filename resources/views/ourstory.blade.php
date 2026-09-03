<x-layout>
    <!-- ================= HERO ================= -->

    <section class="hero">
      <!-- CHANGE YOUR HERO IMAGE HERE -->
      <img src="{{ asset('assests/img/hq-roast-bihar.jpg') }}" alt="Makhana roasted in Mithila" class="hero-img" fetchpriority="high" />

      <div class="hero-overlay"></div>

      <div class="container hero-content">
        <h1>
          From the heart of Bihar,
          <span>to every home.</span>
        </h1>

        <p>
          A traditional treasure, thoughtfully crafted for the modern world.
        </p>

        <a href="#story" class="primary-btn">
          Discover Our Story
          <span>↓</span>
        </a>
      </div>
    </section>

    <!-- ================= INTRO ================= -->

    <section class="story-section" id="story">
      <div class="container story-grid">
        <div class="story-image">
          <img src="{{ asset('assests/img/hq-bowl.jpg') }}" alt="Our Makhana Story" loading="lazy" decoding="async" />
        </div>

        <div class="story-content">
          <span class="section-tag"> WHERE IT BEGAN </span>

          <h2>A little pearl with a big story.</h2>

          <p>
            Deep in the heart of Bihar, makhana has been a part of our culture
            for generations.
          </p>

          <p>
            Grown in the pristine waters of Mithila, these delicate white pearls
            have always been loved for their unique taste and versatility.
          </p>

          <p>
            We wanted to take this beautiful tradition beyond local kitchens and
            introduce it to snack lovers everywhere.
          </p>

          <div class="quote">
            “We believe great food should be rooted in tradition.”
          </div>
        </div>
      </div>
    </section>

    <!-- ================= BRAND STATEMENT ================= -->

    <section class="statement">
      <div class="container statement-content">
        <span class="section-tag"> OUR BELIEF </span>

        <h2>
          Traditional at heart.
          <br />
          Modern by nature.
        </h2>

        <p>
          We bring together the wisdom of traditional makhana farming with
          modern ideas, delicious flavours and thoughtful packaging.
        </p>
      </div>
    </section>

    <!-- ================= FARM TO PACK ================= -->

    <section class="process">
      <div class="container">
        <div class="section-heading">
          <span class="section-tag"> FARM TO PACK </span>

          <h2>Good food starts at the source.</h2>

          <p>
            Every pack of makhana begins with careful sourcing and ends with a
            satisfying crunch.
          </p>
        </div>

        <div class="process-grid">
          <!-- CARD 1 -->

          <div class="process-card">
            <div class="number">01</div>

            <h3>Carefully Grown</h3>

            <p>
              We source makhana from trusted farming regions known for quality
              fox nuts.
            </p>
          </div>

          <!-- CARD 2 -->

          <div class="process-card">
            <div class="number">02</div>

            <h3>Carefully Selected</h3>

            <p>
              Every batch is selected for its quality, size, freshness and
              texture.
            </p>
          </div>

          <!-- CARD 3 -->

          <div class="process-card">
            <div class="number">03</div>

            <h3>Expertly Roasted</h3>

            <p>
              Our roasting process creates the perfect combination of lightness
              and crunch.
            </p>
          </div>

          <!-- CARD 4 -->

          <div class="process-card">
            <div class="number">04</div>

            <h3>Freshly Packed</h3>

            <p>Carefully packed to help preserve freshness and flavour.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= FARMERS ================= -->

    <section class="farmers">
      <div class="container farmers-grid">
        <div class="farmers-content">
          <span class="section-tag"> OUR FARMERS </span>

          <h2>Behind every crunch is a story.</h2>

          <p>
            Our journey would not be possible without the farmers and
            communities who grow and harvest makhana.
          </p>

          <p>
            Their knowledge, patience and connection with the land are what make
            every harvest special.
          </p>

          <p>
            We believe that when farmers grow better, communities grow stronger.
          </p>

          <a href="{{ route('ContactUs') }}" class="dark-btn">
            Learn More
            <span>→</span>
          </a>
        </div>

        <div class="farmers-image">
          <img src="{{ asset('assests/img/hq-masala.jpg') }}" alt="Makhana prepared by local hands" loading="lazy" decoding="async" />
        </div>
      </div>
    </section>

    <!-- ================= VALUES ================= -->

    <section class="values">
      <div class="container">
        <div class="section-heading center">
          <span class="section-tag"> OUR PROMISE </span>

          <h2>Better snacking starts here.</h2>
        </div>

        <div class="values-grid">
          <div class="value-card">
            <div class="icon">✦</div>

            <h3>Authentic</h3>

            <p>Rooted in the rich makhana traditions of Bihar.</p>
          </div>

          <div class="value-card">
            <div class="icon">✓</div>

            <h3>Quality First</h3>

            <p>Carefully selected and thoughtfully prepared.</p>
          </div>

          <div class="value-card">
            <div class="icon">◉</div>

            <h3>Fresh & Crunchy</h3>

            <p>Packed to preserve the crunch you love.</p>
          </div>

          <div class="value-card">
            <div class="icon">♥</div>

            <h3>Full of Flavour</h3>

            <p>Delicious flavours made for every mood.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= TIMELINE ================= -->

    <section class="journey">
      <div class="container">
        <div class="section-heading center">
          <span class="section-tag"> OUR JOURNEY </span>

          <h2>From tradition to your table.</h2>
        </div>

        <div class="timeline">
          <div class="timeline-item">
            <div class="timeline-number">01</div>

            <div>
              <h3>The Beginning</h3>

              <p>A love for authentic Bihar makhana sparked an idea.</p>
            </div>
          </div>

          <div class="timeline-item">
            <div class="timeline-number">02</div>

            <div>
              <h3>The Discovery</h3>

              <p>
                We explored the farms, people and traditions behind makhana.
              </p>
            </div>
          </div>

          <div class="timeline-item">
            <div class="timeline-number">03</div>

            <div>
              <h3>The Craft</h3>

              <p>We experimented with roasting, seasoning and flavours.</p>
            </div>
          </div>

          <div class="timeline-item">
            <div class="timeline-number">04</div>

            <div>
              <h3>The Brand</h3>

              <p>Tradition and modern snacking came together.</p>
            </div>
          </div>

          <div class="timeline-item">
            <div class="timeline-number">05</div>

            <div>
              <h3>The Future</h3>

              <p>
                Our dream is to make makhana a favourite snack in every home.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ================= CTA ================= -->

    <section class="cta">
      <div class="container">
        <div class="cta-content">
          <span class="section-tag"> TASTE THE STORY </span>

          <h2>Your next favourite snack has a story.</h2>

          <p>
            From the waters of Bihar to your hands, every pack carries a little
            piece of tradition and plenty of crunch.
          </p>

          <a href="{{ route('Product') }}" class="primary-btn">
            Shop Makhana
            <span>→</span>
          </a>
        </div>
      </div>
    </section>

</x-layout>
