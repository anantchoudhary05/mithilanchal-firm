<x-layout
    :title="$meta_title"
    :meta_title="$meta_title"
    :meta_description="$meta_description"
    :robots="$robots"
>

    <section class="thankyou-page">
        <div class="container">
            <div class="thankyou-card">
                <div class="thankyou-icon" aria-hidden="true">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <span class="thankyou-tag"> ENQUIRY RECEIVED </span>

                <h1>
                    Thank you
                    @if (filled($name))
                        <span>{{ $name }}.</span>
                    @else
                        <span>for writing to us.</span>
                    @endif
                </h1>

                <p>
                    Your enquiry has reached the Mithilanchal Farms team.
                    We will review your requirement and get back to you shortly.
                </p>

                <div class="thankyou-actions">
                    <a href="{{ route('home') }}" class="thankyou-primary">
                        Back to Home
                    </a>

                    <a href="{{ route('ContactUs') }}" class="thankyou-secondary">
                        Send another enquiry
                    </a>

                    <a
                        href="https://wa.me/919296918101"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="thankyou-whatsapp"
                    >
                        <i class="fa-brands fa-whatsapp"></i>
                        WhatsApp Us
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-layout>
