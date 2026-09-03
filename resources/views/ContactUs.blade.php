<x-layout>

    <!-- ================= HERO ================= -->

    <section class="contact-hero">
      <div class="hero-overlay"></div>

      <div class="hero-content">
        <span class="hero-tag"> GET IN TOUCH </span>

        <h1>
          Let's Talk About
          <span>Premium Makhana.</span>
        </h1>

        <p>
          Looking for premium Makhana for your business? Connect with
          Mithilanchal Farms for wholesale, retail, private-label and export
          requirements.
        </p>
      </div>
    </section>

    <!-- ================= CONTACT AREA ================= -->

    <section class="contact-area" id="contact-form">
      <div class="container contact-grid">
        <!-- LEFT SIDE -->

        <div class="contact-info">
          <span class="section-tag"> CONTACT MITHILANCHAL FARMS </span>

          <h2>
            Let's build a
            <span>better Makhana</span>
            business together.
          </h2>

          <p class="info-description">
            Whether you need bulk Makhana, premium grades, roasted products or
            private-label solutions, our team is ready to help.
          </p>

          <!-- INFO CARD -->

          <div class="info-card">
            <div class="info-icon">
              <i class="fa-solid fa-location-dot"></i>
            </div>

            <div>
              <h3>Visit Us</h3>

              <p>
                Ward No. 6, Motipur,<br />
                Block Alinagar, Darbhanga,<br />
                Bihar – 847103, India
              </p>
            </div>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fa-solid fa-phone"></i>
            </div>

            <div>
              <h3>Call Us</h3>

              <a href="tel:+919296918101"> +91 92969 18101 </a>
            </div>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fa-solid fa-envelope"></i>
            </div>

            <div>
              <h3>Email Us</h3>

              <a href="mailto:mallahmakhana@gmail.com">
                mallahmakhana@gmail.com
              </a>
            </div>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fa-brands fa-whatsapp"></i>
            </div>

            <div>
              <h3>WhatsApp</h3>

              <a href="https://wa.me/919296918101" target="_blank">
                Chat with us
              </a>
            </div>
          </div>
        </div>

        <!-- ================= FORM ================= -->

        <div class="form-card">
          <div class="form-header">
            <span> SEND US A MESSAGE </span>

            <h2>
              Tell us what you
              <strong>are looking for.</strong>
            </h2>

            <p>Fill out the form and our team will get back to you shortly.</p>
          </div>

          <form method="POST" action="{{ route('contact.store') }}">
            @csrf

            @if ($errors->any())
              <div class="form-alert form-alert-error" role="alert">
                <i class="fa-solid fa-circle-exclamation"></i>
                Please check the highlighted fields and try again.
              </div>
            @endif

            <div class="form-row">
              <div class="form-group {{ $errors->has('name') ? 'is-invalid' : '' }}">
                <label for="name"> Full Name * </label>

                <input
                  type="text"
                  id="name"
                  name="name"
                  value="{{ old('name') }}"
                  placeholder="Enter your name"
                  required
                />
                @error('name')
                  <p class="form-error">{{ $message }}</p>
                @enderror
              </div>

              <div class="form-group {{ $errors->has('company') ? 'is-invalid' : '' }}">
                <label for="company"> Company Name </label>

                <input
                  type="text"
                  id="company"
                  name="company"
                  value="{{ old('company') }}"
                  placeholder="Your company name"
                />
                @error('company')
                  <p class="form-error">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="form-row">
              <div class="form-group {{ $errors->has('email') ? 'is-invalid' : '' }}">
                <label for="email"> Email Address </label>

                <input
                  type="email"
                  id="email"
                  name="email"
                  value="{{ old('email') }}"
                  placeholder="Enter your email"
                />
                @error('email')
                  <p class="form-error">{{ $message }}</p>
                @enderror
              </div>

              <div class="form-group {{ $errors->has('phone') ? 'is-invalid' : '' }}">
                <label for="phone"> Phone Number * </label>

                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  value="{{ old('phone') }}"
                  inputmode="numeric"
                  autocomplete="tel"
                  maxlength="10"
                  minlength="10"
                  pattern="[0-9]{10}"
                  title="Enter a 10-digit mobile number"
                  placeholder="10-digit mobile number"
                  data-digits-only="10"
                  required
                />
                @error('phone')
                  <p class="form-error">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="form-group {{ $errors->has('requirement') ? 'is-invalid' : '' }}">
              <label for="requirement"> What are you looking for? * </label>

              <select id="requirement" name="requirement" required>
                <option value="">Select your requirement</option>
                @foreach ($requirements as $option)
                  <option value="{{ $option }}" @selected(old('requirement') === $option)>{{ $option }}</option>
                @endforeach
              </select>
              @error('requirement')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            <div class="form-group {{ $errors->has('quantity') ? 'is-invalid' : '' }}">
              <label for="quantity"> Estimated Quantity </label>

              <input
                type="text"
                id="quantity"
                name="quantity"
                value="{{ old('quantity') }}"
                placeholder="Example: 500 KG / 1 Ton"
              />
              @error('quantity')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            <div class="form-group {{ $errors->has('message') ? 'is-invalid' : '' }}">
              <label for="message"> Your Message </label>

              <textarea
                id="message"
                name="message"
                rows="5"
                placeholder="Tell us about your requirement..."
              >{{ old('message') }}</textarea>
              @error('message')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            <button type="submit" class="submit-btn">
              Send Enquiry

              <i class="fa-solid fa-arrow-right"></i>
            </button>

            <p class="form-note">
              <i class="fa-solid fa-lock"></i>
              Your information is safe and will only be used to respond to your
              enquiry.
            </p>
          </form>
        </div>
      </div>
    </section>

    <!-- ================= MAP ================= -->

    <section class="map-section">
      <div class="container map-grid">
        <div class="map-content">
          <span class="section-tag"> VISIT US </span>

          <h2>
            Our location in
            <span>Mithilanchal, Bihar.</span>
          </h2>

          <p>
            We welcome business partners, wholesalers, distributors and food
            brands to connect with Mithilanchal Farms.
          </p>

          <a
            href="https://www.google.com/maps/search/?api=1&query=Alinagar+Darbhanga+Bihar"
            target="_blank"
            class="map-btn"
          >
            Open in Google Maps
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
          </a>
        </div>

        <div class="map-box">
          <iframe
            src="https://www.google.com/maps?q=Alinagar,Darbhanga,Bihar&output=embed"
            loading="lazy"
            title="Mithilanchal Farms Location"
          ></iframe>
        </div>
      </div>
    </section>

    <!-- ================= CTA ================= -->

    <section class="contact-cta">
      <div class="container">
        <span> HAVE A QUESTION? </span>

        <h2>Let's grow together.</h2>

        <p>
          Connect with us today and discover premium Makhana solutions for your
          business.
        </p>

        <div class="cta-buttons">
          <a
            href="https://wa.me/919296918101"
            target="_blank"
            class="whatsapp-btn"
          >
            <i class="fa-brands fa-whatsapp"></i>
            WhatsApp Us
          </a>

          <a href="tel:+919296918101" class="call-btn">
            <i class="fa-solid fa-phone"></i>
            Call Now
          </a>
        </div>
      </div>
    </section>

 </x-layout>