<?php
/**
 * Product Card block registration and render callback.
 *
 * @package GeneratePress_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

/**
 * Registers the Product Card block using metadata loaded from block.json.
 */
function generatepress_child_register_product_card_block() {
register_block_type(
__DIR__ . '/../../blocks/product-card',
array(
'render_callback' => 'generatepress_child_render_product_card_block',
)
);
}
add_action( 'init', 'generatepress_child_register_product_card_block' );

/**
 * Map a small set of core icon names to SVG markup.
 *
 * @param string $name Icon identifier.
 * @return string SVG markup or empty string.
 */
function generatepress_child_product_card_core_icon( $name ) {
switch ( $name ) {
case 'star-filled':
return '<svg aria-hidden="true" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.787 1.402 8.172L12 18.896l-7.336 3.853 1.402-8.172L.132 9.21l8.2-1.192z"/></svg>';
case 'check':
return '<svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M9.5 16.172L4.828 11.5l-1.414 1.414L9.5 19 21 7.5l-1.414-1.414z"/></svg>';
case 'arrow-right':
return '<svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M13 5l7 7-7 7-1.414-1.414L16.172 13H4v-2h12.172l-4.586-4.586z"/></svg>';
case 'info':
return '<svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>';
default:
return '';
}
}

/**
 * Renders an icon based on attributes.
 *
 * @param array  $attributes Block attributes.
 * @param string $source_attr Attribute key for source.
 * @param string $name_attr Attribute key for core name.
 * @param string $svg_attr Attribute key for custom SVG.
 * @param string $class Optional CSS class.
 * @return string Sanitized HTML markup.
 */
function generatepress_child_product_card_render_icon( $attributes, $source_attr, $name_attr, $svg_attr, $class = '' ) {
$source = isset( $attributes[ $source_attr ] ) ? sanitize_text_field( $attributes[ $source_attr ] ) : '';
$name   = isset( $attributes[ $name_attr ] ) ? sanitize_text_field( $attributes[ $name_attr ] ) : '';
$svg    = isset( $attributes[ $svg_attr ] ) ? $attributes[ $svg_attr ] : '';

$icon_markup = '';

if ( 'custom' === $source && ! empty( $svg ) ) {
$allowed_svg_tags = array(
'svg'  => array(
'xmlns'       => true,
'width'       => true,
'height'      => true,
'viewBox'     => true,
'fill'        => true,
'aria-hidden' => true,
),
'path' => array(
'd'              => true,
'fill'           => true,
'fill-rule'      => true,
'clip-rule'      => true,
'stroke'         => true,
'stroke-width'   => true,
'stroke-linecap' => true,
'stroke-linejoin'=> true,
),
'g'    => array(
'fill' => true,
),
'title' => array(),
);
$icon_markup = wp_kses( $svg, $allowed_svg_tags );
} elseif ( ! empty( $name ) ) {
$icon_markup = generatepress_child_product_card_core_icon( $name );
}

if ( empty( $icon_markup ) ) {
return '';
}

$class_attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';
return '<span' . $class_attr . '>' . $icon_markup . '</span>';
}

/**
 * Render callback for the Product Card block.
 *
 * @param array $attributes Block attributes.
 * @return string HTML output.
 */
function generatepress_child_render_product_card_block( $attributes ) {
$attributes = wp_parse_args( $attributes, array(
'bannerText'              => '',
'bannerIconSource'        => 'core',
'bannerIconName'          => '',
'bannerIconSvg'           => '',
'bannerUseGradient'       => false,
'bannerColor1'            => '',
'bannerColor2'            => '',
'bannerTextColor'         => '',
'orderNumber'             => '',
'title'                   => '',
'titleHeadingLevel'       => 'h3',
'titleFontSize'           => '',
'titleColor'              => '',
'subheaderText'           => '',
'subheaderFontSize'       => '',
'subheaderColor'          => '',
'ratingScore'             => '',
'ratingTitle'             => '',
'ratingStarsCount'        => 5,
'ratingSubheader'         => '',
'bullets'                 => array(),
'bulletsIconSource'       => 'core',
'bulletsIconName'         => '',
'bulletsIconSvg'          => '',
'offerBackgroundColor'    => '',
'offerBorderColor'        => '',
'offerIconSource'         => 'core',
'offerIconName'           => '',
'offerIconSvg'            => '',
'offerText'               => '',
'ctaText'                 => '',
'ctaUrl'                  => '',
'ctaOpenInNewTab'         => false,
'ctaIconSource'           => 'core',
'ctaIconName'             => '',
'ctaIconSvg'              => '',
'ctaIconPosition'         => 'right',
'ctaBackgroundColor'      => '',
'ctaTextColor'            => '',
'ctaHoverBackgroundColor' => '',
'ctaHoverTextColor'       => '',
'ctaWidth'                => 'full',
'footerText'              => '',
'bottomLinkText'          => '',
'bottomLinkUrl'           => '',
'bottomLinkColor'         => '',
'bottomLinkIconSource'    => 'core',
'bottomLinkIconName'      => '',
'bottomLinkIconSvg'       => '',
'bottomLinkOpenInNewTab'  => false,
'cardBackgroundColor'     => '',
'cardBorderColor'         => '',
'cardBorderWidth'         => 1,
'cardBorderRadius'        => 16,
'cardBoxShadow'           => '',
'cardPadding'             => array(
'top'    => '',
'right'  => '',
'bottom' => '',
'left'   => '',
),
'cardMargin'              => array(
'top'    => '',
'right'  => '',
'bottom' => '',
'left'   => '',
),
'cardMaxWidth'            => '',
'rowSpacing'              => 16,
) );

$has_rating = ! empty( $attributes['ratingScore'] ) || ! empty( $attributes['ratingTitle'] ) || ! empty( $attributes['ratingSubheader'] ) || intval( $attributes['ratingStarsCount'] ) > 0;

$rating_star_markup = generatepress_child_product_card_core_icon( 'star-filled' );

$style_parts = array();
if ( $attributes['cardBackgroundColor'] ) {
$style_parts[] = 'background-color:' . esc_attr( $attributes['cardBackgroundColor'] );
}
if ( $attributes['cardBorderColor'] ) {
$style_parts[] = 'border-color:' . esc_attr( $attributes['cardBorderColor'] );
}
if ( $attributes['cardBorderWidth'] ) {
$style_parts[] = 'border-width:' . intval( $attributes['cardBorderWidth'] ) . 'px';
}
if ( $attributes['cardBorderRadius'] ) {
$style_parts[] = 'border-radius:' . intval( $attributes['cardBorderRadius'] ) . 'px';
}
if ( $attributes['cardBoxShadow'] ) {
$style_parts[] = 'box-shadow:' . esc_attr( $attributes['cardBoxShadow'] );
}
foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
if ( ! empty( $attributes['cardPadding'][ $side ] ) ) {
$style_parts[] = 'padding-' . $side . ':' . esc_attr( $attributes['cardPadding'][ $side ] );
}
}
foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
if ( ! empty( $attributes['cardMargin'][ $side ] ) ) {
$style_parts[] = 'margin-' . $side . ':' . esc_attr( $attributes['cardMargin'][ $side ] );
}
}
if ( $attributes['cardMaxWidth'] ) {
$style_parts[] = 'max-width:' . esc_attr( $attributes['cardMaxWidth'] );

$has_left_margin  = ! empty( $attributes['cardMargin']['left'] );
$has_right_margin = ! empty( $attributes['cardMargin']['right'] );

if ( ! $has_left_margin ) {
$style_parts[] = 'margin-left:auto';
}
if ( ! $has_right_margin ) {
$style_parts[] = 'margin-right:auto';
}
}
$wrapper_style = implode( ';', $style_parts );

$rows_style = '';
if ( $attributes['rowSpacing'] ) {
$rows_style = '--pc-row-gap:' . intval( $attributes['rowSpacing'] ) . 'px;';
}

$banner_style = '';
if ( $attributes['bannerUseGradient'] && $attributes['bannerColor1'] && $attributes['bannerColor2'] ) {
$banner_style = 'background:linear-gradient(135deg,' . esc_attr( $attributes['bannerColor1'] ) . ',' . esc_attr( $attributes['bannerColor2'] ) . ');';
} elseif ( $attributes['bannerColor1'] ) {
$banner_style = 'background:' . esc_attr( $attributes['bannerColor1'] ) . ';';
}
if ( $attributes['bannerTextColor'] ) {
$banner_style .= 'color:' . esc_attr( $attributes['bannerTextColor'] ) . ';';
}

$offer_style = '';
if ( $attributes['offerBackgroundColor'] ) {
$offer_style .= 'background:' . esc_attr( $attributes['offerBackgroundColor'] ) . ';';
}
if ( $attributes['offerBorderColor'] ) {
$offer_style .= 'border-color:' . esc_attr( $attributes['offerBorderColor'] ) . ';';
}

$cta_classes = array( 'pc-cta-button' );
if ( 'full' === $attributes['ctaWidth'] ) {
$cta_classes[] = 'is-full';
} elseif ( 'fixed-centered' === $attributes['ctaWidth'] ) {
$cta_classes[] = 'is-fixed';
}

$cta_style = '';
if ( $attributes['ctaBackgroundColor'] ) {
$cta_style .= 'background:' . esc_attr( $attributes['ctaBackgroundColor'] ) . ';';
}
if ( $attributes['ctaTextColor'] ) {
$cta_style .= 'color:' . esc_attr( $attributes['ctaTextColor'] ) . ';';
}

$cta_rel = array();
if ( $attributes['ctaOpenInNewTab'] ) {
$cta_rel[] = 'noopener';
$cta_rel[] = 'noreferrer';
}

$bottom_rel = array();
if ( $attributes['bottomLinkOpenInNewTab'] ) {
$bottom_rel[] = 'noopener';
$bottom_rel[] = 'noreferrer';
}

$heading_tag = in_array( $attributes['titleHeadingLevel'], array( 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ? $attributes['titleHeadingLevel'] : 'h3';

ob_start();
?>
<div class="wp-block-generatepress-child-product-card" style="<?php echo esc_attr( $wrapper_style ); ?>" <?php echo $rows_style ? 'data-row-style="' . esc_attr( $rows_style ) . '"' : ''; ?>>
<div class="pc-inner" style="<?php echo esc_attr( $rows_style ); ?>">
<?php if ( $attributes['bannerText'] || generatepress_child_product_card_render_icon( $attributes, 'bannerIconSource', 'bannerIconName', 'bannerIconSvg' ) ) : ?>
<div class="pc-banner" style="<?php echo esc_attr( $banner_style ); ?>">
<?php echo generatepress_child_product_card_render_icon( $attributes, 'bannerIconSource', 'bannerIconName', 'bannerIconSvg', 'pc-banner-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<span class="pc-banner-text"><?php echo esc_html( $attributes['bannerText'] ); ?></span>
</div>
<?php endif; ?>

<div class="pc-row pc-header">
<div class="pc-header-left">
<?php if ( '' !== $attributes['orderNumber'] ) : ?>
<div class="pc-order-number"><?php echo esc_html( $attributes['orderNumber'] ); ?></div>
<?php endif; ?>
<div class="pc-header-text">
<?php if ( $attributes['title'] ) : ?>
<<?php echo esc_attr( $heading_tag ); ?> class="pc-title" style="<?php echo esc_attr( $attributes['titleColor'] ? 'color:' . $attributes['titleColor'] . ';' : '' ); ?>">
<?php echo esc_html( $attributes['title'] ); ?>
</<?php echo esc_attr( $heading_tag ); ?>>
<?php endif; ?>
<?php if ( $attributes['subheaderText'] ) : ?>
<div class="pc-subheader" style="<?php echo esc_attr( $attributes['subheaderColor'] ? 'color:' . $attributes['subheaderColor'] . ';' : '' ); ?>"><?php echo wp_kses_post( $attributes['subheaderText'] ); ?></div>
<?php endif; ?>
</div>
</div>
<?php if ( $has_rating ) : ?>
<div class="pc-rating" aria-label="<?php echo esc_attr( sprintf( __( 'Rating: %s', 'generatepress-child' ), $attributes['ratingScore'] ) ); ?>">
<?php if ( $attributes['ratingScore'] ) : ?>
<div class="pc-rating-score"><?php echo esc_html( $attributes['ratingScore'] ); ?></div>
<?php endif; ?>
<?php if ( $attributes['ratingTitle'] ) : ?>
<div class="pc-rating-title"><?php echo esc_html( $attributes['ratingTitle'] ); ?></div>
<?php endif; ?>
<?php if ( $attributes['ratingStarsCount'] && $rating_star_markup ) : ?>
<div class="pc-rating-stars" aria-hidden="true">
<?php for ( $i = 0; $i < intval( $attributes['ratingStarsCount'] ); $i++ ) : ?>
<span class="pc-star-icon"><?php echo $rating_star_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
<?php endfor; ?>
</div>
<?php endif; ?>
<?php if ( $attributes['ratingSubheader'] ) : ?>
<div class="pc-rating-subheader"><?php echo esc_html( $attributes['ratingSubheader'] ); ?></div>
<?php endif; ?>
</div>
<?php endif; ?>
</div>

<?php if ( ! empty( $attributes['bullets'] ) ) : ?>
<ul class="pc-bullets">
<?php foreach ( $attributes['bullets'] as $bullet ) :
if ( empty( $bullet['text'] ) ) {
continue;
}
?>
<li>
<?php echo generatepress_child_product_card_render_icon( $attributes, 'bulletsIconSource', 'bulletsIconName', 'bulletsIconSvg', 'pc-bullet-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<span class="pc-bullet-text"><?php echo wp_kses_post( $bullet['text'] ); ?></span>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if ( $attributes['offerText'] || generatepress_child_product_card_render_icon( $attributes, 'offerIconSource', 'offerIconName', 'offerIconSvg' ) ) : ?>
<div class="pc-offer" style="<?php echo esc_attr( $offer_style ); ?>">
<?php echo generatepress_child_product_card_render_icon( $attributes, 'offerIconSource', 'offerIconName', 'offerIconSvg', 'pc-offer-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<div class="pc-offer-text"><?php echo wp_kses_post( $attributes['offerText'] ); ?></div>
</div>
<?php endif; ?>

<?php if ( $attributes['ctaText'] && $attributes['ctaUrl'] ) : ?>
<div class="pc-cta">
<a class="<?php echo esc_attr( implode( ' ', $cta_classes ) ); ?>" href="<?php echo esc_url( $attributes['ctaUrl'] ); ?>" style="<?php echo esc_attr( $cta_style ); ?>" <?php echo $attributes['ctaOpenInNewTab'] ? 'target="_blank"' : ''; ?> <?php echo ! empty( $cta_rel ) ? 'rel="' . esc_attr( implode( ' ', $cta_rel ) ) . '"' : ''; ?>>
<?php if ( 'left' === $attributes['ctaIconPosition'] ) : ?>
<?php echo generatepress_child_product_card_render_icon( $attributes, 'ctaIconSource', 'ctaIconName', 'ctaIconSvg', 'pc-cta-icon left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php endif; ?>
<span class="pc-cta-label"><?php echo esc_html( $attributes['ctaText'] ); ?></span>
<?php if ( 'right' === $attributes['ctaIconPosition'] ) : ?>
<?php echo generatepress_child_product_card_render_icon( $attributes, 'ctaIconSource', 'ctaIconName', 'ctaIconSvg', 'pc-cta-icon right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php endif; ?>
</a>
</div>
<?php endif; ?>

<?php if ( $attributes['footerText'] ) : ?>
<div class="pc-footer-text"><?php echo wp_kses_post( $attributes['footerText'] ); ?></div>
<?php endif; ?>

<?php if ( $attributes['bottomLinkText'] && $attributes['bottomLinkUrl'] ) : ?>
<div class="pc-bottom-link">
<a href="<?php echo esc_url( $attributes['bottomLinkUrl'] ); ?>" <?php echo $attributes['bottomLinkOpenInNewTab'] ? 'target="_blank"' : ''; ?> <?php echo ! empty( $bottom_rel ) ? 'rel="' . esc_attr( implode( ' ', $bottom_rel ) ) . '"' : ''; ?> style="<?php echo esc_attr( $attributes['bottomLinkColor'] ? 'color:' . $attributes['bottomLinkColor'] . ';' : '' ); ?>">
<span class="pc-bottom-link-text"><?php echo esc_html( $attributes['bottomLinkText'] ); ?></span>
<?php echo generatepress_child_product_card_render_icon( $attributes, 'bottomLinkIconSource', 'bottomLinkIconName', 'bottomLinkIconSvg', 'pc-bottom-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</a>
</div>
<?php endif; ?>
</div>
</div>
<?php
return ob_get_clean();
}
