# Product Card Block – Code Review (last commit)

## Blocking issues

1. **Editor script fails to run.** The `edit.js` file repeatedly calls `sel(...)`, but only `el` is defined. Because `sel` is undefined, the script throws a `ReferenceError` before the block can even register, making the block unusable in the editor.
2. **Rating stars pull the wrong icon source.** The PHP renderer uses the bullets icon settings when outputting rating stars, so changing bullet icons also changes the rating visuals. The spec fixes rating colors/styles and does not tie them to the bullet icon configuration.
3. **Rating row hides incorrectly when stars are the only value.** `has_rating` ignores `ratingStarsCount`, so with the default star count of 5 and no other rating text, the rating row is hidden. The doc states the rating box should hide only when *all* rating values (score, title, stars, subheader) are empty.

## Missing functionality vs. product-card.md

- **Inspector controls are incomplete.** The spec calls for heading-level selection, title/subheader font sizes and colors, card padding/margin controls, box-shadow presets, and icon source pickers per panel. The current Inspector UI exposes only a subset (e.g., there is no heading level control despite the attribute existing, no font-size controls, no hover colors for CTA, no box-shadow control, no padding/margin inputs, and the icon panels only accept free-text inputs without source toggles per spec).
- **Conditional rendering and hiding rules are only partially applied.** The editor preview shows rows even when required paired values are absent (e.g., CTA preview renders when text is present regardless of URL), and the front-end uses empty wrappers (e.g., bullets `<ul>` renders when the array is non-empty but all texts are empty). The spec requires hiding entire rows when their meaningful content is missing.
- **Accessibility and aria labeling for rating stars are minimal.** The aria-label only reports the score string and does not include context such as “out of 10 – Excellent” as requested in the spec.

## Security/sanitization observations

- **SVG sanitization is limited.** Only `<svg>`, `<path>`, `<g>`, and `<title>` tags are allowed; attributes like `viewBox` are whitelisted but there is no handling for `polygon`, `circle`, etc., which may be needed for custom icons from the spec. Consider expanding or validating more robustly.
- **Style attributes accept raw values.** Inputs like border color and card box shadow are inserted directly into inline styles after `esc_attr()`, but no validation ensures they are legitimate colors/lengths. The spec recommends `sanitize_hex_color()` or similar.
