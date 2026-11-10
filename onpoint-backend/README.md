# OnPoint shared backend (PHP + MySQL, cPanel)

This folder is the **source of truth** for the blog/admin/API code. It is copied into each site, and every
site runs it independently with its own database, `.env`, admin accounts and uploads.

```
onpoint-backend/        source (edit here)
  src/ admin/ api/ schema.sql install.php sync-backend.sh
onpoint-cleaning/       PHP site  → onpointhygienezw.co.zw
onpoint-security/       PHP site  → shieldtrac.co.zw
onpoint-engineering/    PHP site  → onpointcivils.co.zw  (adds Projects)
```

**Never edit `admin/`, `api/`, `src/` inside a site.** Edit here, then run `./sync-backend.sh` (all sites)
or `./sync-backend.sh cleaning`. Site-owned files are never overwritten: `index.php`, `includes/layout.php`, `assets/`, `.htaccess`
and **`blog/`** — each site has its own blog design:

| Site | Blog name | Layout |
|---|---|---|
| engineering | Project Updates | same card grid + details sidebar as Projects |
| cleaning | Hygiene Tips | magazine: featured tip, thumbnail rows, booking sidebar |
| security | Security Insights | bulletin: dark masthead, featured advisory, tagged cards, CTA band |

Blog names/intro copy live in `blog_meta()` in each site's `includes/layout.php`.

## Deploy one site (cPanel, no SSH needed)
1. cPanel → **MySQL Databases**: create a database and user, add the user with all privileges.
2. **phpMyAdmin** → select that database → **Import** → the site's `schema.sql`.
3. Upload the whole site folder's contents to `public_html` (File Manager / FTP). Use PHP 8.1+ (cPanel → *Select PHP Version*; `pdo_mysql`, `fileinfo`, `dom` on).
4. Copy `.env.example` → `.env`, fill in the DB details and a long random `INSTALL_KEY`.
5. Open `https://yourdomain/install.php`, create the first admin, then **delete `install.php`**.
6. Sign in at `https://yourdomain/admin/`, fill in *Settings*, write a post.
7. Make sure `uploads/` is writable (755).

## What each site serves
| URL | |
|---|---|
| `/blog/`, `/blog/{slug}` | public blog (server-rendered PHP) |
| `/projects/`, `/projects/{slug}` | engineering only (`FEATURE_PROJECTS=true`) |
| `/sitemap.xml` | generated from the database |
| `/api/posts`, `/api/posts/{slug}`, `/api/site`, `/api/contact` (+ `/api/projects` on engineering) | JSON API |
| `/admin/` | admin panel |

The old React versions of cleaning and security are kept in `../_legacy-react/` for reference.

## SEO & AI-engine optimisation (built in)

| What | Where |
|---|---|
| One consistent schema.org graph on every page: Organization (with address, phones, services catalog), WebSite, WebPage, Blog, **BlogPosting**, BreadcrumbList, **FAQPage** | `src/seo.php`, facts in each site's `includes/profile.php` |
| Title/description, canonical, hreflang (en-ZW), robots (`max-snippet`, `max-image-preview:large`), Open Graph + `article:*` times, Twitter cards, RSS link | `seo_head()` |
| `/sitemap.xml` (lastmod + cover images), `/feed.xml` (RSS, full text), **`/llms.txt`** and **`/llms-full.txt`** for AI assistants, `robots.txt` with explicit AI-crawler rules | `sitemap.php`, `feed.php`, `llms.php`, `robots.txt` |
| **IndexNow** ping when a post is published (Bing → ChatGPT search, Copilot, DuckDuckGo) | set `INDEXNOW_KEY` in `.env`; verified at `/indexnow.txt` |
| Per-article **Key takeaways**, **FAQs**, visible **Updated** date, breadcrumbs, related-article links | post editor → "Answer-ready content" |
| **301 redirects** automatically created when a post/project slug changes | `redirects` table |
| Live **SEO checklist** in the post editor (title/description length, 300+ words, subheadings, internal link, cover + alt text, takeaways, FAQs) | post editor sidebar |
| HTTPS + one canonical host redirect, gzip, long-cache static files | each site's `.htaccess` |

### After deploying a site
1. Existing database from an older `schema.sql`? Run `migrations/001_seo.sql` once in phpMyAdmin. (Fresh imports already include it.)
2. `.env`: set `INDEXNOW_KEY` (any 32 random letters/digits).
3. Admin → Settings → **Social & directory profile links**: paste the Facebook / LinkedIn / Google Business Profile URLs (this is what lets Google and AI tools treat the site and profiles as one business).
4. Add the site in **Google Search Console** and **Bing Webmaster Tools**, and submit `https://yourdomain/sitemap.xml`.
5. Claim/complete the **Google Business Profile** and keep name, address and phone identical to the site.
6. Write articles that answer real customer questions; fill in Key takeaways and 2+ FAQs on each.
