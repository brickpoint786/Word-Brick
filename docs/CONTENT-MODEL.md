# Content model

## Post types & meta
| Post type | Slug | Meta keys |
|---|---|---|
| `bp_product` | `/products/` | `_bp_short`, `_bp_price`, `_bp_price_label`, `_bp_unit`, `_bp_availability`, `_bp_badge`, `_bp_sku`, `_bp_gallery` (csv attachment IDs/URLs), `_bp_specs` ("Label: Value" per line), `_bp_features` (one per line), `_bp_video`, `_bp_brochure`, `_bp_cta_text`, `_bp_cta_link`, `_bp_whatsapp`, `_bp_whatsapp_url`, `_bp_related`, `_bp_featured` |
| `bp_video` | `/videos/` | `_bpv_url`, `_bpv_source`, `_bpv_duration`, `_bpv_button_text`, `_bpv_button_link`, `_bpv_captions`, `_bpv_related_products`, `_bpv_featured` |
| `bp_project` | `/projects/` | `_bpp_location`, `_bpp_status`, `_bpp_gallery`, `_bpp_video`, `_bpp_link`, `_bpp_cta_text`, `_bpp_cta_link`, `_bpp_related`, `_bpp_featured`, `_bpp_illustrative` |
| `bp_location` | `/locations/` | `_bpl_address`, `_bpl_maps`, `_bpl_phone`, `_bpl_whatsapp`, `_bpl_hours`, `_bpl_lat`, `_bpl_lng`, `_bpl_video`, `_bpl_badge` |
| `bp_inquiry` | private | quotation form submissions (`_bpi_*`) |

## Taxonomies
* `bp_product_category` (`/categories/<slug>/`) – term meta `bp_cat_image`, `bp_cat_banner`, `bp_cat_icon`, `bp_cat_video`, `bp_cat_whatsapp`, `bp_cat_order`
* `bp_video_category`, `bp_project_category`, plus core `category`/`post_tag` for the blog.

## Idempotency markers
Every imported object gets `_brickpoint_demo_id` (post meta / term meta), e.g. `product:ss7-premium-bricks`, `tpl:header`, `img:redStack`.
Media map is stored in option `brickpoint_demo_media`; template IDs in `brickpoint_tpl_*` options.

## Theme options (theme mods)
`bp_phone_display`, `bp_whatsapp_number`, `bp_default_wa`, `bp_email`, `bp_address`, `bp_ceo`, `bp_sales`, `bp_companies`, `bp_copyright`,
`bp_social_facebook|instagram|twitter|tiktok`, `bp_office_maps`, `bp_contact_to`, `bp_float_wa`, `bp_hero_video`, `bp_hero_poster`,
`bp_primary`, `bp_accent`, `bp_secondary`, `bp_whatsapp_color`, `bp_radius`, `bp_container`.

## Page ↔ section blueprints
`inc/content-defaults.php::bp_demo_pages()` lists each page as `[section_type, settings]` pairs. The same blueprint feeds
(a) the PHP fallback (`page.php`) and (b) the generated Elementor JSON (`elementor/templates/page-*.json`).
