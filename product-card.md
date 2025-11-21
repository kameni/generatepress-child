1. Block Overview

Name (internal): generatepress-child/product-card
Label (editor): Product Card
Location: Implemented as a theme block inside the generatepress-child theme.

Primary goal:
A fixed-layout “product card” block that visually matches the provided design. All content and visual options are editable via block settings. Any element with no value provided must be hidden in the front-end and editor preview.

Layout summary (rows):

Row 1: Top-left banner

Row 2: Two-column header (order + title/subheader on left, rating box on right)

Row 3: Bullet list

Row 4: Offer pill row

Row 5: CTA button row

Row 6: Small explanatory text

Row 7: Bottom link + optional icon

Card-level appearance (radius, border, padding, spacing) is configurable.

2. File & Folder Structure (within child theme)

Inside generatepress-child:

blocks/
  product-card/
    block.json          # Block registration metadata
    edit.js             # Editor UI logic
    save.js (optional)  # Only if we don’t use dynamic render; see section 7
    style.css           # Front-end styles
    editor.css          # Editor-only styles
    icons.js            # Optional helper for icon registry / picker


PHP registration (no code here, just concept):

Add a small loader in the child theme (e.g. inc/blocks/product-card.php) and register the block with register_block_type, pointing to block.json.

Use a PHP render callback (dynamic block) to ensure consistent markup and conditional visibility logic.

3. Data Model (Block Attributes)

All attributes will be defined in block.json and used both in editor JS and PHP renderer.

3.1. Row 1 – Top-left Banner

Visual: small banner badge overlapping / touching top-left border of card.

Attributes:

Attribute	Type	Description	Default
bannerText	string	Text inside banner (e.g., “Best Overall Tirzepatide Value”). If empty, banner hidden.	''
bannerIconSource	string	'core' (built-in WP icon) or 'custom'.	'core'
bannerIconName	string	Identifier for core icon (e.g., 'star-filled' from @wordpress/icons). Only used if bannerIconSource === 'core'.	''
bannerIconSvg	string	Raw SVG string for custom icon. Only used if bannerIconSource === 'custom'.	''
bannerUseGradient	boolean	If true and both colors set, use a gradient background.	false
bannerColor1	string	Primary color (solid or gradient start).	theme default
bannerColor2	string	Secondary color (gradient end). If empty or bannerUseGradient === false, use solid color1 only.	''
bannerTextColor	string	Banner text color.	theme default

Gradient behavior:

If bannerUseGradient === true and both bannerColor1 and bannerColor2 are set, apply linear-gradient() using a fixed angle that visually matches screenshot (e.g., 135deg).

If bannerUseGradient === false or bannerColor2 is empty, use bannerColor1 as solid background.

If bannerText is empty and no icon is set, hide the entire banner (no visual placeholder).

3.2. Row 2 – Header (Left cell: order + title; right cell: rating box)

Layout: flex row with space-between; responsiveness handled via CSS (see Section 6).

3.2.1. Left cell attributes
Attribute	Type	Description	Default
orderNumber	string or number	Displayed large on left (e.g. “1”). If empty, hide order number but keep title/subheader.	''
title	string	Main product title (e.g. “MEDVI”). Required for block to make sense; if empty, hide title element only.	''
titleHeadingLevel	string	Heading tag to use: 'h2', 'h3', 'h4', etc.	'h3'
titleFontSize	string	CSS value (e.g., '24px', '1.6rem'); used via inline style or CSS var.	theme default
titleColor	string	Title color.	theme default
subheaderText	string	Small descriptive text under title.	''
subheaderFontSize	string	Font size for subheader.	theme default
subheaderColor	string	Color for subheader text.	theme default

Order number styling:

Large numeric value; uses a dedicated CSS class (e.g., .pc-order-number).

If orderNumber empty, .pc-order-number is not rendered (no placeholder).

3.2.2. Right cell – Rating box attributes
Attribute	Type	Description	Default
ratingScore	number or string	Numeric score (e.g., 9.7).	''
ratingTitle	string	Label text (e.g., “EXCELLENT”).	''
ratingStarsCount	number	Number of star icons (e.g., 5).	5
ratingSubheader	string	Small text below stars (e.g., “Price, access & support score”).	''

Appearance constraints (as requested):

Score box uses fixed style (colors, background, border) defined in CSS, not user-editable for now.

Only textual and numeric values are editable.

If all rating-related attributes are empty (ratingScore, ratingTitle, ratingStarsCount, ratingSubheader), hide the whole rating box container.

3.3. Row 3 – Bullet list

A vertical list of bullet items; contents have simple HTML formatting support.

Attributes:

Attribute	Type	Description	Default
bullets	array	Repeater of bullet items.	[]
bullets[n].id	string	Internal unique ID per bullet (for React list keys).	auto
bullets[n].text	string	Bullet text with basic inline formatting (strong, em, etc).	''
bulletsIconSource	string	'core' or 'custom' – shared for all bullets.	'core'
bulletsIconName	string	Core icon reference (e.g., 'check').	''
bulletsIconSvg	string	Custom SVG markup reused for all bullets.	''

Behavior:

User can add/remove bullets in a Repeater UI.

Single icon reused for all bullets: if icon is not defined (no name and no custom SVG), bullets render with standard list marker or no icon depending on CSS design.

Bullet text is stored as HTML string; render via RichText and sanitized with wp_kses_post() in PHP.

If bullets array is empty, row 3 is not rendered.

3.4. Row 4 – Offer pill row

Visual: a rounded pill spanning card width with icon + text.

Attributes:

Attribute	Type	Description	Default
offerBackgroundColor	string	Background color of the pill.	theme default
offerBorderColor	string	Border color around pill.	theme default
offerIconSource	string	'core' or 'custom'.	'core'
offerIconName	string	Core icon identifier.	''
offerIconSvg	string	Custom SVG markup.	''
offerText	string	Text with inline formatting (e.g., price in <strong>).	''

Shape:

Always a rounded pill: high border-radius (e.g., 999px) defined by CSS.

If offerText is empty and no icon is selected, hide the entire row.

3.5. Row 5 – CTA button

Centered or full-width primary call-to-action.

Attributes:

Attribute	Type	Description	Default
ctaText	string	Button label text.	''
ctaUrl	string	Link URL.	''
ctaOpenInNewTab	boolean	Whether to set target="_blank" and rel="noopener noreferrer".	false
ctaIconSource	string	'core' or 'custom'.	'core'
ctaIconName	string	Core icon identifier.	''
ctaIconSvg	string	Custom SVG markup.	''
ctaIconPosition	string	'left' or 'right'.	'right'
ctaBackgroundColor	string	Button background color (default matches screenshot).	theme default
ctaTextColor	string	Text color.	theme default
ctaHoverBackgroundColor	string	Background on hover.	derived
ctaHoverTextColor	string	Text color on hover.	derived
ctaWidth	string	'auto', 'full', 'fixed-centered'.	'full'

Behavior:

If ctaText is empty or ctaUrl is empty, hide the button row entirely.

If icon not set, render text-only.

Width behavior:

auto: button width fits content.

full: button spans available width.

fixed-centered: fixed max-width (e.g., 260–300px) and horizontally centered.

3.6. Row 6 – Small explanatory text

Attributes:

Attribute	Type	Description	Default
footerText	string	Plain or lightly formatted text under CTA (e.g., “Quick online intake…“).	''

Uses RichText with allowed formats (bold/italic/link) if needed.

If empty, row is hidden.

3.7. Row 7 – Bottom link + optional icon

Example: “Read our full MEDVI review →”

Attributes:

Attribute	Type	Description	Default
bottomLinkText	string	Text displayed.	''
bottomLinkUrl	string	URL.	''
bottomLinkColor	string	Link color.	theme default
bottomLinkIconSource	string	'core' or 'custom'.	'core'
bottomLinkIconName	string	Core icon identifier (e.g., arrow).	''
bottomLinkIconSvg	string	Custom SVG markup.	''
bottomLinkOpenInNewTab	boolean	Optional new-tab behavior.	false

Behavior:

If bottomLinkText or bottomLinkUrl is empty, row is hidden.

Icon, if present, is rendered after the text (matching screenshot).

If no icon is set, text is rendered alone with standard spacing.

3.8. Card-level styling

These apply to the outer container of the card.

Attribute	Type	Description	Default
cardBackgroundColor	string	Overall card background.	theme default (white)
cardBorderColor	string	Card border color.	subtle grey
cardBorderWidth	number	Border width (px).	1
cardBorderRadius	number	Corner radius (px).	16 or value matching screenshot
cardBoxShadow	string	Optional box-shadow preset or custom value.	theme default
cardPadding	object	Per-side padding (top, right, bottom, left).	sensible defaults
cardMargin	object	Per-side margin.	null / theme default
rowSpacing	number	Vertical spacing between rows (px).	16
4. Editor UI / Controls

The block will include Inspector Controls organized into logical panels.

4.1. “Layout & Card” panel

Card background color control (ColorPalette).

Border radius (range slider).

Border width & color.

Optional box shadow presets (dropdown or toggle).

Card padding (input or spacing control).

Row spacing slider.

4.2. “Banner” panel

Toggle / “Show banner” is implicit by content (no separate boolean).

Text input (RichText or TextControl).

Icon source selector: “WordPress icon” / “Custom SVG”.

If “WordPress icon”: icon picker using @wordpress/icons.

If “Custom SVG”: textarea or media upload for SVG (validate MIME type image/svg+xml).

Background:

Switch: “Solid” vs “Gradient”.

Color control for bannerColor1.

If gradient: show bannerColor2.

Text color picker.

4.3. “Header” panel
Left cell subpanel

Order number (NumberControl or text).

Title (RichText).

Heading level dropdown (h2–h6).

Title font size (range or select).

Title color.

Subheader text (RichText).

Subheader font size & color.

Rating box subpanel

Score field (NumberControl or TextControl).

Title label field.

Star count (RangeControl 0–5).

Subheader text field.

Info notice: “Colors of rating box are fixed by design.”

4.4. “Bullets” panel

Repeater control with Add bullet / Remove buttons.

Each bullet: RichText allowing bold, italic, underline, possibly link.

Icon configuration:

Icon source (“WordPress icon” / “Custom SVG”).

Icon picker or SVG field.

Optionally toggle: “Use custom icon instead of default check mark”.

4.5. “Offer Row” panel

Background color picker.

Border color picker.

Icon source selector + input fields (as above).

Offer text (RichText) with inline formatting.

4.6. “CTA Button” panel

Button text.

URL input (__experimentalLinkControl or URLInput).

"Open in new tab" toggle.

Icon source and icon selection.

Icon position: radio (left / right).

Background color.

Text color.

Hover background & text colors.

Width: select (auto, full, fixed-centered).

4.7. “Footer Text” panel (Row 6)

RichText field for explanatory text; simple inline formatting allowed.

4.8. “Bottom Link” panel (Row 7)

Link text.

Link URL.

“Open in new tab” toggle.

Link color picker.

Icon source + icon selector.

Icon always rendered after text if set.

5. Conditional rendering rules (critical)

For both editor preview and front-end:

If all content for a row is effectively empty, that row is not rendered (no empty wrappers).

Emptiness rules by row:

Row 1: Hide if bannerText empty AND no icon.

Row 2:

If title empty AND orderNumber empty AND all rating values empty → hide the entire header row (edge case).

Otherwise, hide only individual pieces that are empty.

Row 3: Hide if bullets array length = 0.

Row 4: Hide if offerText empty AND no icon.

Row 5: Hide if ctaText empty OR ctaUrl empty.

Row 6: Hide if footerText empty.

Row 7: Hide if bottomLinkText empty OR bottomLinkUrl empty.

6. Layout & Responsive Behavior
Desktop (approx ≥ 768px)

Card is a flexbox / block container with:

Banner absolutely positioned at the top-left edge relative to card container (or simply attached to top border via margin).

Row 2 uses a flex row:

Left column: order number + title + subheader.

Order and title are horizontally aligned; order number on left, title/subheader on right.

Right column: rating box fixed width.

Other rows are full-width stacked.

Mobile (narrow viewports)

Requirements you gave:

Banner always stays at top-left.

Order number + title + subheader: stack vertically but still in the left side.

Rating block must not move below (i.e., don’t drop into a new row in normal mobile width).

Rating block should just get smaller.

Implementation (spec):

Row 2 remains a single flex row with justify-content: space-between; align-items: flex-start;.

Use responsive CSS:

Reduce rating box width, font size and padding for smaller screens.

Allow text to wrap inside rating box.

Maintain flex row; avoid flex-wrap causing rating box to drop below unless viewport is extremely narrow (we can treat this as acceptable overflow edge case).

Title block can wrap onto multiple lines; order number may shrink slightly or keep size; use min-width on rating box but keep it smaller than desktop.

7. Render Strategy
Dynamic block via PHP (recommended)

Use apiVersion: 2 and set render in block.json to a PHP callback.

PHP function:

Receives $attributes.

Implements all conditional row visibility logic.

Outputs final HTML with appropriate CSS classes.

Escapes all values correctly (see next section).

This keeps front-end output stable even if editor JS changes and ensures server-side control over icon sanitization and SVG handling.

8. Security & Sanitization Requirements

On save (JS):

RichText fields should allow only basic formatting.

Don’t manually strip anything – rely on allowed formats and later PHP sanitization.

On render (PHP):

Text fields (title, subheader, banners without HTML): use esc_html().

URLs: esc_url().

Attributes like colors, widths: sanitize via sanitize_hex_color(), sanitize_text_field() or custom sanitization before placing into inline styles.

RichText fields (bullets, offerText, footerText, etc) that allow inline HTML: pass through wp_kses_post().

SVG icons:

If stored as strings, they must be sanitized/whitelisted carefully (e.g., restrict to expected tags like <svg>, <path>, <g>).

Alternatively, restrict upload to trusted admins and use wp_kses() with custom allowed tags and attributes.

Always escape attributes inside HTML attributes (class, data-*, style) with esc_attr().

9. Icons Handling (Technical Detail)

We support two icon sources per icon location:

Core icon from @wordpress/icons:

In editor: use <Icon icon={ someIcon } />.

Attribute stores a string key, e.g., 'star-filled'.

In PHP: output an <span> with CSS pseudo-element OR inline SVG stored in a map (depending on how we decide to ship icons).

Custom SVG:

Provide a control that accepts an SVG upload or raw SVG string.

In attributes, store sanitized SVG markup.

On render, echo sanitized SVG without additional wrapping where possible.

If neither core icon nor custom SVG is selected, the icon is simply not rendered at that location.

10. Block Supports & Integration with theme.json

supports in block.json (conceptual):

spacing → enable padding/margin controls only where needed (card-level mostly).

html → false to prevent users from editing as raw HTML.

align → maybe prevent align options; or allow full width if design supports it.

color → likely disabled at block root because we manage colors via custom controls.

Theme integration:

Card fonts, base colors, and spacing should leverage theme.json tokens where reasonable:

Example: default card background var(--wp--preset--color--base) etc.

Allow overrides via block attributes that set inline styles or CSS vars.

11. Accessibility Considerations

Rating box:

Provide aria-label describing score and meaning (e.g., “Rating: 9.7 out of 10 – Excellent”).

Ensure stars are decorative (aria-hidden="true") or combined into the label.

Links & buttons:

Ensure CTA button and bottom link have accessible names from text content.

If opening in new tab, optionally include screen-reader-only text like “(opens in a new tab)”.

Color contrast:

Default color choices should meet contrast guidelines; allow user to adjust but maintain sane defaults.

12. Performance & Maintainability

Single CSS file (style.css) scoped with a .wp-block-generatepress-child-product-card root class.

Editor-specific adjustments in editor.css only (e.g., outlines, placeholders).

Avoid heavy JS; all logic should be minimal React components using WordPress data and components.

For icons, consider importing only the few needed from @wordpress/icons, not the entire set.