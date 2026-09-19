# Installation & Demo Import

## 1. Install
1. WordPress admin → **Appearance → Themes → Add New → Upload Theme**.
2. Choose `brickpoint-wordpress-elementor-pro-demo-import-final.zip` → **Install Now** → **Activate**.
3. Install **Elementor** (Plugins → Add New) and **Elementor Pro** (upload your licensed ZIP), activate both.

## 2. Import the demo
Activating the theme redirects you to **Appearance → BrickPoint Demo**. Click **Import Demo**.

Steps shown live in the progress UI:
1. Preparing & checking requirements
2. Theme options (WhatsApp, contact, social)
3. Downloading images to Media Library (batched)
4. Product, video & project categories
5. Products
6. Videos
7. Project references
8. Locations
9. Blog articles
10. Pages
11. Elementor templates (batched)
12. Menus
13. Homepage, permalinks & cleanup

On success: **View Website · Edit Homepage · Edit Header · Edit Footer · Open Elementor · Theme Settings**.

*If a step fails (e.g. server timeout), click **Retry Import** — every step is safe to repeat.*
*WP-CLI alternative:* `wp brickpoint import-demo`.

## 3. Customise
* **Appearance → Customize → BrickPoint** – phone, WhatsApp number & default message, email, address, CEO, sales manager, companies,
  social links, office map, contact recipient, floating WhatsApp, colours, radius, container width.
* **Products / Videos / Projects / Locations** admin menus – all fields are meta boxes (gallery pickers, video URL, specs one-per-line…).
* **Products → Categories** – image, banner, icon, video, WhatsApp text, order.
* **Elementor → Templates → Theme Builder** – header, footer, singles, archives, 404 (conditions pre-set).
* **Inquiries** menu – quotation form submissions (also emailed to the contact recipient).

## 4. Without Elementor Pro / without Elementor
Everything still renders identically through the theme's PHP templates. Theme Builder documents are imported and pick up automatically
once Elementor Pro is activated (re-run the import to refresh conditions if needed).

## 5. Media notes
Images are fetched from the original site assets (Pexels) into the Media Library. If outbound HTTP is blocked on your host, the importer
keeps the exact original URLs (no random replacements) and the site still displays correctly; re-run the import later to localise them.
Video files intentionally remain on their original URLs; thumbnails are stored locally.
