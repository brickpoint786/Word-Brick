# BrickPoint — WordPress + Elementor Pro Theme (with One-Click Demo Import)

Production-ready WordPress theme for **BrickPoint** (premium bricks & construction materials, Lahore).
A faithful 1:1 port of the original BrickPoint (LM Arena / Next.js) design — same colours, typography (Archivo + Inter),
spacing, card shapes, animations and responsive behaviour — rebuilt as a native WordPress + Elementor Pro system.

**Download:** [`brickpoint-wordpress-elementor-pro-demo-import-final.zip`](./brickpoint-wordpress-elementor-pro-demo-import-final.zip) (installable theme, `brickpoint/` root)
**Source:** [`brickpoint/`](./brickpoint)

## Quick start
1. **Appearance → Themes → Add New → Upload Theme** → upload the ZIP → **Activate**.
2. Install & activate **Elementor** and **Elementor Pro** (Pro enables Theme Builder header/footer/archives; without Pro, the theme's identical PHP templates render).
3. You are redirected to **Appearance → BrickPoint Demo** → click **Import Demo**.
4. Watch the 13-step progress UI; the success screen offers **View Website · Edit Homepage · Edit Header · Edit Footer · Open Elementor · Theme Settings**.
5. Change phone / WhatsApp / email / address / CEO / Sales / social links under **Appearance → Customize → BrickPoint** (all templates read them dynamically).

The importer is **idempotent** — run it again any time; existing demo items are updated (matched by `_brickpoint_demo_id` / slug), never duplicated. Pages you edited in Elementor are not overwritten.

## What the demo imports
| Content | Count | Notes |
|---|---|---|
| Product categories | 15 | slug, description, icon, image, banner, WhatsApp text, order (term meta) |
| Products (`bp_product`) | 12 | SKU, price, unit, price label, availability, badge, specs, features, gallery, product video, featured, WhatsApp order link |
| Video categories / Videos (`bp_video`) | 11 / 6 | remote MP4/YouTube URL, local thumbnail, duration, featured |
| Project categories / Projects (`bp_project`) | 3 / 6 | location, status (“Illustrative construction reference”), gallery, featured |
| Locations (`bp_location`) | 4 | address, Google Maps link, phone, hours, badge (Unit 1–4), lat/lng |
| Blog posts | 4 | categories + tags, featured images |
| Pages | 16 routes | Home, About, Products, Categories, SS7 Bricks, Construction Materials, For Contractors, For Builders, For Construction Companies, Projects, Videos, Locations, Blog, Contact, Privacy Policy, Terms & Conditions |
| Media | 20 images | downloaded into the Media Library (attachment IDs); video files stay on their original URLs (thumbnails local). If a host blocks outbound downloads, the exact original source URL is kept — never a random replacement. |
| Menus | 3 | Primary (with Products dropdown), Footer, Mobile — auto-assigned |
| Elementor documents | 34 | Header, Footer, Single Product/Video/Project/Location/Post, Product/Video/Project/Location/Blog archives, 404 (all with display conditions), 11 Elementor pages, 10 library templates (cards, CTA band/box, SS7 feature, WhatsApp button, quotation form) |
| Settings | — | Homepage=Home, Posts page=Blog, permalinks `/%postname%/`, rewrite flush, theme options, Elementor kit colours/fonts/container width |

## Architecture
```
brickpoint/
├── style.css, functions.php, screenshot.png, readme.txt
├── inc/
│   ├── helpers.php, setup.php, enqueue.php, customizer.php, icons.php
│   ├── post-types.php, taxonomies.php, meta-fields.php   # CPTs, term meta, meta boxes
│   ├── whatsapp.php, contact-form.php                    # WhatsApp URLs, AJAX quotation form (bp_inquiry)
│   ├── content-defaults.php   # ALL real site content + page section blueprints (single source of truth)
│   ├── sections.php           # bp_section_* renderers (used by PHP templates AND Elementor widgets)
│   ├── template-functions.php # breadcrumbs, pagination, filter pills, fallback menu…
│   ├── elementor.php, elementor-widgets.php, elementor-dynamic-tags.php
│   ├── admin.php              # Appearance → BrickPoint Demo UI
│   └── demo/class-demo-importer.php, class-elementor-templates.php
├── elementor/
│   ├── widgets/schema.php (controls for 21 section widgets + 18 template widgets), class-section-widget.php, class-template-widget.php
│   ├── dynamic-tags/ (BrickPoint Field / URL / Gallery)
│   └── templates/*.json       # 34 Elementor JSON documents imported by the demo
├── template-parts/ (header, footer, cards, single bodies, archive loops)
├── assets/css (main, responsive, animations, editor, admin, icons), assets/js, assets/icons (51 SVG + JSON library)
├── languages/brickpoint.pot
└── root templates: front-page, page, index/home/search, archive(-cpt), taxonomy-*, single(-cpt), 404, comments, header, footer
```
Design rule: every section exists once as a PHP renderer in `inc/sections.php`; the matching Elementor widget just maps its controls
onto the same settings — so Elementor output and PHP fallback output are pixel-identical, and pages stay fully editable in Elementor.

## Elementor
* **Widget category “BrickPoint”**: Hero, Image + Content, Category Grid, SS7 Feature, Product Grid, Video Showcase, Video/Project/Location/Blog Grids,
  Audience Cards, Team, CTA Band, CTA Box, Page Header, Icon Cards, Notice, Video + Content, Material Groups, Contact Section, Prose Page,
  plus Site Header, Site Footer, Product/Video/Project/Location/Post Details, Archive Grid, Blog Archive, Product/Video/Project/Location/Blog Card,
  WhatsApp Button, Social Links, Quotation Form, Breadcrumbs.
* **Dynamic tags** (group “BrickPoint”): product price/unit/SKU/badge/availability/specs/features/WhatsApp URL/video/brochure/gallery, video URL/duration,
  project location/status/gallery, location address/phone/hours/maps, site phone/email/address/WhatsApp/CEO/Sales/socials/map.
* **Icon library** “BrickPoint Icons” (51 editable SVG icons, `assets/icons/svg`).
* Links in widgets accept shortcuts: `page:contact`, `archive:bp_product`, `home`, `whatsapp`, `wa:Message…`, `tel`.

## Requirements
WordPress 6.2+, PHP 8.0+ (tested on 8.2 / 8.5), Elementor 3.2x+, Elementor Pro (optional, recommended). No WooCommerce.

See [`docs/`](./docs) for the installation guide, content model and QC report.
