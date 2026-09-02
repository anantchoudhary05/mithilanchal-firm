# Mithilanchal Farms — Full Project Baseline Report

**Breakpoint ID:** git `main` @ `bea46db` (tracked with `origin/main`)  
**Captured:** 2 September 2026  
**Purpose of this file:** This is the complete “what exists today” map of the codebase. When you add or change anything, compare against this document so you can see: (1) what was already here, (2) which file/function you touched, and (3) why the new lines exist.

Do not treat Laravel’s default README as the project spec. This file is the spec of *this* product.

---

## 1. What this project is

This is the company website for **Mithilanchal Farms Private Limited** (a unit of Mallah Makhana), a premium makhana / fox-nut manufacturer, wholesale supplier and exporter based in Motipur, Alinagar, Darbhanga, Bihar.

The product is **not** an e-commerce store. It is:

1. A **public marketing website** (Home, Products, Why Choose Us, Our Story, Contact, Blog).
2. A **MoonShine admin CMS** at `/admin` to create/edit/publish blog posts with SEO fields.
3. An **XML sitemap** at `/sitemap.xml` for search engines.

There is **no** public user registration, **no** shopping cart, **no** payment, **no** REST API, **no** working contact-form backend, and **no** product catalog stored in the database. Products on `/product` are hardcoded HTML.

---

## 2. Snapshot identity (use this as the “before” line)

| Item | Value |
|---|---|
| Workspace folder | `backend/mithilanchal-firm` |
| Git branch | `main` (clean, matches `origin/main`) |
| Commits so far | 3 (listed in section 3) |
| PHP | `^8.3` (composer.json) |
| Framework | Laravel `^13.17` |
| Admin | MoonShine `^4.18` |
| Editor | MoonShine TinyMCE `^2.0` |
| HTML sanitizer | `mews/purifier` `^3.4` |
| Tests | Pest `^5.1` |
| Frontend build | Vite `^8` + Tailwind `^4` (installed, **not used by public pages**) |
| Database | MySQL `mithilanchal_firm` (see `.env.example`) |
| Public site CSS/JS | Static files under `public/assests/` (folder name is misspelled: **assests**, not assets) |
| Admin URL prefix | `/admin` (`MOONSHINE_ROUTE_PREFIX`) |
| Health check | `GET /up` (Laravel default) |

---

## 3. Git history that exists today

| Hash | Message | What it actually changed |
|---|---|---|
| `d940da9` | Initial commit: Mithilanchal Farms Laravel site with MoonShine blog CMS | Entire application as described in this report |
| `0450633` | Fix Blog published_at carbon type warning | `app/Models/Blog.php` — `Carbon::now()` instead of `now()` when stamping `published_at` |
| `bea46db` | Add login button styles in header | Navbar Login link (`moonshine.login`), CSS for `.nav-login`, 1 line in `routes/web.php` |

If a future diff is not one of these three commits, it is **new work after this baseline**.

---

## 4. Feature inventory (complete — nothing omitted)

### 4.1 Implemented and working

| # | Feature | Where it lives | How a user uses it |
|---|---|---|---|
| F01 | Home / About marketing page | `GET /` → `resources/views/index.blade.php` | Public |
| F02 | Products marketing page | `GET /product` → `Products.blade.php` | Public, 6 hardcoded product cards |
| F03 | Why Choose Us page | `GET /why-choose-us` → `WhyChooseUs.blade.php` | Public |
| F04 | Our Story page | `GET /our-story` → `ourstory.blade.php` | Public |
| F05 | Contact Us page | `GET /contact-us` → `ContactUs.blade.php` | Public; form is **display only** |
| F06 | Shared header + footer + WhatsApp float | `components/header.blade.php`, `components/layout.blade.php` | All public pages |
| F07 | Blog listing | `GET /blog` → `BlogController@index` | Paginated 9 per page, published+active only |
| F08 | Blog detail by slug | `GET /blog/{slug}` → `BlogController@show` | 404 for drafts/inactive |
| F09 | Featured posts first | `BlogController@index` order | `is_featured` DESC then date |
| F10 | Auto unique slug | `Blog::booted` + `uniqueSlug()` | On save if slug empty or provided |
| F11 | HTML purify on save | `clean($blog->content)` via Purifier | XSS-safe content |
| F12 | Auto TOC from H2/H3 | `injectHeadingAnchors()` | Injects `id` on headings + HTML TOC |
| F13 | Auto heading structure text | `buildHeadingStructure()` | `H2: …` lines if field empty |
| F14 | Auto excerpt / meta title / image alt | `Blog::booted` saving hook | If those fields blank |
| F15 | Stamp `published_at` on first publish | `Blog::booted` | Only if status is `published` and date empty |
| F16 | Scheduled posts | `scopePublished` | `status=scheduled` AND `published_at <= now()` |
| F17 | Related blogs | `relatedBlogs()` + show view | IDs stored as JSON, order preserved |
| F18 | Related products chips | JSON on blog + show view | Title + URL, no Product model |
| F19 | FAQ accordion | JSON on blog + `<details>` | Question + answer |
| F20 | SEO meta + Open Graph + canonical | `layout.blade.php` | Per-page props from controllers/views |
| F21 | Custom JSON-LD schema | `custom_schema` field | Injected as `application/ld+json` |
| F22 | XML sitemap | `GET /sitemap.xml` | Static pages + published blogs, cached 1 hour |
| F23 | Blog cache invalidation | `clearBlogCaches()` | Forgets sitemap + listing keys 1–50 |
| F24 | MoonShine admin login | `/admin` | Separate `moonshine_users` table |
| F25 | Admin: Blogs CRUD | `BlogResource` | Create/edit/delete, no dedicated View page |
| F26 | Admin: TinyMCE editor | `BlogFormPage` + `config/moonshine_tinymce.php` | Rel dropdown: dofollow/nofollow/sponsored/ugc |
| F27 | Admin: featured image upload | Image field disk `public`, dir `blogs` | Needs `php artisan storage:link` |
| F28 | Admin: SEO tab | meta title/description/keywords, canonical, schema | |
| F29 | Admin: related blogs / products / FAQ tab | Select + JSON repeaters | |
| F30 | Admin: inline Active/Featured toggles | `BlogIndexPage` `updateOnPreview()` | |
| F31 | Admin: search + filters | id/title/slug/meta/author/status; status/type/active/featured | |
| F32 | Admin users + roles | MoonShineUser / MoonShineUserRole resources | System menu |
| F33 | Admin dashboard page | `App\MoonShine\Pages\Dashboard` | **Empty components array** |
| F34 | Bootstrap 5 pagination | `AppServiceProvider::boot` | Blog listing links |
| F35 | Pest tests for blog + sitemap | `tests/Feature/BlogTest.php` | 4 real tests |
| F36 | Blog factory + seeder | 6 random + 1 featured + 1 draft + 1 inactive | `php artisan db:seed` |
| F37 | Header Login button | `route('moonshine.login')` | Commit `bea46db` |
| F38 | Floating WhatsApp + tel/mailto | layout + several CTAs | Number `+91 92969 18101` |
| F39 | Google Maps embed | Contact page | Alinagar, Darbhanga |
| F40 | Mobile nav + scroll header + reveal animation | `public/assests/js/style.js` | **See known bug F-BUG-1** |
| F41 | `GET /up` health | Laravel bootstrap | Ops |

### 4.2 Present in schema/code but NOT wired to UI or behavior

| # | Item | Status as of this baseline |
|---|---|---|
| G01 | `blogs.is_sticky` | Column + fillable + factory/seeder. **No admin field. Listing does not order by sticky.** |
| G02 | `blogs.link_attribute` | Column default `dofollow`. **No admin field. Link rel is handled inside TinyMCE per-link instead.** |
| G03 | Contact `<form>` | Fields exist (name, company, email, phone, requirement, quantity, message). **No `action`, no controller, no table, no mail.** Submit does nothing useful. |
| G04 | Laravel `users` table / `User` model | Seeded test user only. **Not used for public login.** Admin uses `moonshine_users`. |
| G05 | Notifications table | Migration exists because MoonShine notifications flag is on. No app notification classes. |
| G06 | Jobs / queue | Tables exist; `composer run dev` starts `queue:listen`. **No Job classes in `app/`.** |
| G07 | Mail | `MAIL_MAILER=log`. **No Mailable classes.** |
| G08 | Vite + Tailwind (`resources/css/app.css`, `resources/js/app.js`) | Installed. Public pages do **not** `@vite`. They load `public/assests/css/*.css`. |
| G09 | `resources/js/app.js` | File is empty (`//`). |
| G10 | Dashboard widgets | `components()` returns `[]`. |
| G11 | `robots.txt` sitemap URL | Hardcoded `http://localhost:8000/sitemap.xml` — not production-ready. |

### 4.3 Known bugs / leftovers from static HTML (exist today — do not “rediscover” them)

| ID | Issue | File |
|---|---|---|
| F-BUG-1 | Layout loads `assests/js/script.js` but the real file is `assests/js/style.js`. Site JS (mobile menu etc.) may not run. | `layout.blade.php` line 134 vs `public/assests/js/style.js` |
| F-BUG-2 | Folder is spelled `assests` everywhere. Changing it later will break every `asset()` call. | `public/assests/` |
| F-BUG-3 | Home CTAs still use static HTML paths: `/ourstory.html`, `index.html#products`. | `index.blade.php` |
| F-BUG-4 | Why Choose Us CTAs use `ContactUs.html` instead of `route('ContactUs')`. | `WhyChooseUs.blade.php` |
| F-BUG-5 | Home uses Wikimedia/external image URLs, not local `public/assests/img`. | `index.blade.php` |
| F-BUG-6 | Blog listing fallback image path `assests/css/images/makhana.jpg` — that file is **not** in the repo (images live in `assests/img/`). | `blog/index.blade.php` |
| F-BUG-7 | `style.js` smooth-scroll has a broken ternary: `.header") ? offsetHeight` (undefined variable). | `public/assests/js/style.js` ~line 200 |
| F-BUG-8 | `clearBlogCaches()` still forgets `blogs.index.page.{1..50}` but `BlogController@index` **does not cache the paginator** (comment says unserialize broke Eloquent). Dead cache keys. | `Blog.php` vs `BlogController.php` |

---

## 5. Runtime request map

```
Browser
  │
  ├─ Public HTML pages ─────────────── routes/web.php closures ──► Blade views
  │                                      /, /product, /why-choose-us,
  │                                      /our-story, /contact-us
  │
  ├─ /blog, /blog/{slug} ───────────── BlogController ──► Blog::published() ──► MySQL blogs
  │
  ├─ /sitemap.xml ──────────────────── SitemapController ──► Cache 1h ──► sitemap.blade.php
  │
  ├─ /admin/* ──────────────────────── MoonShine (vendor + app/MoonShine)
  │                                      moonshine_users + blogs
  │
  └─ /up ───────────────────────────── Laravel health
```

Layout for every public page: `x-layout` → `<x-header />` + `$slot` + footer + WhatsApp float.

---

## 6. Folder and file map (application code only)

Vendor, `node_modules`, compiled `public/build`, and MoonShine published vendor assets are **framework/package files**, not product features. They are listed only where the app depends on them.

### 6.1 Root

| File | Role |
|---|---|
| `artisan` | Laravel CLI entry |
| `composer.json` / `composer.lock` | PHP deps + scripts `setup`, `dev`, `test` |
| `package.json` / `package-lock.json` | Vite/Tailwind; scripts `dev`, `build` |
| `vite.config.js` | Vite inputs `resources/css/app.css`, `resources/js/app.js`; Tailwind plugin; Bunny font Instrument Sans |
| `phpunit.xml` | Pest/PHPUnit; testing uses sqlite `:memory:` |
| `.env` / `.env.example` | Runtime config. **Never commit `.env`.** Example DB name `mithilanchal_firm`, TinyMCE token placeholder |
| `AGENTS.md` / `CLAUDE.md` / `boost.json` | Laravel Boost AI guidelines — not runtime |
| `README.md` | Stock Laravel readme — not this product’s docs |
| `PROJECT_BASELINE.md` | **This file** — baseline for future diffs |

Composer scripts that exist:

- `composer run setup` — install, copy env, key, migrate, npm install, npm build
- `composer run dev` — concurrently: `php artisan serve` + `queue:listen` + `npm run dev`
- `composer run test` — config:clear + artisan test

### 6.2 `bootstrap/`

| File | Role |
|---|---|
| `bootstrap/app.php` | Creates app; registers `routes/web.php`, `routes/console.php`, health `/up`; JSON errors for `api/*` (no API routes exist); empty middleware extra |
| `bootstrap/providers.php` | Registers `AppServiceProvider`, `MoonShineServiceProvider` |
| `bootstrap/cache/*` | Compiled package/service cache (generated) |

### 6.3 `routes/`

#### `routes/web.php` — every public route

| Method | URI | Name | Handler | Feature |
|---|---|---|---|---|
| GET | `/` | `home` | closure `view('index')` | F01 |
| GET | `/product` | `Product` | closure `view('Products')` | F02 |
| GET | `/why-choose-us` | `WhyChooseUs` | closure `view('WhyChooseUs')` | F03 |
| GET | `/our-story` | `OurStory` | closure `view('ourstory')` | F04 |
| GET | `/contact-us` | `ContactUs` | closure `view('ContactUs')` | F05 |
| GET | `/blog` | `blog.index` | `BlogController@index` | F07 |
| GET | `/blog/{slug}` | `blog.show` | `BlogController@show` | F08 |
| GET | `/sitemap.xml` | `sitemap` | `SitemapController@index` | F22 |

There is **no** `routes/api.php`. There is **no** POST route anywhere in app code.

MoonShine adds its own routes (login, resources, pages) via the package when `config/moonshine.php` `use_routes` is true. Prefix: `admin`. Named example used in header: `moonshine.login`.

#### `routes/console.php`

| Command | Purpose |
|---|---|
| `inspire` | Laravel demo — prints a quote. Not a product feature. |

No custom scheduled tasks.

### 6.4 `app/Http/Controllers/`

#### `Controller.php`

Empty abstract base. No helpers.

#### `BlogController.php`

| Method | Signature | What it does | Why it exists |
|---|---|---|---|
| `index` | `(Request $request): View` | Query `Blog::published()`, order featured → published_at → id, paginate 9, `withQueryString()`, pass listing SEO strings | Public blog index |
| `show` | `(string $slug): View` | `published()->where('slug')->firstOrFail()`, load `relatedBlogs()`, pass per-post SEO + schema + canonical | Public blog detail; 404 if not live |

Comment in `index`: listing **must not** cache the paginator because file/DB cache can unserialize models into strings and break `featured_image_url`.

Hardcoded listing meta:

- title: `Blog \| Mithilanchal Farms`
- description: Fox nuts recipes / nutrition / processing
- keywords: Fox Nuts, Makhana, …

#### `SitemapController.php`

| Method | What it does |
|---|---|
| `index(): Response` | `Cache::remember('blogs.sitemap', 1 hour, …)` builds URL array: home, Product, WhyChooseUs, OurStory, ContactUs, blog.index, then every published blog `loc/lastmod/changefreq/priority`. Renders `sitemap` view, prepends XML declaration, returns `Content-Type: application/xml`. |

### 6.5 `app/Models/`

#### `User.php` (Laravel default, unused by public site)

- Fillable via attributes: `name`, `email`, `password`
- Hidden: `password`, `remember_token`
- Casts: `email_verified_at` datetime, `password` hashed
- Traits: `HasFactory`, `Notifiable`
- **No relationships. No blog authorship.** Blog author is a free-text `author_name` string.

#### `Blog.php` — the only domain model

**Fillable columns:**  
`title, slug, content, excerpt, featured_image, image_alt, author_name, author_profile, status, content_type, is_active, is_featured, is_sticky, published_at, meta_title, meta_description, meta_keywords, canonical_url, link_attribute, custom_schema, heading_structure, table_of_contents, faq, related_blog_ids, related_products`

**Casts (`casts()`):**

| Attribute | Cast |
|---|---|
| `is_active`, `is_featured`, `is_sticky` | boolean |
| `published_at` | datetime |
| `faq`, `related_blog_ids`, `related_products` | array |

**Lifecycle (`booted()`):**

1. **`saving`**
   - If slug blank and title filled → `uniqueSlug(Str::slug(title), id)`
   - Else if slug filled → re-slug and uniquify (prevents duplicates)
   - If content filled → `clean()` (Purifier) → `injectHeadingAnchors()` writes content + `table_of_contents` → if `heading_structure` empty, `buildHeadingStructure()`
   - If `meta_title` blank → copy title
   - If `excerpt` blank → `Str::limit(strip_tags(content), 160)`
   - If `image_alt` blank → copy title
   - If `status === 'published'` and `published_at` blank → `Carbon::now()`
2. **`saved` / `deleted`** → `clearBlogCaches()`

**Public methods:**

| Method | Type | Behavior |
|---|---|---|
| `scopePublished(Builder): Builder` | local scope | `is_active = true` AND (`status = published` **OR** (`status = scheduled` AND `published_at` not null AND `published_at <= now()`)). Comment: for published posts, `published_at` is **display date only** (they go live immediately). Scheduled waits for the datetime. Draft/review never appear. |
| `getRouteKeyName(): string` | | returns `'slug'` (implicit route model binding would use slug; controller currently queries slug manually) |
| `getFeaturedImageUrlAttribute(): ?string` | accessor `featured_image_url` | `asset('storage/'.$featured_image)` or null |
| `getSeoTitleAttribute(): string` | accessor `seo_title` | `meta_title ?: title` |
| `relatedBlogs()` | instance | Reads JSON IDs, `published()->whereIn`, sorts by original ID order, returns Collection (not a relation) |
| `clearBlogCaches(): void` | static | `Cache::forget('blogs.sitemap')` and `blogs.index.page.1` … `50` |
| `uniqueSlug(string $base, ?int $ignoreId): string` | static | Appends `-1`, `-2`, … until unique; empty base becomes `post` |
| `injectHeadingAnchors(string $html): array` | static | Regex H2/H3; skip empty; slugify text as `id` unless heading already has `id`; builds `<ul class="blog-toc-list">` with H3 indented `ms-3`; returns `[$content, $tocHtml]` |
| `buildTableOfContents(string $html): string` | static | Wrapper around `injectHeadingAnchors()[1]` — **not called from elsewhere today** |
| `buildHeadingStructure(string $html): string` | static | All H1–H6 as `H2: text` newline-separated |

**Not present:** categories, tags, comments, users relation, media library, revisions.

### 6.6 `app/Providers/`

#### `AppServiceProvider`

| Method | Body |
|---|---|
| `register()` | empty |
| `boot()` | `Paginator::useBootstrapFive()` so blog pagination matches Bootstrap 5 markup (used with custom CSS) |

#### `MoonShineServiceProvider`

`boot(CoreContract $core)` registers resources:

1. `BlogResource`
2. `MoonShineUserResource`
3. `MoonShineUserRoleResource`

and default MoonShine pages from config.

### 6.7 `app/MoonShine/` — admin CMS

Admin layout class is `App\MoonShine\Layouts\MoonShineLayout` (set in `config/moonshine.php`).

#### `Layouts/MoonShineLayout.php`

| Method | Behavior |
|---|---|
| `$palette` | `PurplePalette` |
| `assets()` | parent assets only |
| `menu()` | Group **Content** → MenuItem Blogs (`BlogResource`), then `parent::menu()` (System: Admins, Roles) |
| `colors()` | parent only |

#### `Pages/Dashboard.php`

| Method | Behavior |
|---|---|
| `getBreadcrumbs()` | `'#' => title` |
| `getTitle()` | `'Dashboard'` if empty |
| `components()` | **empty list** — blank dashboard |

Skipped from menu via `#[SkipMenu]`.

#### Blog resource

**`Resources/Blog/BlogResource.php`**

| Piece | Value |
|---|---|
| Model | `Blog` |
| Title column | `title` |
| Menu | icon `newspaper`, group `Content`, order 1 |
| Title | `Blogs` |
| Pagination | not simple |
| Actions | parent minus `VIEW` (index + form only) |
| Pages | `BlogIndexPage`, `BlogFormPage` |
| Search | `id, title, slug, meta_title, author_name, status` |

**`Pages/BlogIndexPage.php`**

Index columns: ID (sortable), featured image (`public`/`blogs`, empty-string raw fallback), Title (sortable), Slug, Status select, Active switcher (inline update), Featured switcher (inline update), Published date `d M Y` (sortable).

Filters: Status, Content Type, Active, Featured.

**`Pages/BlogFormPage.php`**

Three tabs:

**Tab “Blog Content”**

- ID, Title (required), Slug (hint: auto from title)
- TinyMCE `content` (required; hint Ctrl+K + Rel dropdown)
- Excerpt textarea
- Featured image (jpg/jpeg/png/webp/gif, removable, disk public, dir blogs)
- Image ALT, Author Name, Author Profile
- Status: `draft | review | scheduled | published` default `published`
- Content Type: `guide | news | case_study | technical | comparison | product_education`
- Published/Scheduled Date with time
- Switchers: Active (default true), Featured
- Heading Structure textarea (auto if empty)
- Table of Contents HTML textarea (auto from H2/H3)

**Tab “SEO Control”**

- SEO Title, Meta Description, Meta Keywords, Canonical URL, Custom Schema JSON-LD

**Tab “Related & FAQ”**

- Related Blogs: multi searchable select of all blog titles by id
- Related Products JSON: `title`, `url`
- FAQ JSON: `question`, `answer`

**Validation `rules()`:** title required max 255; slug nullable max 255; content required; status in enum; content_type in enum or null; featured_image image mimes max 5120 KB; meta_title max 255; meta_description max 500; canonical_url url max 500.

**Not on the form:** `is_sticky`, `link_attribute`.

#### MoonShine users (package models, app-owned pages)

**`MoonShineUserResource`:** model `MoonshineUser`, with `moonshineUserRole`, simple paginate, search id/name, no VIEW action.

**`MoonShineUserIndexPage`:** ID, Role badge, Name, Avatar, Created `d.m.Y`, Email. Filters: role, email. Table has column selection.

**`MoonShineUserFormPage`:** Main tab — role BelongsTo (creatable), name, email, avatar, created_at. Password tab — password + confirmation with eye. Rules: name required, role required, unique email, avatar image, password required on create / optional on update, confirmed, `Password::defaults()`.

**`MoonShineUserRoleResource`:** create/edit/detail in modal, cursor paginate.

**`MoonShineUserRoleFormPage`:** name required min 5.

**`MoonShineUserRoleIndexPage`:** ID, name.

Default role seeded as **Admin** (`MoonshineUserRole::DEFAULT_ROLE_ID`).

### 6.8 `database/`

#### Migrations (run order)

| File | Tables | Product meaning |
|---|---|---|
| `0001_01_01_000000_create_users_table` | `users`, `password_reset_tokens`, `sessions` | Laravel default; sessions used because `SESSION_DRIVER=database` |
| `0001_01_01_000001_create_cache_table` | `cache`, `cache_locks` | Used because `CACHE_STORE=database`; sitemap cache lives here |
| `0001_01_01_000002_create_jobs_table` | `jobs`, `job_batches`, `failed_jobs` | Queue infrastructure; no app jobs yet |
| `2020_10_04_115514_create_moonshine_roles_table` | `moonshine_user_roles` + insert Admin | Admin RBAC |
| `2020_10_05_173148_create_moonshine_tables` | `moonshine_users` | Admin accounts |
| `2026_08_26_082002_create_notifications_table` | `notifications` | MoonShine DB notifications flag |
| `2026_08_26_111414_create_blogs_table` | `blogs` | **The only custom business table** |

#### `blogs` columns (authoritative schema)

| Column | Type | Default | Notes |
|---|---|---|---|
| `id` | bigint PK | | |
| `title` | string | | required in admin |
| `slug` | string unique + index | | auto from title |
| `content` | longText nullable | | purified HTML |
| `excerpt` | text nullable | | auto 160 chars |
| `featured_image` | string nullable | | path on public disk |
| `image_alt` | string nullable | | auto = title |
| `author_name` | string nullable | | not a User FK |
| `author_profile` | text nullable | | shown on detail |
| `status` | string | `draft` | draft/review/scheduled/published |
| `content_type` | string nullable | | 6 types |
| `is_active` | boolean | false | must be true to show |
| `is_featured` | boolean | false | listing sort |
| `is_sticky` | boolean | false | **unused in UI** |
| `published_at` | timestamp nullable | | display date or schedule time |
| `meta_title` | string nullable | | |
| `meta_description` | text nullable | | |
| `meta_keywords` | string nullable | | |
| `canonical_url` | string nullable | | else current blog URL |
| `link_attribute` | string | `dofollow` | **unused in UI** |
| `custom_schema` | longText nullable | | JSON-LD raw |
| `heading_structure` | text nullable | | auto outline |
| `table_of_contents` | longText nullable | | auto HTML |
| `faq` | json nullable | | `[{question, answer}]` |
| `related_blog_ids` | json nullable | | `[id, id]` |
| `related_products` | json nullable | | `[{title, url}]` |
| `created_at` / `updated_at` | timestamps | | sitemap lastmod uses updated_at |
| index | `(is_active, status, published_at)` | | listing/sitemap queries |

#### Factories

**`BlogFactory::definition()`** — unique title, HTML with H2/H3, published+active, random content_type, 20% featured, sample FAQ and related products pointing at `/product`.

States:

- `draft()` → status draft, inactive
- `inactive()` → `is_active` false

**`UserFactory`** — Laravel default; password `password`; `unverified()` state.

#### Seeders

**`DatabaseSeeder`:** creates `User` `test@example.com` (public users table), then `BlogSeeder`. Uses `WithoutModelEvents` so Blog `booted` hooks **do not run during seed** (slugs/TOC/caches from factory values as-is).

**`BlogSeeder`:** 6 factory posts; one featured sticky guide “Health Benefits of Premium Mithila Makhana” with schema.org Article JSON and related_blog_ids of first 2; one draft; one inactive published (proves listing hides it).

### 6.9 `resources/views/` — public UI

All pages wrap in `<x-layout>` except `sitemap.blade.php`.

#### `components/layout.blade.php`

Props: `title, meta_title, meta_description, meta_keywords, canonical_url, robots, custom_schema, og_type`.

Head: charset, viewport, title, description, keywords (page or default company keywords), author company, robots, canonical, OG title/description/type/url, theme-color `#155b27`, Google Fonts DM Sans + Playfair Display, **all six CSS files on every page**, Font Awesome 6.5.2 CDN, optional JSON-LD.

Body: `<x-header />`, `<main class="main">` slot, footer (logo, about, quick links, product links, contact address/phone/email, copyright year), floating WhatsApp `wa.me/919296918101`, script `assests/js/script.js` (**mismatch**).

Footer product links all go to `route('Product')` (no product detail routes).

#### `components/header.blade.php`

- Topbar: welcome text, tel, email
- Navbar: logo `mithanchal firm.html 2.png`, brand “MITHILANCHAL FARMS PRIVATE LIMITED / A Unit of Mallah Makhana”
- Hamburger `#menuToggle`
- Nav: Home `/`, Products, Why Choose Us, Our Story, Blog, Contact Us, **Login** → `moonshine.login`

#### `index.blade.php` (Home) — sections in order

1. Hero — “Rooted in Mithilanchal. Growing with Purpose.” CTAs to `/ourstory.html` and `index.html#products` (**broken vs Laravel routes**)
2. Company intro — who we are, Wikimedia image
3. Stats — 4+ years, Premium, Pan-India, Global
4. Farmers — check list of 5 points, Mongabay image
5. Values — 6 cards: Authenticity, Quality, Trust, Sustainability, Reliability, Global Vision
6. Product showcase gallery — 3 Wikimedia images
7. Mission (01) + Vision (02)
8. Our Story block (`#story`)
9. Quality process 01–06: Sourcing, Cleaning, Grading, Inspection, Packaging, Delivery
10. CTA — WhatsApp + Call

#### `Products.blade.php` — sections

1. Hero
2. Intro
3. Product grid (6 cards, hardcoded):
   - Premium Raw Makhana (PREMIUM) — `Raw-Makhana-2.webp`
   - Premium Makhana (BEST SELLER)
   - Roasted Makhana (READY TO EAT)
   - Flavoured Makhana (FLAVOURED)
   - Bulk Makhana (WHOLESALE)
   - Private Label Makhana (CUSTOM)
4. Quality: Naturally Sourced, Carefully Graded, Reliable Packaging, Reliable Supply
5. Wholesale CTA — WhatsApp

No database, no enquiry-per-product.

#### `WhyChooseUs.blade.php` — sections

1. Hero
2. Intro + 100% Mithilanchal Roots card
3. Why cards: Authentic Sourcing, Premium Quality, Trusted Partnership, Reliable Supply, Flexible Solutions, Naturally Focused
4. Process: Source → Select → Prepare → Deliver
5. Business benefits (bulk, retail, private-label, flexible) — CTA `ContactUs.html` (**broken**)
6. Stats: Bihar / Premium / B2B / Trust
7. CTA Get In Touch — `ContactUs.html` (**broken**)

#### `ourstory.blade.php` — sections

1. Hero (`makhana main.webp`)
2. Where it began (`#story`)
3. Belief statement
4. Farm to pack: Carefully Grown, Carefully Selected, Expertly Roasted, Freshly Packed
5. Farmers (`#` dead button)
6. Promise values: Authentic, Quality First, Fresh & Crunchy, Full of Flavour
7. Journey timeline: Beginning, Discovery, Craft, Brand, Future
8. CTA (`#` dead primary button)

#### `ContactUs.blade.php` — sections

1. Hero
2. Info cards: Visit (address), Call, Email, WhatsApp
3. Enquiry form fields (no backend): Full Name*, Company, Email*, Phone*, requirement select (7 options), Estimated Quantity, Message*, Submit
4. Map + Google Maps link/iframe Alinagar Darbhanga
5. CTA WhatsApp + Call

#### `blog/index.blade.php`

Hero “Our Latest Stories”. Grid of cards: image or fallback, date badge, featured badge, author, content_type, title, 140-char excerpt, Read More. Empty state. Pagination if needed.

#### `blog/show.blade.php`

`og_type=article`. Back link. Type badge, H1, byline, updated date if changed, author profile, featured figure, TOC aside (`{!! table_of_contents !!}`), body `{!! content !!}` (trusted after Purifier), FAQ `<details>`, related product chips, related blog cards.

#### `sitemap.blade.php`

`<urlset>` loop: loc, optional lastmod, changefreq, priority.

### 6.10 `public/`

| Path | Role |
|---|---|
| `index.php` | Front controller |
| `.htaccess` | Apache rewrite to index.php |
| `robots.txt` | Allow all; sitemap localhost URL |
| `favicon.ico` | empty/placeholder |
| `assests/css/style.css` | Global + home (~1233 lines); includes `.nav-login` from later commit |
| `assests/css/Product.css` | Products page |
| `assests/css/WhyChooseUs.css` | Why Choose Us |
| `assests/css/ourstory.css` | Our Story |
| `assests/css/contact.css` | Contact |
| `assests/css/blog.css` | Blog listing + detail (~440 lines) |
| `assests/js/style.js` | Mobile nav, header shadow, IntersectionObserver reveal, smooth anchors, image error, year replace |
| `assests/img/*` | Brand + product photos (webp/png). Logo filename contains `.html` (`mithanchal firm.html 2.png`) |
| `vendor/moonshine/` | Published admin UI |
| `vendor/moonshine-tinymce/` | TinyMCE assets |
| `storage` | symlink target after `storage:link` (gitignored) |
| `build/` | Vite output (gitignored) — unused by Blade today |

Image files committed:

- `5 sutta.webp`, `6 sutta.webp`, `7 sutta.webp`
- `Flavored makhana.webp`, `Raw-Makhana-2.webp`, `bulk makhana.webp`
- `makhana farmer.webp`, `makhana farming.webp`, `makhana main.webp`
- `mithanchal firm.html 2.png` (logo)
- `organic makhana.webp 2.webp`, `premium makhana.webp`, `private level.webp`, `roasted makhana.webp`

### 6.11 `config/` (product-relevant)

Laravel defaults exist for `app, auth, cache, database, filesystems, logging, mail, queue, services, session`.

Custom / customized:

| File | What was customized |
|---|---|
| `moonshine.php` | title, prefix `admin`, layout `App\MoonShine\Layouts\MoonShineLayout`, dashboard `App\MoonShine\Pages\Dashboard`, auth guard `moonshine`, disk `public`, notifications on, migrations on |
| `moonshine_tinymce.php` | plugins, full toolbar, `link_rel_list` DoFollow/NoFollow/Sponsored/UGC combos, default target `_blank` |
| `purifier.php` | Allowed tags include headings with `id`, links with `rel/target`, tables, figure; `Attr.EnableID` true so TOC anchors survive `clean()` |
| `filesystems.php` | extra disk `uploads` rooted at `storage/app/public/blogs` (Blog image field uses `public` disk + dir `blogs`, not this named disk) |

Auth: default guard `web` → `User` model. MoonShine uses its own guard from its config, not `config/auth.php` extra guard in this repo’s auth.php (MoonShine package registers it).

### 6.12 `tests/`

| File | What it covers |
|---|---|
| `Pest.php` | Feature tests extend `Tests\TestCase`; unused helper `something()`; `toBeOne` expectation |
| `TestCase.php` | empty subclass of Laravel TestCase |
| `Feature/ExampleTest.php` | `GET /` returns 200 |
| `Unit/ExampleTest.php` | `true` is true (placeholder) |
| `Feature/BlogTest.php` | **Real product tests** (RefreshDatabase): (1) listing shows only active published; (2) show by slug 200 + SEO title, draft 404; (3) auto slug `fresh-farm-story` and listing cache key cleared; (4) sitemap XML includes post URL |

Not tested: admin CRUD, scheduled posts, TOC generation, related blogs, contact form, static pages besides `/`.

### 6.13 Other folders

| Folder | Role |
|---|---|
| `storage/` | logs, cache, compiled views, sessions, public uploads, purifier cache |
| `lang/vendor/moonshine/` | Admin English translations |
| `.cursor/skills/` | Laravel Boost skills (conventions, best practices, tailwind, testing) — AI only |
| `vendor/` | Composer packages |
| `node_modules/` | NPM packages |
| `tests/` | Pest |

---

## 7. Public JS functions (`public/assests/js/style.js`)

There is no `app/js` module system. One IIFE-style script:

| Block | Function | Behavior |
|---|---|---|
| Mobile nav | click on `#menuToggle` | toggles `.nav.open`, swaps ☰/✕ and aria-label; link click closes menu |
| Header shadow | `updateHeader()` | box-shadow if `scrollY > 20` |
| Reveal | IntersectionObserver | `.value-card, .process-card, .mission-card` fade/slide in once |
| Injected CSS | creates `<style>` | `.revealed { opacity:1; transform:none }` |
| Smooth anchors | click `a[href^="#"]` | scroll to target minus header height (**bug in headerHeight expression**) |
| Image errors | `img` error listener | green background, console.warn |
| Year | `.footer-bottom span` | replace `2026` — **footer does not use that selector** (copyright is `.copyright`), so this is dead code |

---

## 8. Environment / ops notes that already exist

From `.env.example` (do not copy secrets from `.env` into git):

- `APP_URL=http://localhost:8000`
- `DB_CONNECTION=mysql`, `DB_DATABASE=mithilanchal_firm`
- `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`
- `MAIL_MAILER=log`
- `TINYMCE_TOKEN=` (free key from tiny.cloud)
- `FILESYSTEM_DISK=local` default; blog images still use disk `public` in MoonShine field

Required for featured images on the site: `php artisan storage:link` so `/storage/blogs/...` is web-accessible.

Admin login URL: `{APP_URL}/admin` (or `/admin/login`). Create first admin via MoonShine’s user seeder/UI (not in `DatabaseSeeder`).

---

## 9. How to use this file as a breakpoint when you add code

When you implement a new feature, append a short **Change Log** entry at the bottom (do not rewrite history above unless you intentionally replace a behavior). For each change record:

1. **Date / git hash** after the work
2. **Feature name**
3. **Files added** (new functions)
4. **Files edited** — quote “old behavior → new behavior”
5. **Why** (one sentence)
6. **Tests added**

Example template:

```
### YYYY-MM-DD — <short title>
- Why:
- Added:
- Changed (old → new):
- Removed:
- Tests:
- Still unused / still broken:
```

### Suggested first follow-ups (not done yet — listed so you do not think they already exist)

1. Wire contact form to a controller + mail/table.
2. Fix `script.js` vs `style.js`.
3. Replace leftover `.html` hrefs with named routes.
4. Either use `is_sticky` in listing or remove the column.
5. Point `robots.txt` sitemap at `APP_URL`.
6. Fill MoonShine Dashboard or hide it.
7. Product CMS (today products are HTML only).

---

## Change log (after baseline)

### 2026-09-02 — CMS roles, approval, dashboards, bugfixes

Full file-by-file learning log: **`UPDATE_REPORT.md`**.

- Why: Two CMS roles (Admin / Author), approval before a post goes live, dashboards, visible logout, and the bugs listed in section 4.3.
- Added: `app/Support/CmsRole.php`, `app/Support/CmsUser.php`, `app/Models/MoonshineUser.php`, `app/Http/Controllers/AdminLogoutController.php`, `app/MoonShine/Pages/ProfilePage.php`, migration `2026_09_02_091808_add_author_role_bio_and_blog_author_id`, `UPDATE_REPORT.md`, tests `CmsAuthTest`, `PublicSiteTest`.
- Changed (old → new): empty dashboard → role-aware metrics; free-text-only author → `author_id` + dropdown; `review` status → author “submit for approval”; broken `.html` links → named routes; `script.js` → `style.js`; sticky unused → listing + admin field.
- Removed: `public/robots.txt` (replaced by Laravel route); dead `blogs.index.page.*` cache forgets; dead year JS.
- Tests: 19 passing.
- Still unused / still broken: contact form backend, product CMS, Vite on public pages, `link_attribute` UI, `assests` spelling.

### 2026-09-02 — Login role selector

- Why: Login had no way to choose Admin vs Author; role was only stored on the account after sign-in.
- Added: `app/MoonShine/Forms/LoginForm.php`, `app/MoonShine/Auth/LoginWithSelectedRole.php`.
- Changed (old → new): stock MoonShine login → **Login as** dropdown; auth pipeline rejects a role that does not match the account.
- Tests: login page shows both roles; Admin cannot log in as Author and vice versa.

### 2026-09-02 — Blog list Approve button and metric cards

- Why: Count cards stacked full-width; admin had no one-click approve on the list.
- Changed: metrics `columnSpan(3, 3)` so four cards sit in one row; green **Approve** button left of Edit; `Blog::approve()` sets status published + Active ON; list label `published` → Approved.
- Tests: pending post is 404 until `approve()`, then it is live.

### 2026-09-02 — Preview everywhere; sticky removed

- Why: Admins and authors needed to read a post before approve. Sticky was unused extra UI that crowded the list actions.
- Added: `GET /cms/blogs/{blog}/preview` (`cms.blogs.preview`); Preview button on the blog list, edit form, and dashboards; yellow preview banner + `noindex`.
- Changed (old → new): listing sort sticky → featured first; Sticky switcher/filter/Pinned badge removed.
- Tests: admin can preview a draft; author can preview own pending post only; guests go to CMS login; public `/blog/{slug}` still 404s drafts.

### 2026-09-02 — Public login removed; TinyMCE sticky toolbar + image upload

- Why: Login did not belong on the marketing navbar. Long blog drafts scrolled the editor toolbar away. The image button asked for a URL and sizes instead of a file.
- Added: `POST /cms/tinymce/upload` (`cms.tinymce.upload`); `TinyMceImageUploadController` stores jpg/png/webp/gif under `storage/app/public/blogs/content`.
- Changed (old → new): public header Login/Dashboard links removed; TinyMCE menubar/toolbar stick while scrolling (`toolbar_sticky`, `max_height` 720); image dialog uses Upload + file picker, width/height fields off.
- Tests: home page has no CMS login URL; admin and author can upload; non-images rejected; guests redirected to login.

### 2026-09-02 — Compact CMS list actions

- Why: Preview/Edit/Delete sat off-screen because the blog table was too wide and Preview used a text label.
- Changed (old → new): Preview and Approve are icon-only (eye / check) with tooltips; slug dropped from the list; status is a short badge; action column sticks on the right; dashboards use the same eye + edit icons.
- Tests: status labels map review → Pending and published → Approved.

### 2026-09-02 — Authors can edit their own posts

- Why: Authors had no Edit action on approved posts and had to ask an admin to change them.
- Changed (old → new): authors can edit any post they own; delete stays limited to draft/pending. Saving a live post sends it back to approval and takes it off the website until an admin approves it again.
- Tests: author can edit own approved post, cannot delete it, cannot edit another author’s post.

---

## 10. Line-count sense of “where the product code is”

Almost all original behavior is in:

- `app/Models/Blog.php` (~228 lines) — **core business logic**
- `app/Http/Controllers/BlogController.php` + `SitemapController.php`
- `app/MoonShine/Resources/Blog/*` — **CMS UI**
- `resources/views/**` — **public pages**
- `public/assests/css/**` + `js/style.js` — **styling/interaction**
- `database/migrations/2026_08_26_111414_create_blogs_table.php` — **schema**

If a future PR does not touch those, it is either ops/config or a brand-new module.

---

*End of baseline. Anything not named in this document was not part of the product as of commit `bea46db`.*
