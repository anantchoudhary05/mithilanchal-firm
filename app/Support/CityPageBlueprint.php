<?php

declare(strict_types=1);

namespace App\Support;

final class CityPageBlueprint
{
    public const TEMPLATE_STANDARD = 'standard';

    public const TEMPLATE_MODERN = 'modern';

    public const TEMPLATE_MINIMAL = 'minimal';

    public const HERO = 'hero';

    public const STATS = 'stats';

    public const ABOUT = 'about';

    public const HIGHLIGHTS = 'highlights';

    public const ATTRACTIONS = 'attractions';

    public const SERVICES = 'services';

    public const WHY_CHOOSE = 'why_choose';

    public const PROCESS = 'process';

    public const NUTRITION = 'nutrition';

    public const GALLERY = 'gallery';

    public const TESTIMONIALS = 'testimonials';

    public const FAQ = 'faq';

    public const ADDITIONAL = 'additional';

    public const CTA = 'cta';

    public const LOCATION_BAR = 'location_bar';

    /**
     * @return array<string, string>
     */
    public static function templates(): array
    {
        return [
            self::TEMPLATE_STANDARD => 'Template 1 — Standard City',
            self::TEMPLATE_MODERN => 'Template 2 — Modern City',
            self::TEMPLATE_MINIMAL => 'Template 3 — Minimal / SEO City',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            'draft' => 'Draft',
            'published' => 'Published',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function icons(): array
    {
        return [
            'location-dot' => 'Location pin',
            'leaf' => 'Leaf',
            'seedling' => 'Sprout',
            'layer-group' => 'Grades / layers',
            'certificate' => 'Certificate',
            'star' => 'Star',
            'truck' => 'Delivery truck',
            'phone' => 'Phone',
            'clipboard' => 'Clipboard',
            'box-open' => 'Open box',
            'dumbbell' => 'Dumbbell',
            'bone' => 'Bone',
            'heart-pulse' => 'Heart',
            'fire-flame-simple' => 'Flame',
            'shield-halved' => 'Shield',
            'wheat-awn' => 'Grain',
            'medal' => 'Medal',
            'boxes-stacked' => 'Bulk boxes',
            'handshake' => 'Handshake',
            'tags' => 'Price tag',
            'check' => 'Check',
            'bowl-food' => 'Bowl',
            'heart' => 'Heart (simple)',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function iconClasses(): array
    {
        return [
            'location-dot' => 'fa-location-dot',
            'leaf' => 'fa-leaf',
            'seedling' => 'fa-seedling',
            'layer-group' => 'fa-layer-group',
            'certificate' => 'fa-certificate',
            'star' => 'fa-star',
            'truck' => 'fa-truck',
            'phone' => 'fa-phone',
            'clipboard' => 'fa-clipboard-list',
            'box-open' => 'fa-box-open',
            'dumbbell' => 'fa-dumbbell',
            'bone' => 'fa-bone',
            'heart-pulse' => 'fa-heart-pulse',
            'fire-flame-simple' => 'fa-fire-flame-simple',
            'shield-halved' => 'fa-shield-halved',
            'wheat-awn' => 'fa-wheat-awn',
            'medal' => 'fa-medal',
            'boxes-stacked' => 'fa-boxes-stacked',
            'handshake' => 'fa-handshake',
            'tags' => 'fa-tags',
            'check' => 'fa-circle-check',
            'bowl-food' => 'fa-bowl-food',
            'heart' => 'fa-heart',
        ];
    }

    public static function iconClass(string $icon): string
    {
        return self::iconClasses()[$icon] ?? 'fa-leaf';
    }

    /**
     * @return list<string>
     */
    public static function types(): array
    {
        return array_keys(self::definitions());
    }

    /**
     * @return array<string, array{label: string, order: int, enabled: bool}>
     */
    public static function definitions(): array
    {
        return [
            self::HERO => ['label' => 'Hero Section', 'order' => 10, 'enabled' => true],
            self::STATS => ['label' => 'Statistics Bar', 'order' => 20, 'enabled' => true],
            self::ABOUT => ['label' => 'City Introduction / About', 'order' => 30, 'enabled' => true],
            self::HIGHLIGHTS => ['label' => 'City Highlights / Key Information', 'order' => 40, 'enabled' => true],
            self::ATTRACTIONS => ['label' => 'Popular Places / Attractions', 'order' => 50, 'enabled' => false],
            self::SERVICES => ['label' => 'Services / Offerings', 'order' => 60, 'enabled' => false],
            self::WHY_CHOOSE => ['label' => 'Why Choose Us / Benefits', 'order' => 70, 'enabled' => true],
            self::PROCESS => ['label' => 'How It Works / Process', 'order' => 80, 'enabled' => true],
            self::NUTRITION => ['label' => 'Nutritional Value / Superfood', 'order' => 90, 'enabled' => true],
            self::GALLERY => ['label' => 'Gallery', 'order' => 100, 'enabled' => false],
            self::TESTIMONIALS => ['label' => 'Testimonials / Reviews', 'order' => 110, 'enabled' => true],
            self::FAQ => ['label' => 'FAQ', 'order' => 120, 'enabled' => true],
            self::ADDITIONAL => ['label' => 'Additional Content', 'order' => 130, 'enabled' => false],
            self::CTA => ['label' => 'Call to Action', 'order' => 140, 'enabled' => true],
            self::LOCATION_BAR => ['label' => 'Location Info Bar', 'order' => 150, 'enabled' => true],
        ];
    }

    public static function label(string $type): string
    {
        return self::definitions()[$type]['label'] ?? $type;
    }

    public static function defaultOrder(string $type): int
    {
        return self::definitions()[$type]['order'] ?? 100;
    }

    public static function defaultEnabled(string $type): bool
    {
        return self::definitions()[$type]['enabled'] ?? false;
    }

    public static function formKey(string $type): string
    {
        return 'sec_'.$type;
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultContent(string $type): array
    {
        return self::defaultContents()[$type] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultFormSection(string $type): array
    {
        return array_merge([
            'is_enabled' => self::defaultEnabled($type),
            'display_order' => self::defaultOrder($type),
        ], self::defaultContent($type));
    }

    /**
     * Placeholder copy uses {city} and {state}; replaced on the public page.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function defaultContents(): array
    {
        return [
            self::HERO => [
                'tag' => '{city}, {state} — Premium Makhana Hub',
                'title' => 'Premium {city} Makhana Supplier & Manufacturer',
                'description' => 'Buy premium quality {city} Makhana directly from trusted suppliers and manufacturers in {state}. We provide wholesale makhana with competitive makhana prices in {city} for retailers, exporters, and bulk buyers.',
                'image' => 'assests/img/hq-bowl.jpg',
                'image_alt' => 'Premium roasted {city} makhana in a wooden bowl',
                'primary_cta_text' => 'Call Now for Quote',
                'primary_cta_url' => 'tel:+919296918101',
                'secondary_cta_text' => 'Send Enquiry',
                'secondary_cta_url' => '/contact-us',
                'badges' => [
                    ['icon' => 'truck', 'title' => 'Fast Delivery', 'subtext' => 'Pan India'],
                    ['icon' => 'star', 'title' => 'Premium Grade', 'subtext' => 'Export Quality'],
                ],
            ],
            self::STATS => [
                'items' => [
                    ['value' => '15+', 'label' => 'Years in Business'],
                    ['value' => '500+', 'label' => 'Happy Buyers'],
                    ['value' => '100%', 'label' => 'Natural Product'],
                    ['value' => '20+', 'label' => 'Cities Served'],
                ],
            ],
            self::ABOUT => [
                'eyebrow' => 'About Mallah Makhana',
                'title' => 'Why Mallah Makhana is Famous in India',
                'description' => '{city} is a leading region for makhana production. Clean water bodies, traditional methods, and careful grading make {city} makhana a trusted choice for wholesale supply across India.',
                'image' => 'assests/img/hq-white.jpg',
                'image_alt' => 'Mallah Makhana product packaging',
                'inset_image' => 'assests/img/hq-bowl.jpg',
                'inset_image_alt' => 'Bowl of roasted fox nuts',
                'badge_text' => "Bihar's Best",
                'button_text' => '',
                'button_url' => '',
            ],
            self::HIGHLIGHTS => [
                'title' => '',
                'items' => [
                    [
                        'icon' => 'location-dot',
                        'title' => 'Source of Origin',
                        'description' => '{city}, {state}, sits in the Mithilanchal belt — the traditional home of premium fox nuts.',
                    ],
                    [
                        'icon' => 'leaf',
                        'title' => 'Naturally Organic',
                        'description' => 'Cultivated without harmful chemicals or preservatives, then graded for retail and export.',
                    ],
                    [
                        'icon' => 'layer-group',
                        'title' => 'Multiple Grades Available',
                        'description' => 'Choose 4 Sutta, 5 Sutta, 6 Sutta and other grades to match your market.',
                    ],
                    [
                        'icon' => 'certificate',
                        'title' => 'FSSAI & Export Certified',
                        'description' => 'Packed to food-safety standards with export-ready packaging on request.',
                    ],
                ],
            ],
            self::ATTRACTIONS => [
                'eyebrow' => 'Around the city',
                'title' => 'Places buyers visit in {city}',
                'description' => 'Useful local context for buyers travelling to {city} for makhana sourcing.',
                'items' => [],
            ],
            self::SERVICES => [
                'eyebrow' => 'What we supply',
                'title' => 'Makhana offerings from {city}',
                'description' => 'Wholesale, bulk and private-label supply for businesses that need reliable {city} makhana.',
                'items' => [
                    [
                        'icon' => 'bowl-food',
                        'title' => 'Premium Raw Makhana',
                        'description' => 'Carefully graded raw fox nuts for roasting, packing and export.',
                        'url' => '/product',
                    ],
                    [
                        'icon' => 'fire-flame-simple',
                        'title' => 'Roasted & Flavoured',
                        'description' => 'Ready-to-eat roasted makhana for retail and snack brands.',
                        'url' => '/product',
                    ],
                    [
                        'icon' => 'boxes-stacked',
                        'title' => 'Bulk & Private Label',
                        'description' => 'Moisture-proof packing and custom branding for distributors.',
                        'url' => '/product',
                    ],
                ],
            ],
            self::WHY_CHOOSE => [
                'eyebrow' => 'Why Mallah Makhana?',
                'title' => 'Finest Makhana in {city} from Mallah Makhana to all over {state}.',
                'description' => '{city} is a makhana hub. We source, grade and pack so wholesalers and brands get consistent quality at a fair price.',
                'items' => [
                    [
                        'icon' => 'leaf',
                        'title' => '100% Natural',
                        'description' => 'Pure {city} makhana processed naturally without harmful chemicals or preservatives.',
                        'featured' => false,
                    ],
                    [
                        'icon' => 'medal',
                        'title' => 'Premium Quality',
                        'description' => 'Carefully graded and selected makhana suitable for retail, wholesale, and export markets.',
                        'featured' => true,
                    ],
                    [
                        'icon' => 'boxes-stacked',
                        'title' => 'Bulk Supply',
                        'description' => 'Reliable bulk supply for wholesalers, distributors, snack brands, and food industries.',
                        'featured' => false,
                    ],
                    [
                        'icon' => 'handshake',
                        'title' => 'Direct from Farmers',
                        'description' => 'We work closely with makhana farmers in {city} to provide fair pricing and quality assurance.',
                        'featured' => false,
                    ],
                    [
                        'icon' => 'truck',
                        'title' => 'Fast Delivery',
                        'description' => 'Efficient logistics support ensures timely delivery of makhana across India.',
                        'featured' => false,
                    ],
                    [
                        'icon' => 'tags',
                        'title' => 'Best Makhana Price',
                        'description' => 'Get competitive makhana prices in {city} for wholesale and bulk orders directly from manufacturers.',
                        'featured' => false,
                    ],
                ],
            ],
            self::PROCESS => [
                'eyebrow' => 'Simple Process',
                'title' => 'How to Order {city} Makhana from Mallah Makhana',
                'title_highlight' => 'Order {city} Makhana',
                'description' => 'Getting premium {state} makhana delivered to your business is simple. Follow these 4 easy steps.',
                'items' => [
                    [
                        'number' => '01',
                        'icon' => 'phone',
                        'title' => 'Contact Us',
                        'description' => 'Call or WhatsApp us at +91 92969 18101 to discuss your requirements and quantities.',
                    ],
                    [
                        'number' => '02',
                        'icon' => 'clipboard',
                        'title' => 'Get Custom Quote',
                        'description' => 'Receive a tailored price quote based on your order volume, variety, and delivery location.',
                    ],
                    [
                        'number' => '03',
                        'icon' => 'box-open',
                        'title' => 'Confirm & Pack',
                        'description' => 'We carefully grade, inspect, and pack your makhana in food-safe, moisture-proof packaging.',
                    ],
                    [
                        'number' => '04',
                        'icon' => 'truck',
                        'title' => 'Swift Delivery',
                        'description' => 'Your fresh makhana is dispatched via our trusted logistics network to anywhere in India.',
                    ],
                ],
            ],
            self::NUTRITION => [
                'eyebrow' => 'Nutritional Value',
                'title' => 'Why Mallah Makhana is a Healthy Superfood',
                'title_highlight' => 'Mallah Makhana',
                'description' => 'Fox nuts from {city} are naturally rich in protein, calcium, antioxidants and fibre — a light snack with serious nutrition.',
                'items' => [
                    [
                        'icon' => 'dumbbell',
                        'title' => 'High in Protein',
                        'description' => 'Around 9–10g of plant protein per 100g, useful for everyday snacking and bulk food use.',
                    ],
                    [
                        'icon' => 'bone',
                        'title' => 'Calcium Rich',
                        'description' => '{city} makhana is a natural source of calcium that supports bone health.',
                    ],
                    [
                        'icon' => 'heart-pulse',
                        'title' => 'Heart Healthy',
                        'description' => 'Low sodium and low fat, a sensible choice for maintaining blood pressure.',
                    ],
                    [
                        'icon' => 'fire-flame-simple',
                        'title' => 'Low Calorie Snack',
                        'description' => 'High fibre and light calories make it suitable for weight-conscious diets.',
                    ],
                    [
                        'icon' => 'shield-halved',
                        'title' => 'Antioxidant Properties',
                        'description' => 'Natural flavonoids that help the body manage everyday inflammation.',
                    ],
                    [
                        'icon' => 'wheat-awn',
                        'title' => 'Gluten-Free',
                        'description' => 'Easy to digest and suitable for gluten-free and many specialised diets.',
                    ],
                ],
            ],
            self::GALLERY => [
                'eyebrow' => 'Gallery',
                'title' => '{city} makhana at a glance',
                'items' => [],
            ],
            self::TESTIMONIALS => [
                'eyebrow' => 'Customer Reviews',
                'title' => 'What Our {city} Buyers Say',
                'title_highlight' => '{city}',
                'items' => [
                    [
                        'name' => 'Rakesh Kumar',
                        'designation' => 'Wholesale Trader, {city}',
                        'review' => 'Consistent grade and packing. We reorder {city} makhana every season for our retail network.',
                        'rating' => 5,
                        'image' => '',
                    ],
                    [
                        'name' => 'Neha Sharma',
                        'designation' => 'Snack Brand Buyer, Delhi',
                        'review' => 'Clear communication, honest pricing, and fox nuts that match the sample. Reliable manufacturer.',
                        'rating' => 5,
                        'image' => '',
                    ],
                    [
                        'name' => 'Imran Ali',
                        'designation' => 'Exporter, Mumbai',
                        'review' => 'Export packing was neat and the moisture control was right. We will continue sourcing from {city}.',
                        'rating' => 5,
                        'image' => '',
                    ],
                ],
            ],
            self::FAQ => [
                'eyebrow' => 'FAQs',
                'title' => 'Frequently Asked Questions',
                'title_highlight' => 'Questions',
                'description' => 'Have questions about {city} Makhana or bulk orders? Contact Mallah Makhana directly at +91 92969 18101.',
                'items' => [
                    [
                        'question' => 'Why is {city} Makhana famous in India?',
                        'answer' => '{city} lies in the Mithilanchal belt of {state}, known for pond-grown fox nuts, traditional popping methods, and consistent wholesale supply.',
                    ],
                    [
                        'question' => 'Is Mallah Makhana a trusted makhana supplier in {city}?',
                        'answer' => 'Yes. Mallah Makhana works with local farmers, grades stock carefully, and supplies retailers, exporters and bulk buyers across India.',
                    ],
                    [
                        'question' => 'What is the current makhana price in {city}?',
                        'answer' => 'Price depends on grade, quantity and packing. Call or WhatsApp +91 92969 18101 for a live wholesale quote.',
                    ],
                    [
                        'question' => 'Do you provide bulk supply as a makhana manufacturer in {city}?',
                        'answer' => 'Yes. We pack moisture-proof bags for 50kg to multi-tonne orders and ship pan-India.',
                    ],
                    [
                        'question' => 'Which makhana grades are available at Mallah Makhana?',
                        'answer' => 'Typical grades include 4 Sutta, 5 Sutta and 6 Sutta, plus roasted and flavoured options on request.',
                    ],
                ],
            ],
            self::ADDITIONAL => [
                'title' => '',
                'body' => '',
            ],
            self::CTA => [
                'eyebrow' => 'Ready to order?',
                'title' => 'Get the Best Makhana Price in {city} Today',
                'description' => 'Whether you need 50kg or 5000kg — we have the stock and the expertise to fulfill your order with speed and quality.',
                'primary_cta_text' => 'Call: +91-9296918101',
                'primary_cta_url' => 'tel:+919296918101',
                'secondary_cta_text' => 'Email Us',
                'secondary_cta_url' => 'mailto:mallahmakhana@gmail.com',
                'whatsapp_text' => 'WhatsApp Us',
                'whatsapp_url' => 'https://wa.me/919296918101',
                'background_image' => '',
            ],
            self::LOCATION_BAR => [
                'title' => '{city}, {state} — Your Local Makhana Source',
                'description' => 'Visit us or place bulk orders via call, email, or WhatsApp. We serve all of {state} and India.',
                'primary_cta_text' => 'Call Now',
                'primary_cta_url' => 'tel:+919296918101',
                'secondary_cta_text' => 'WhatsApp',
                'secondary_cta_url' => 'https://wa.me/919296918101',
            ],
        ];
    }
}
