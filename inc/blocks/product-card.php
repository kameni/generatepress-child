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
 * Sanitize a CSS color value, permitting hex or CSS variables.
 *
 * @param string $value Raw color value.
 * @return string Sanitized color or empty string.
 */
function generatepress_child_product_card_sanitize_color( $value ) {
    $trimmed = trim( (string) $value );

    if ( '' === $trimmed ) {
        return '';
    }

    $hex = sanitize_hex_color( $trimmed );
    if ( $hex ) {
        return $hex;
    }

    if ( preg_match( '/^var\(--[a-zA-Z0-9_-]+\)$/', $trimmed ) ) {
        return $trimmed;
    }

    return '';
}

/**
 * Sanitize a CSS dimension value (px, em, rem, %).
 *
 * @param string $value Raw dimension value.
 * @return string Sanitized dimension or empty string.
 */
function generatepress_child_product_card_sanitize_dimension( $value ) {
    $trimmed = trim( (string) $value );

    if ( '' === $trimmed ) {
        return '';
    }

    return preg_match( '/^-?\d*\.?\d+(px|em|rem|%)?$/', $trimmed ) ? $trimmed : '';
}

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
    }

    return '';
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
                'd'               => true,
                'fill'            => true,
                'fill-rule'       => true,
                'clip-rule'       => true,
                'stroke'          => true,
                'stroke-width'    => true,
                'stroke-linecap'  => true,
                'stroke-linejoin' => true,
            ),
            'g'    => array(
                'fill' => true,
            ),
            'title' => array(),
        );
        $icon_markup      = wp_kses( $svg, $allowed_svg_tags );
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
    $attributes = wp_parse_args(
        $attributes,
        array(
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
        )
    );

    $has_rating = ! empty( $attributes['ratingScore'] ) || ! empty( $attributes['ratingTitle'] ) || ! empty( $attributes['ratingSubheader'] ) || intval( $attributes['ratingStarsCount'] ) > 0;

    $rating_star_markup = generatepress_child_product_card_core_icon( 'star-filled' );

    $style_parts = array();

    $card_background    = generatepress_child_product_card_sanitize_color( $attributes['cardBackgroundColor'] );
    $card_border_color  = generatepress_child_product_card_sanitize_color( $attributes['cardBorderColor'] );
    $card_box_shadow    = sanitize_text_field( $attributes['cardBoxShadow'] );
    $card_max_width     = generatepress_child_product_card_sanitize_dimension( $attributes['cardMaxWidth'] );

    if ( $card_background ) {
        $style_parts[] = 'background-color:' . $card_background;
    }

    if ( $card_border_color ) {
        $style_parts[] = 'border-color:' . $card_border_color;
    }

    if ( $attributes['cardBorderWidth'] ) {
        $style_parts[] = 'border-width:' . intval( $attributes['cardBorderWidth'] ) . 'px';
    }

    if ( $attributes['cardBorderRadius'] ) {
        $style_parts[] = 'border-radius:' . intval( $attributes['cardBorderRadius'] ) . 'px';
    }

    if ( $card_box_shadow ) {
        $style_parts[] = 'box-shadow:' . $card_box_shadow;
    }

    foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
        $padding_value = generatepress_child_product_card_sanitize_dimension( $attributes['cardPadding'][ $side ] ?? '' );

        if ( $padding_value ) {
            $style_parts[] = 'padding-' . $side . ':' . $padding_value;
        }
    }

    foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
        $margin_value = generatepress_child_product_card_sanitize_dimension( $attributes['cardMargin'][ $side ] ?? '' );

        if ( $margin_value ) {
            $style_parts[] = 'margin-' . $side . ':' . $margin_value;
        }
    }

    if ( $card_max_width ) {
        $style_parts[] = 'max-width:' . $card_max_width;

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

    $banner_style       = '';
    $banner_color_one   = generatepress_child_product_card_sanitize_color( $attributes['bannerColor1'] );
    $banner_color_two   = generatepress_child_product_card_sanitize_color( $attributes['bannerColor2'] );
    $banner_text_color  = generatepress_child_product_card_sanitize_color( $attributes['bannerTextColor'] );

    if ( $attributes['bannerUseGradient'] && $banner_color_one && $banner_color_two ) {
        $banner_style = 'background:linear-gradient(135deg,' . $banner_color_one . ',' . $banner_color_two . ');';
    } elseif ( $banner_color_one ) {
        $banner_style = 'background:' . $banner_color_one . ';';
    }

    if ( $banner_text_color ) {
        $banner_style .= 'color:' . $banner_text_color . ';';
    }

    $offer_style            = '';
    $offer_background_color = generatepress_child_product_card_sanitize_color( $attributes['offerBackgroundColor'] );
    $offer_border_color     = generatepress_child_product_card_sanitize_color( $attributes['offerBorderColor'] );

    if ( $offer_background_color ) {
        $offer_style .= 'background:' . $offer_background_color . ';';
    }

    if ( $offer_border_color ) {
        $offer_style .= 'border-color:' . $offer_border_color . ';';
    }

    $cta_classes = array( 'pc-cta-button' );

    if ( 'full' === $attributes['ctaWidth'] ) {
        $cta_classes[] = 'is-full';
    } elseif ( 'fixed-centered' === $attributes['ctaWidth'] ) {
        $cta_classes[] = 'is-fixed';
    }

    $cta_style             = '';
    $cta_background_color  = generatepress_child_product_card_sanitize_color( $attributes['ctaBackgroundColor'] );
    $cta_text_color        = generatepress_child_product_card_sanitize_color( $attributes['ctaTextColor'] );

    if ( $cta_background_color ) {
        $cta_style .= 'background:' . $cta_background_color . ';';
    }

    if ( $cta_text_color ) {
        $cta_style .= 'color:' . $cta_text_color . ';';
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
                            <<?php echo esc_attr( $heading_tag ); ?> class="pc-title" style="<?php echo esc_attr( $attributes['titleColor'] ? 'color:' . generatepress_child_product_card_sanitize_color( $attributes['titleColor'] ) . ';' : '' ); ?>">
                                <?php echo esc_html( $attributes['title'] ); ?>
                            </<?php echo esc_attr( $heading_tag ); ?>>
                        <?php endif; ?>

                        <?php if ( $attributes['subheaderText'] ) : ?>
                            <div class="pc-subheader" style="<?php echo esc_attr( $attributes['subheaderColor'] ? 'color:' . generatepress_child_product_card_sanitize_color( $attributes['subheaderColor'] ) . ';' : '' ); ?>"><?php echo wp_kses_post( $attributes['subheaderText'] ); ?></div>
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
                    <?php
                    foreach ( $attributes['bullets'] as $bullet ) {
                        if ( empty( $bullet['text'] ) ) {
                            continue;
                        }
                        ?>
                        <li>
                            <?php echo generatepress_child_product_card_render_icon( $attributes, 'bulletsIconSource', 'bulletsIconName', 'bulletsIconSvg', 'pc-bullet-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <span class="pc-bullet-text"><?php echo wp_kses_post( $bullet['text'] ); ?></span>
                        </li>
                        <?php
                    }
                    ?>
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
                    <a href="<?php echo esc_url( $attributes['bottomLinkUrl'] ); ?>" <?php echo $attributes['bottomLinkOpenInNewTab'] ? 'target="_blank"' : ''; ?> <?php echo ! empty( $bottom_rel ) ? 'rel="' . esc_attr( implode( ' ', $bottom_rel ) ) . '"' : ''; ?> style="<?php echo esc_attr( $attributes['bottomLinkColor'] ? 'color:' . generatepress_child_product_card_sanitize_color( $attributes['bottomLinkColor'] ) . ';' : '' ); ?>">
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
