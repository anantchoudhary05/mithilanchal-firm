@php
    /** @var \App\Models\ContactLead $lead */
    $blank = 'Not provided';
@endphp

<article class="lead-summary">
    <header class="lead-summary__header">
        <div>
            <p class="lead-summary__eyebrow">Contact enquiry</p>
            <h2 class="lead-summary__name">{{ $lead->name ?: 'Untitled enquiry' }}</h2>
            <p class="lead-summary__meta">Received {{ $lead->receivedLabel() }}</p>
        </div>
        <span class="lead-summary__status lead-summary__status--{{ $lead->status }}">
            {{ $lead->statusLabel() }}
        </span>
    </header>

    <div class="lead-summary__actions">
        @if ($lead->telHref())
            <a class="lead-chip lead-chip--call" href="{{ $lead->telHref() }}">Call</a>
        @endif
        @if ($lead->whatsappHref())
            <a class="lead-chip lead-chip--wa" href="{{ $lead->whatsappHref() }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>
        @endif
        @if ($lead->mailtoHref())
            <a class="lead-chip lead-chip--mail" href="{{ $lead->mailtoHref() }}">Email</a>
        @endif
    </div>

    <dl class="lead-summary__grid">
        <div class="lead-summary__cell">
            <dt>Full name</dt>
            <dd>{{ $lead->name ?: $blank }}</dd>
        </div>
        <div class="lead-summary__cell">
            <dt>Company</dt>
            <dd>{{ $lead->company ?: $blank }}</dd>
        </div>
        <div class="lead-summary__cell">
            <dt>Phone</dt>
            <dd>
                @if ($lead->telHref())
                    <a href="{{ $lead->telHref() }}">{{ $lead->phone }}</a>
                @else
                    {{ $blank }}
                @endif
            </dd>
        </div>
        <div class="lead-summary__cell">
            <dt>Email</dt>
            <dd>
                @if ($lead->mailtoHref())
                    <a href="{{ $lead->mailtoHref() }}">{{ $lead->email }}</a>
                @else
                    {{ $blank }}
                @endif
            </dd>
        </div>
        <div class="lead-summary__cell">
            <dt>Requirement</dt>
            <dd>{{ $lead->requirement ?: $blank }}</dd>
        </div>
        <div class="lead-summary__cell">
            <dt>Estimated quantity</dt>
            <dd>{{ $lead->quantity ?: $blank }}</dd>
        </div>
        <div class="lead-summary__cell lead-summary__cell--wide">
            <dt>Message</dt>
            <dd class="lead-summary__message">{{ $lead->message ?: $blank }}</dd>
        </div>
    </dl>
</article>
