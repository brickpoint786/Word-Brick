# Internal QC report (2026-09-18)

Environment: WordPress 6.8.2 (SQLite, WP Playground), PHP 8.2, Elementor 3.33.2 (free). Elementor Pro not available in the sandbox — Theme Builder
documents were verified for type/conditions meta and via the theme's Free-compatible fallback (option-based header/footer rendering).

| Check | Result |
|---|---|
| `php -l` on all 71 PHP files (PHP 8.5) | pass |
| JSON validity: 34 Elementor templates + icon library | pass |
| No lorem ipsum / placeholder strings | pass |
| Theme activation, redirect to demo page | pass |
| Full import run (13 steps) | pass — 12 products, 6 videos, 6 projects, 4 locations, 4 posts, 12 pages, 29 terms, 3 menus, 24 library docs + 11 Elementor pages |
| Second import run (idempotency) | counts unchanged, no duplicates |
| Front-end routes (home, archives, singles, taxonomies, all static pages, search, 404) | all 200 / 404 as expected, no PHP notices from the theme in debug.log |
| Header & footer rendered by Elementor documents | pass (`bp_site_header` / `bp_site_footer` widgets in output) |
| Elementor editor opens homepage & header; 41 BrickPoint widgets, 3 dynamic tags, icon library registered | pass |
| Quotation form AJAX → `bp_inquiry` created | pass |
| Permalinks `/%postname%/`, homepage/blog page assigned | pass |
| Media download | blocked in sandbox (no outbound HTTP) → importer kept exact original source URLs; on a normal host images are stored in the Media Library |
