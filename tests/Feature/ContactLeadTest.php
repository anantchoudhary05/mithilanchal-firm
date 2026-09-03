<?php

use App\Models\ContactLead;
use App\Models\MoonshineUser;
use App\MoonShine\Resources\ContactLead\ContactLeadResource;
use App\Support\CmsRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

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
        ->assertSee('Mithila Traders');
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
        ->assertDontSee('Contact enquiries');
});
