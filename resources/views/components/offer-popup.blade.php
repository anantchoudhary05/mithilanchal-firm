@props([
    'section',
])

@php
    /** @var \App\Models\HomepageSection $section */
@endphp

<div
    class="offer-popup"
    data-offer-popup
    data-offer-id="{{ $section->id }}"
    hidden
>
    <div class="offer-popup__backdrop" data-offer-close></div>
    <div
        class="offer-popup__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="offer-popup-title"
    >
        <x-offer-card :section="$section" variant="popup" />
    </div>
</div>
<script>
(function () {
    var popup = document.querySelector('[data-offer-popup]');
    if (!popup) {
        return;
    }

    var offerId = popup.getAttribute('data-offer-id');
    var sessionKey = 'mithilanchal-offer-session-' + offerId;
    var oldKey = 'mithilanchal-offer-dismissed-' + offerId;

    try {
        window.localStorage.removeItem(oldKey);
    } catch (error) {}

    var dismissed = false;
    try {
        dismissed = window.sessionStorage.getItem(sessionKey) === '1';
    } catch (error) {
        dismissed = false;
    }

    if (dismissed) {
        popup.hidden = true;
        document.body.classList.remove('offer-popup-open');
        return;
    }

    popup.hidden = false;
    document.body.classList.add('offer-popup-open');
})();
</script>
