<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactLeadRequest;
use App\Models\ContactLead;
use App\Support\ProductCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(Request $request): View
    {
        $product = trim((string) $request->query('product', ''));

        return view('ContactUs', [
            'requirements' => ContactLead::REQUIREMENTS,
            'enquiryProduct' => $product !== '' ? $product : null,
            'enquiryRequirement' => ProductCatalog::requirementFor($product !== '' ? $product : null),
        ]);
    }

    public function store(StoreContactLeadRequest $request): RedirectResponse
    {
        $lead = ContactLead::query()->create([
            ...$request->validated(),
            'status' => ContactLead::STATUS_NEW,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('contact.thankYou')
            ->with('contact_name', $lead->name);
    }

    public function thankYou(): View
    {
        return view('contact-thank-you', [
            'name' => session('contact_name'),
            'meta_title' => 'Thank You | Mithilanchal Farms',
            'meta_description' => 'Thank you for contacting Mithilanchal Farms. Our team will get back to you shortly.',
            'robots' => 'noindex, nofollow',
        ]);
    }
}
