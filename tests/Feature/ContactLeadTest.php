<?php

use App\Models\ContactLead;
use App\Models\MoonshineUser;
use App\MoonShine\Resources\ContactLead\ContactLeadResource;
use App\Support\CmsRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use ZipArchive;

uses(RefreshDatabase::class);

function validContactPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Priya Sharma',
        'company' => 'Sharma Foods',
        'email' => 'priya@example.com',
        'phone' => '9296918101',
        'requirement' => 'Bulk Wholesale Makhana',
        'quantity' => '500 KG',
        'message' => 'We need premium grade makhana for retail distribution.',
    ], $overrides);
}

it('shows the contact form on the public contact page', function () {
    $this->get(route('ContactUs'))
        ->assertOk()
        ->assertSee('Tell us what you')
        ->assertSee('name="name"', false)
        ->assertSee(route('contact.store'), false)
        ->assertSee('pattern="[0-9]{10}"', false);
});

it('stores a contact enquiry and redirects to the thank you page', function () {
    $response = $this->from(route('ContactUs'))
        ->post(route('contact.store'), validContactPayload());

    $response->assertRedirect(route('contact.thankYou'));

    $this->assertDatabaseHas('contact_leads', [
        'name' => 'Priya Sharma',
        'company' => 'Sharma Foods',
        'email' => 'priya@example.com',
        'phone' => '9296918101',
        'requirement' => 'Bulk Wholesale Makhana',
        'quantity' => '500 KG',
        'status' => ContactLead::STATUS_NEW,
    ]);

    $this->followRedirects($response)
        ->assertOk()
        ->assertSee('Thank you')
        ->assertSee('Priya Sharma')
        ->assertSee('noindex, nofollow', false);
});

it('stores an enquiry without email or message', function () {
    $this->from(route('ContactUs'))
        ->post(route('contact.store'), validContactPayload([
            'email' => '',
            'message' => '',
        ]))
        ->assertRedirect(route('contact.thankYou'))
        ->assertSessionDoesntHaveErrors();

    $this->assertDatabaseHas('contact_leads', [
        'name' => 'Priya Sharma',
        'phone' => '9296918101',
        'email' => null,
        'message' => null,
    ]);
});

it('rejects letters, symbols and the wrong length in the phone number', function () {
    $this->from(route('ContactUs'))
        ->post(route('contact.store'), validContactPayload([
            'phone' => '92969abc01',
        ]))
        ->assertRedirect(route('ContactUs'))
        ->assertSessionHasErrors('phone');

    $this->from(route('ContactUs'))
        ->post(route('contact.store'), validContactPayload([
            'phone' => '92969-1810',
        ]))
        ->assertRedirect(route('ContactUs'))
        ->assertSessionHasErrors('phone');

    $this->from(route('ContactUs'))
        ->post(route('contact.store'), validContactPayload([
            'phone' => '929691810',
        ]))
        ->assertRedirect(route('ContactUs'))
        ->assertSessionHasErrors('phone');

    expect(ContactLead::query()->count())->toBe(0);
});

it('rejects an incomplete contact enquiry but not a missing email or message', function () {
    $this->from(route('ContactUs'))
        ->post(route('contact.store'), validContactPayload([
            'name' => '',
            'email' => 'not-an-email',
            'requirement' => 'Not a real option',
            'message' => '',
        ]))
        ->assertRedirect(route('ContactUs'))
        ->assertSessionHasErrors(['name', 'email', 'requirement'])
        ->assertSessionDoesntHaveErrors('message');

    expect(ContactLead::query()->count())->toBe(0);
});

it('serves the thank you page', function () {
    $this->get(route('contact.thankYou'))
        ->assertOk()
        ->assertSee('Thank you')
        ->assertSee('noindex, nofollow', false);
});

it('shows new contact enquiries on the admin dashboard', function () {
    $admin = MoonshineUser::query()->create([
        'name' => 'Site Admin',
        'email' => 'admin-leads@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    ContactLead::factory()->create([
        'name' => 'Amit Kumar',
        'email' => 'amit@example.com',
        'requirement' => 'Export Enquiry',
    ]);

    $this->actingAs($admin, 'moonshine')
        ->get(route('moonshine.index'))
        ->assertOk()
        ->assertSee('Contact enquiries')
        ->assertSee('New enquiries')
        ->assertSee('View all leads')
        ->assertSee('S.No.')
        ->assertSee('Amit Kumar')
        ->assertSee('Export Enquiry');
});

it('lists contact enquiries for an admin in the cms', function () {
    $admin = MoonshineUser::query()->create([
        'name' => 'Site Admin',
        'email' => 'admin-leads-list@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    ContactLead::factory()->create([
        'name' => 'Rina Devi',
        'email' => 'rina@example.com',
        'company' => 'Mithila Traders',
    ]);

    $this->actingAs($admin, 'moonshine')
        ->get(app(ContactLeadResource::class)->getIndexPageUrl())
        ->assertOk()
        ->assertSee('Rina Devi')
        ->assertSee('Mithila Traders')
        ->assertSee('S.No.')
        ->assertSee('Export Excel');
});

it('does not show contact enquiries to an author', function () {
    $author = MoonshineUser::query()->create([
        'name' => 'Author User',
        'email' => 'author-leads@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::authorId(),
    ]);

    ContactLead::factory()->create([
        'name' => 'Secret Buyer',
        'email' => 'secret-buyer@example.com',
    ]);

    $this->actingAs($author, 'moonshine')
        ->get(route('moonshine.index'))
        ->assertOk()
        ->assertDontSee('Secret Buyer')
        ->assertDontSee('Contact enquiries')
        ->assertSee('Your writing');
});

it('shows a compact enquiry card instead of a long stacked form', function () {
    $admin = MoonshineUser::query()->create([
        'name' => 'Site Admin',
        'email' => 'admin-lead-detail@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    $lead = ContactLead::factory()->create([
        'name' => 'Neha Gupta',
        'company' => 'Gupta Exports',
        'email' => 'neha@example.com',
        'phone' => '9876543210',
        'requirement' => 'Private Label',
        'quantity' => '1 Ton',
        'message' => 'Need branded pouches for retail.',
    ]);

    $this->actingAs($admin, 'moonshine')
        ->get(app(ContactLeadResource::class)->getFormPageUrl($lead->getKey()))
        ->assertOk()
        ->assertSee('lead-summary', false)
        ->assertSee('Neha Gupta')
        ->assertSee('Gupta Exports')
        ->assertSee('9876543210')
        ->assertSee('Need branded pouches for retail.')
        ->assertSee('Follow-up')
        ->assertSee('Call')
        ->assertSee('WhatsApp')
        ->assertSee('tel:+919876543210', false)
        ->assertSee('https://wa.me/919876543210', false);
});

it('builds click-to-call and whatsapp links from a 10-digit phone', function () {
    $lead = ContactLead::factory()->make([
        'name' => 'Ravi',
        'phone' => '9296918101',
        'requirement' => 'Export Enquiry',
        'email' => 'ravi@example.com',
    ]);

    expect($lead->telHref())->toBe('tel:+919296918101')
        ->and($lead->whatsappHref())->toStartWith('https://wa.me/919296918101')
        ->and($lead->mailtoHref())->toStartWith('mailto:ravi@example.com');
});

it('exports new contacted and closed enquiries on three excel sheets', function () {
    ContactLead::factory()->create([
        'name' => 'New Buyer',
        'status' => ContactLead::STATUS_NEW,
        'requirement' => 'Premium Makhana',
    ]);
    ContactLead::factory()->contacted()->create([
        'name' => 'Called Buyer',
        'status' => ContactLead::STATUS_CONTACTED,
        'requirement' => 'Roasted Makhana',
    ]);
    ContactLead::factory()->closed()->create([
        'name' => 'Closed Buyer',
        'status' => ContactLead::STATUS_CLOSED,
        'requirement' => 'Private Label',
    ]);

    $path = (new \App\Exports\ContactLeadExcelExporter)->writeWorkbook();
    $zip = new ZipArchive;

    expect($zip->open($path))->toBeTrue();

    $workbook = (string) $zip->getFromName('xl/workbook.xml');
    $newSheet = (string) $zip->getFromName('xl/worksheets/sheet1.xml');
    $contactedSheet = (string) $zip->getFromName('xl/worksheets/sheet2.xml');
    $closedSheet = (string) $zip->getFromName('xl/worksheets/sheet3.xml');

    $zip->close();
    @unlink($path);

    expect($workbook)
        ->toContain('name="New"')
        ->toContain('name="Contacted"')
        ->toContain('name="Closed"')
        ->and($newSheet)->toContain('New Buyer')->not->toContain('Called Buyer')
        ->and($contactedSheet)->toContain('Called Buyer')->not->toContain('Closed Buyer')
        ->and($closedSheet)->toContain('Closed Buyer')->not->toContain('New Buyer');
});

it('lets an admin download the excel report from contact enquiries', function () {
    $admin = MoonshineUser::query()->create([
        'name' => 'Site Admin',
        'email' => 'admin-lead-export@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::ADMIN,
    ]);

    ContactLead::factory()->create(['name' => 'Export Row']);

    $this->actingAs($admin, 'moonshine')
        ->get(app(ContactLeadResource::class)->getRoute('handler', query: ['handlerUri' => 'excel-export']))
        ->assertOk()
        ->assertDownload();
});

it('does not let an author export contact enquiries', function () {
    $author = MoonshineUser::query()->create([
        'name' => 'Author User',
        'email' => 'author-lead-export@example.com',
        'password' => Hash::make('password'),
        'moonshine_user_role_id' => CmsRole::authorId(),
    ]);

    $this->actingAs($author, 'moonshine')
        ->get(app(ContactLeadResource::class)->getRoute('handler', query: ['handlerUri' => 'excel-export']))
        ->assertForbidden();
});
