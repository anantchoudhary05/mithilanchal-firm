# Mithilanchal Farms — Update Report

**Latest session:** 4 September 2026 — see [section 12](#12-4-september-2026--blog-seo-label--related-sidebar)  
**Original date:** 2 September 2026  
**Compared against:** `PROJECT_BASELINE.md` (git `main` @ `bea46db`)  
**Tests:** 54 Pest tests passing (`php artisan test`)  
**Required on your machine:** `php artisan migrate` (MySQL was not running during the 2 Sep session, so the new columns/role were applied in tests only)

This file is the learning log for this change set. For each file, it records **what changed**, **why**, and **how the CMS pieces connect**.

---

## 1. What you asked for, and what was delivered

| # | Request | Result |
|---|---|---|
| 1 | Fix current website bugs | Broken `.html` links, missing JS, bad fallback image, broken smooth-scroll, dead cache keys, robots sitemap URL, external home images |
| 2 | Unused CMS code: implement or remove | Sticky switcher **removed** (column kept unused). Dead listing-cache keys removed. Post-level `link_attribute` left in DB (TinyMCE already handles per-link Rel) |
| 3 | Two MoonShine roles: Author + Admin | Role `Author` is inserted by migration. Existing role `Admin` (id `1`) stays superuser |
| 4 | Author writes → asks admin to approve → then live | Author can only set Draft or Submit for approval. Admin publishes + Active ON |
| 5 | Admin creates authors and picks author from dropdown | People → Authors. Blog form Author select is required for admins |
| 6 | Author dashboard (counts + their posts) | Dashboard switches by role |
| 7 | Admin dashboard (all authors, counts, pending) | Same Dashboard page, admin layout |
| 8 | Visible logout that returns to login | Sidebar **Log out** + header name/exit button. Route `GET /cms/logout` |
| 9 | Login role selector (Admin vs Author) | Login form **Login as** dropdown. Credentials must match the chosen role |

---

## 2. How the CMS workflow works now

```
Admin login (/admin)
  ├─ Login as: Admin + email + password
  ├─ Dashboard: published / pending / drafts / author count + author table
  ├─ Blogs: all posts, Approve by setting Status = Published + Active ON
  ├─ Authors: create MoonShine users with role Author (or Admin)
  ├─ Roles: Admin + Author
  ├─ My profile / Log out
  └─ Blog form Author dropdown = every CMS user (name + role)

Author login (/admin)
  ├─ Login as: Author + email + password
  ├─ Dashboard: my total / live / pending / drafts + recent posts
  ├─ My Blogs: only that author’s rows
  ├─ Create or edit a post as Draft, or Submit for approval (status = review)
  ├─ Cannot publish, feature, or activate a post
  ├─ Can edit any of their own posts (a live post goes back to approval after save)
  ├─ Cannot delete a post once it is published (ask admin)
  ├─ Cannot see Authors / Roles
  └─ My profile (name, bio, password) / Log out
```

Public site still shows only `Blog::published()` rows (`is_active = true` and status `published`, or due `scheduled` posts).

When an admin picks an author, `blogs.author_id` is stored and `author_name` / `author_profile` are copied from that user for the public byline.

---

## 3. Unused / leftover analysis (baseline G01–G11)

| ID | Item | Decision | Why |
|---|---|---|---|
| G01 | `blogs.is_sticky` | **Removed from UI** | Column stays in the database. Sticky switcher, listing sort, and Pinned badge are gone so the list has room for Preview |
| G02 | `blogs.link_attribute` | **Kept in DB, not shown** | TinyMCE Rel dropdown already sets dofollow/nofollow per link. A second post-level field would confuse editors |
| G03 | Contact form | **Unchanged on 2 Sep** (wired on **3 Sep** — see section 10) | Public marketing form still had no backend in the CMS-role change set |
| G04 | Laravel `users` table | **Unchanged** | Public site has no customer login. CMS uses `moonshine_users` |
| G05 | Notifications table | **Unchanged** | MoonShine package flag, not app code |
| G06 | Jobs / queue | **Unchanged** | Infrastructure only |
| G07 | Mail | **Unchanged** | Still `log` mailer, no Mailables |
| G08–G09 | Vite / empty `app.js` | **Unchanged** | Public pages load `public/assests/css`, not Vite |
| G10 | Empty dashboard | **Implemented** | Role-aware metrics + tables |
| G11 | `robots.txt` localhost sitemap | **Fixed** | Laravel route now prints `Sitemap: {APP_URL}/sitemap.xml` |
| — | `Blog::clearBlogCaches()` keys `blogs.index.page.1..50` | **Removed** | Listing is not cached; those forget() calls were dead |
| — | `buildTableOfContents()` | **Kept** | Thin helper around heading-anchor logic |
| — | Year JS in `style.js` | **Removed** | Footer already uses Blade `date('Y')`; selector `.footer-bottom span` did not exist |

---

## 4. Bug fixes (baseline F-BUG-*)

| ID | Fix |
|---|---|
| F-BUG-1 | `layout.blade.php` now loads `assests/js/style.js` (the file that actually exists) |
| F-BUG-2 | Folder still spelled `assests` on purpose — renaming would break every `asset()` call |
| F-BUG-3 | Home CTAs use `route('OurStory')` and `route('Product')` |
| F-BUG-4 | Why Choose Us + Products CTAs use `route('ContactUs')` |
| F-BUG-5 | Home images use local `public/assests/img/*` instead of Wikimedia / Mongabay / external CDN |
| F-BUG-6 | Blog card fallback image is `assests/img/makhana main.webp` |
| F-BUG-7 | Smooth-scroll uses `header.offsetHeight` instead of an undefined `offsetHeight` |
| F-BUG-8 | Dead paginator cache keys removed from `Blog::clearBlogCaches()` |

Also: Our Story dead `#` buttons now go to Contact / Products. Header Login becomes **Dashboard** when a MoonShine user is already logged in.

---

## 5. File-by-file change log

### New files

#### `app/Support/CmsRole.php`
Holds CMS role names and IDs. `ADMIN` is MoonShine’s default role id `1`. `authorId()` looks up the `Author` row so the id does not have to stay `2` forever.

#### `app/Support/CmsUser.php`
Reads the current MoonShine guard user. `isAdmin()` / `isAuthor()` are used by menu, resources, and the dashboard so role checks live in one place.

#### `app/Models/MoonshineUser.php`
Extends MoonShine’s user model. Adds `bio`, `blogs()` (`hasMany` Blog via `author_id`), `isAdmin()`, `isAuthor()`. Config points the `moonshine` guard at this class so auth users have those methods.

#### `app/Http/Controllers/AdminLogoutController.php`
Logs out the `moonshine` guard, invalidates the session, regenerates the CSRF token, redirects to `moonshine.login`. Used by the sidebar **Log out** link (`GET /cms/logout`). MoonShine’s own profile button still uses `DELETE /admin/logout`.

#### `app/MoonShine/Pages/ProfilePage.php`
Replaces the stock profile page. Adds **Author bio** so authors can edit the text that appears on public posts.

#### `database/migrations/2026_09_02_091808_add_author_role_bio_and_blog_author_id.php`
1. Inserts role `Author` if missing.  
2. Adds nullable `moonshine_users.bio`.  
3. Adds nullable `blogs.author_id` FK to `moonshine_users` (`nullOnDelete` so deleting a user does not delete their posts; the public `author_name` string remains).

#### `app/MoonShine/Forms/LoginForm.php`
Replaces MoonShine’s stock login form. Adds a required **Login as** dropdown (`Admin` / `Author`) above email and password.

#### `app/MoonShine/Auth/LoginWithSelectedRole.php`
MoonShine auth pipeline. After email/password succeed, the account’s stored role must match the dropdown. Mismatch logs the user back out and shows an error on **Login as**.

#### `tests/Feature/CmsAuthTest.php`
Author role exists; an Author user can be created; logout returns to login; login page shows Admin/Author; wrong role is rejected.

#### `tests/Feature/PublicSiteTest.php`
Marketing routes return 200; leftover `.html` hrefs are gone; `robots.txt` includes the current app sitemap URL.

#### `UPDATE_REPORT.md`
This file.

---

### Edited application files

#### `app/Models/Blog.php`
- Fillable: `author_id`.
- `author()` belongsTo `MoonshineUser`.
- Scopes: `authoredBy()`, `pendingApproval()`.
- Helpers: `isLive()`, `isOwnedBy()`, `syncAuthorDisplayFields()`, `previewUrl()`.
- On save, if `author_id` is set, copy that user’s `name` into `author_name`, and copy `bio` into `author_profile` when the profile field is empty.
- `clearBlogCaches()` only forgets `blogs.sitemap`.

**Why:** Public pages still print `author_name`. Linking to a real CMS user is what makes “post as this author” and dashboards possible.

#### `app/Http/Controllers/BlogController.php`
Listing order is now featured → `published_at` → id. Sticky is no longer used.

#### `app/MoonShine/Layouts/MoonShineLayout.php`
Custom sidebar: Dashboard, Blogs / My Blogs, People (admin only), My profile, **Log out**.  
`getProfileComponent()` no longer wraps Profile in a tiny avatar dropdown, so the header shows the user name plus an exit icon.

#### `app/MoonShine/Pages/Dashboard.php`
Empty `components()` replaced with admin vs author widgets (`ValueMetric` + `TableBuilder`). Pending and recent tables include a Preview link.

#### `app/MoonShine/Resources/Blog/BlogResource.php`
- Authors only query `author_id = current user`.
- `isCan()`: authors may view/create; update any of their posts; delete only their **draft/review** posts; no mass delete.
- Shared `getPreviewButton()` (opens `/cms/blogs/{slug}/preview` in a new tab).
- `beforeCreating` / `beforeUpdating`: authors are forced onto themselves, status draft/review, and cannot set active/featured.

#### `app/MoonShine/Resources/Blog/Pages/BlogFormPage.php`
Admin: Author dropdown, profile textarea, full status list, dates, Active / Featured, top-bar **Preview**.  
Author: name preview, Draft vs Submit for approval only, same **Preview** button on saved posts.  
Status label `review` is shown as “Awaiting approval” / “Submit for approval”.

#### `app/MoonShine/Resources/Blog/Pages/BlogIndexPage.php`
Author column, query tags (Awaiting approval / Approved / Drafts). Count cards use `columnSpan(3, 3)` so they sit in one row. Icon-only **Preview** (eye), **Approve** (check, pending only), Edit, and Delete. Action column sticks to the right. Slug is not shown in the table.

#### `app/MoonShine/Resources/MoonShineUser/MoonShineUserResource.php`
Title **Authors**. Hidden from authors (`canSee` + `isCan`). `withCount` for total / published / pending posts.

#### `app/MoonShine/Resources/MoonShineUser/Pages/MoonShineUserFormPage.php`
Default role = Author. Bio field. Removed “creatable” role popup so people cannot invent extra roles from this form.

#### `app/MoonShine/Resources/MoonShineUser/Pages/MoonShineUserIndexPage.php`
Columns: Total posts, Published, Pending.

#### `app/MoonShine/Resources/MoonShineUserRole/MoonShineUserRoleResource.php`
Admin-only. Menu group **People**.

#### `config/moonshine.php`
Title default `Mithilanchal CMS`. Auth model = `App\Models\MoonshineUser`. Profile page = custom `App\MoonShine\Pages\ProfilePage`. Login form = `App\MoonShine\Forms\LoginForm`. Auth pipeline = `LoginWithSelectedRole` so the selected role is checked on sign-in.

#### `config/moonshine_tinymce.php`
Sticky menubar/toolbar while scrolling long posts. Image dialog: Upload tab + local file picker, no width/height fields. Uploads POST to `/cms/tinymce/upload`.

#### `routes/web.php`
- `GET /robots.txt` — dynamic sitemap URL from `APP_URL`.
- `GET /cms/logout` — named `cms.logout`.
- `GET /cms/blogs/{blog}/preview` — CMS preview (MoonShine login required). Named `cms.blogs.preview`.
- `POST /cms/tinymce/upload` — TinyMCE content image upload. Named `cms.tinymce.upload`.

#### `database/factories/BlogFactory.php`
Sets `author_id` null for factory posts (legacy/public-name-only still valid).

#### `tests/Feature/BlogTest.php`
Cache assertion now uses `blogs.sitemap`. Added featured-first listing test, CMS preview auth tests, and author byline sync test.

---

### Edited public views / assets

| File | Change |
|---|---|
| `resources/views/components/layout.blade.php` | Script `style.js`; robots no longer involved here |
| `resources/views/components/header.blade.php` | Public navbar has no Login/Dashboard; CMS is only at `/admin` |
| `resources/views/index.blade.php` | Laravel routes + local images |
| `resources/views/Products.blade.php` | `ContactUs.html` → `route('ContactUs')` (7 links) |
| `resources/views/WhyChooseUs.blade.php` | same (2 links) |
| `resources/views/ourstory.blade.php` | Learn More → Contact; Shop → Products |
| `resources/views/blog/index.blade.php` | Real fallback image; Featured badge only (Pinned/sticky removed) |
| `resources/views/blog/show.blade.php` | Yellow CMS preview banner when `$isPreview` is set |
| `public/assests/css/blog.css` | Preview banner styles |
| `public/robots.txt` | **Deleted** so Laravel can serve the route (static files win over routes) |

---

## 6. Database you need to run

When MySQL is up:

```bash
php artisan migrate
```

That creates:

- row `moonshine_user_roles.name = Author`
- column `moonshine_users.bio`
- column `blogs.author_id`

Existing admin accounts keep role **Admin**. Create writers under **People → Authors** (role Author, email + password).

---

## 7. How to try it

1. Migrate (above).  
2. Open `/admin`. On the login form choose **Login as → Admin** (or Author), then email and password.  
   An Admin account cannot sign in with Author selected, and the reverse is also blocked.  
3. Confirm sidebar **Log out** — after click you should see the login form again.  
4. Create an Author. Log out. Log in as that author.  
5. Write a blog, set status **Submit for approval**, save. The public `/blog` page must **not** show it.  
6. Log in as admin. Dashboard should list the pending post. Open **Preview** to read it as it will look on the site. Then use **Approve** (or set **Approved** and **Active ON**).  
7. Reload `/blog` — the post should appear under the author’s name. `/blog/{slug}` still 404s for drafts; only `/cms/blogs/{slug}/preview` shows them to signed-in CMS users.

---

## 8. Still unused / not in this change

- Contact form **did not** store enquiries in this 2 Sep change set (wired on 3 Sep — see section 10). Still no enquiry email.
- Products remain hardcoded HTML (no product CMS).
- Vite + Tailwind still unused by public pages.
- Laravel `users` table still unused.
- Folder name `assests` is still misspelled.
- `link_attribute` column still unused in UI (TinyMCE Rel covers it).
- `is_sticky` column still exists but has no admin field and does not change listing order.

---

## 9. Tests added or changed

| Test file | What it proves |
|---|---|
| `BlogTest` | Live listing, draft 404, slug + sitemap cache, featured order, CMS preview (admin/author/guest), author name/bio copy, approve makes a pending post live |
| `CmsAuthTest` | Author role seed, author user flags, logout → login + guest, login page shows roles, wrong role rejected, matching role logs in |
| `PublicSiteTest` | Pages 200, no leftover `.html`, robots.txt uses `url('/sitemap.xml')`, public navbar has no CMS Login |
| `TinyMceUploadTest` | Admin/author can upload an editor image; PDF rejected; guests go to CMS login |
| `ExampleTest` | Home 200 (unchanged) |

Run: `php artisan test`

---

## 10. Later updates (3 September 2026)

This section is **after** the CMS-role change set above. Test count at this date: **45** Pest tests passing.

### 10.1 What you asked for, and what was delivered

| # | Request | Result |
|---|---|---|
| 1 | Public UI polish | Home slideshow, counting stats, values grid, shared `theme.css` motion, contact form error styles, thank-you page |
| 2 | Make Contact Us workable | Submissions save as `contact_leads`. Admin dashboard + **Leads → Contact enquiries** |
| 3 | Email and message not mandatory | Both nullable in DB, form, and `StoreContactLeadRequest` |
| 4 | Phone = 10 digits only | HTML `pattern` + JS strips letters/symbols + server `digits:10` |
| 5 | Thank you page after submit | `GET /contact-us/thank-you` (`contact.thankYou`), `noindex` |

### 10.2 How a contact enquiry flows now

```
Public /contact-us
  ├─ Required: name, 10-digit phone, requirement
  ├─ Optional: company, email, quantity, message
  ├─ POST /contact-us (throttle 5/min) → contact_leads row, status = new
  └─ Redirect → /contact-us/thank-you  (shows the visitor’s name)

Admin login (/admin)
  ├─ Dashboard: New / Contacted / Total enquiries + last 10 leads
  ├─ Leads → Contact enquiries: search, filter, mark contacted, notes, delete
  └─ Authors do not see this menu or these numbers
```

No Mailable is sent. The lead lives in the database for the admin to call/WhatsApp.

### 10.3 File-by-file (contact + related UI)

#### New files

| File | Role |
|---|---|
| `app/Models/ContactLead.php` | Lead model; statuses `new` / `contacted` / `closed`; requirement list |
| `app/Http/Controllers/ContactController.php` | `show`, `store`, `thankYou` |
| `app/Http/Requests/StoreContactLeadRequest.php` | Validation (phone `digits:10`; email/message nullable) |
| `app/MoonShine/Resources/ContactLead/ContactLeadResource.php` | Admin-only CMS resource |
| `app/MoonShine/Resources/ContactLead/Pages/ContactLeadIndexPage.php` | List, metrics, mark-as-contacted |
| `app/MoonShine/Resources/ContactLead/Pages/ContactLeadFormPage.php` | Read-only enquiry + status/notes |
| `database/migrations/2026_09_03_110000_create_contact_leads_table.php` | `contact_leads` |
| `database/migrations/2026_09_03_112500_make_contact_lead_email_and_message_nullable.php` | Email/message nullable on MySQL |
| `database/factories/ContactLeadFactory.php` | Test factory |
| `resources/views/contact-thank-you.blade.php` | Thank-you UI |
| `tests/Feature/ContactLeadTest.php` | Public submit + admin visibility tests |

#### Edited files

| File | Change (old → new) |
|---|---|
| `routes/web.php` | Closure view → `ContactController`; added POST store + thank-you GET |
| `app/Providers/MoonShineServiceProvider.php` | Registered `ContactLeadResource` |
| `app/MoonShine/Layouts/MoonShineLayout.php` | Sidebar **Leads → Contact enquiries** (admin only) |
| `app/MoonShine/Pages/Dashboard.php` | Admin dashboard starts with enquiry metrics + recent table |
| `resources/views/ContactUs.blade.php` | Working POST form, CSRF, old input, errors; email/message optional; 10-digit phone field |
| `resources/views/components/header.blade.php` | Contact Us stays active on the thank-you route |
| `resources/views/index.blade.php` | Hero slideshow, counting stats, values grid, local photos |
| `public/assests/css/contact.css` | Form alerts + thank-you page layout |
| `public/assests/css/theme.css` | Shared public theme, stats, motion |
| `public/assests/js/style.js` | Stat counters, hero slideshow, `data-digits-only` phone filter |

### 10.4 Database

```bash
php artisan migrate
```

Creates `contact_leads` (name, company, email nullable, phone, requirement, quantity, message nullable, status, admin_notes, ip_address).

### 10.5 How to try it

1. Open `/contact-us`, submit with a 10-digit phone (email/message can be blank).
2. You should land on `/contact-us/thank-you`.
3. Log in at `/admin` as **Admin**. Dashboard should show the new enquiry.
4. Open **Leads → Contact enquiries** to mark contacted or add notes.
5. An Author login must **not** see those leads.

---

## 11. Later updates (3 September 2026 — CMS dashboard UI, serials, Excel)

This section is **after** the contact-form change set in section 10. Test count at this date: **50** Pest tests passing.

### 11.1 What you asked for, and what was delivered

| # | Request | Result |
|---|---|---|
| 1 | Admin dashboard UI is hard to scan; lead details stack one field after another | Enquiry opens as a compact 2-column card; follow-up stays beside it. Dashboard has a greeting, shortcuts, colour-coded metrics, and side-by-side tables |
| 2 | Serial number on every table; filtering **New** starts at 1 | `S.No.` column on dashboard tables, Contact enquiries, Blogs, Authors, and Roles. Numbering is for the current result set (filter/tag resets to 1; page 2 continues 26, 27, …) |
| 3 | Excel report on Contact enquiries | **Export Excel** downloads one `.xlsx` with three sheets: **New**, **Contacted**, **Closed** |

### 11.2 How the CMS screens work now

```
Admin login (/admin)
  ├─ Dashboard greeting + shortcuts (View all leads / Write a blog)
  ├─ Contact metrics + last 10 leads (click-to-call phone, S.No.)
  ├─ Authors table | Pending posts (side by side, S.No.)
  ├─ Leads → Contact enquiries
  │    ├─ S.No. + click-to-call / WhatsApp / Email
  │    ├─ Filter or query tag (New / Contacted / Closed) → S.No. starts at 1
  │    ├─ Export Excel → one file, 3 sheets (all New, Contacted, Closed rows)
  │    └─ View enquiry: compact card (name, phone, message) + sticky Follow-up
  └─ Authors still do not see leads, export, or enquiry numbers

Author login (/admin)
  ├─ Dashboard greeting + shortcuts (My blogs / Write a post)
  └─ Recent posts table with S.No.
```

No extra Composer package. The `.xlsx` is built with PHP `ZipArchive` (SpreadsheetML). No Mailable is sent.

### 11.3 File-by-file (dashboard UI + serials + Excel)

#### New files

| File | Role |
|---|---|
| `resources/views/moonshine/contact-lead-summary.blade.php` | Compact enquiry card: status badge, Call / WhatsApp / Email, 2-column details |
| `resources/views/moonshine/dashboard-welcome.blade.php` | Greeting bar + shortcut chips |
| `app/Support/SerialNumber.php` | Shared `S.No.` Preview field; `forIndexPage()` adds page offset |
| `app/Exports/ContactLeadExcelExporter.php` | Writes a real `.xlsx` with three worksheets |
| `app/MoonShine/Handlers/ContactLeadExcelExportHandler.php` | Admin-only MoonShine handler (`excel-export`) behind **Export Excel** |

#### Edited files

| File | Change (old → new) |
|---|---|
| `app/Models/ContactLead.php` | Added `telHref()`, `whatsappHref()`, `mailtoHref()`, `phoneDigits()`, `receivedLabel()` for click-to-call and the enquiry card |
| `app/MoonShine/Resources/ContactLead/Pages/ContactLeadFormPage.php` | Stacked Preview fields → summary card (8 cols) + sticky Follow-up (4 cols); Call / WhatsApp / Email buttons |
| `app/MoonShine/Resources/ContactLead/Pages/ContactLeadIndexPage.php` | `S.No.`; clickable phone/email; Call / WhatsApp row actions; **Export Excel** handler |
| `app/MoonShine/Pages/Dashboard.php` | Welcome banner, View-all links, colour-coded metrics, authors \| pending side by side, `S.No.`, click-to-call |
| `app/MoonShine/Layouts/MoonShineLayout.php` | Palette Purple → **Green**; CSS for welcome bar, metric accents, lead card, sticky follow-up |
| `app/MoonShine/Resources/Blog/Pages/BlogIndexPage.php` | `S.No.`; colour-coded metric cards |
| `app/MoonShine/Resources/Blog/Pages/BlogFormPage.php` | Title/slug, status/type/date, SEO title/keywords sit in rows instead of a long stack |
| `app/MoonShine/Resources/MoonShineUser/Pages/MoonShineUserIndexPage.php` | `S.No.` |
| `app/MoonShine/Resources/MoonShineUserRole/Pages/MoonShineUserRoleIndexPage.php` | `S.No.` |
| `app/MoonShine/Pages/ProfilePage.php` | Name and username sit side by side |
| `tests/Feature/ContactLeadTest.php` | Compact enquiry card, S.No. / Export Excel on the list, 3-sheet workbook, admin download, author 403 |

### 11.4 Excel report columns

Each sheet has: S.No., Name, Company, Email, Phone, Requirement, Quantity, Message, Status, Admin notes, Received.

Empty statuses still get a sheet with headers only.

### 11.5 How to try it

1. Log in at `/admin` as **Admin**. Dashboard should greet you and show `S.No.` on the enquiry table.
2. Open **Leads → Contact enquiries**. Confirm `S.No.` and **Export Excel**.
3. Click **New** (or another status tag). `S.No.` should start at 1 for that list.
4. Open a lead. Details should sit in a card with Call / WhatsApp; status/notes on the right.
5. Click **Export Excel**. Open the file — three sheets: New, Contacted, Closed.
6. An Author login must **not** see leads or the export button.

---

## 12. 4 September 2026 — Blog SEO label + related sidebar

**Tests:** 54 Pest tests passing (`php artisan test`)

### 12.1 What you asked for, and what was delivered

| # | Request | Result |
|---|---|---|
| 1 | Blog backend SEO tab: rename **SEO Title** to **Meta Title** | Label only. Database column stays `meta_title`. Hint explains it becomes the browser / search-result title |
| 2 | Related products and related blogs on the **right** of the article | Two-column layout: article left, sticky sidebar right. Stacks on tablet/phone |
| 3 | Related product cards in an **auto-slide carousel**, with details and **Enquire Now** | One product per slide. Auto-rotates every 4.5s, pauses on hover, prev/next + dots. Card has image, badge, grade, description, origin, Enquire Now, and View product details |

### 12.2 How the public blog page works now

```
/blog/{slug}
  ├─ Article (left): type, title, byline, image, TOC, body, FAQ
  └─ Sidebar (right, sticky)
       ├─ Related Products
       │    ├─ From the post’s Related Products JSON when titles exist
       │    ├─ Else the 6 catalog products (same range as /products)
       │    └─ Each card → Enquire Now = /contact-us?product=Title#contact-form
       └─ Related Blogs
            ├─ Chosen Related Blogs IDs when set
            └─ Else up to 4 other published posts (thumbnail + title + Read more)
```

Contact form reads `?product=` and pre-selects a matching requirement (Premium / Roasted / Flavoured / Bulk / Private Label / Other) and fills the message with “I would like to enquire about {product}.”

### 12.3 File-by-file

#### New files

| File | Role |
|---|---|
| `app/Support/ProductCatalog.php` | Shared product cards for the sidebar: image, grade, badge, description, enquiry URL. Matches titles from the CMS JSON to the catalog when extra fields are blank |

#### Edited files

| File | Change (old → new) |
|---|---|
| `app/MoonShine/Resources/Blog/Pages/BlogFormPage.php` | SEO tab label **SEO Title** → **Meta Title**. Related Products JSON now has title, grade, badge, description, image path, URL |
| `app/Models/Blog.php` | Added `relatedProductCards()` and `sidebarRelatedBlogs()` |
| `app/Http/Controllers/BlogController.php` | Show + preview pass the sidebar collections |
| `app/Http/Controllers/ContactController.php` | Reads `product` query and passes enquiry defaults |
| `resources/views/blog/show.blade.php` | Related blocks moved from under the article into a right sidebar |
| `resources/views/ContactUs.blade.php` | Requirement select + message prefilled from `?product=` |
| `public/assests/css/blog.css` | Two-column detail layout, product carousel, related-blog list |
| `public/assests/js/style.js` | Auto-slide for `[data-product-carousel]` |
| `database/factories/BlogFactory.php` | Sample related products include grade, badge, description, image |
| `tests/Feature/BlogTest.php` | Sidebar, carousel, Meta Title CMS label |
| `tests/Feature/ContactLeadTest.php` | Contact form prefill from product enquiry |

### 12.4 How to try it

1. CMS: open a blog → **SEO Control**. The first field is **Meta Title** (not SEO Title). Saving still writes `meta_title`.
2. CMS: **Related & FAQ** → add related blogs and products (title is enough; extra fields fill from the catalog when the name matches).
3. Open `/blog/{slug}` on desktop. Products and blogs sit on the **right**. Product cards rotate by themselves.
4. Click **Enquire Now**. Contact form should open with the product in the requirement/message.
5. On a phone, the sidebar drops **below** the article.
