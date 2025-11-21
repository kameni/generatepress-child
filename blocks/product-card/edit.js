( function( wp ) {
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, RichText, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, ToggleControl, RangeControl, Button, ButtonGroup, BaseControl, ColorPalette } = wp.components;
const { createElement: el, Fragment } = wp.element;

const uuidv4 = () => {
if ( window.crypto && window.crypto.randomUUID ) {
return window.crypto.randomUUID();
}
return 'pc-' + Date.now() + '-' + Math.floor( Math.random() * 10000 );
};

const colorChoices = [ '#1e1e1e', '#ffffff', '#0f766e', '#f97316', '#2563eb', '#10b981', '#facc15', '#9ca3af' ];

const colorPalette = ( value, onChange ) =>
el( ColorPalette, {
colors: colorChoices.map( ( color ) => ( { color, name: color } ) ),
value,
onChange: ( newValue ) => onChange( newValue || '' ),
} );

const iconPanel = ( attributes, setAttributes, prefix, label ) =>
el(
PanelBody,
{ title: label, initialOpen: false },
[
el( TextControl, {
label: __( 'Core icon name', 'generatepress-child' ),
value: attributes[ `${ prefix }IconName` ],
onChange: ( value ) => setAttributes( { [ `${ prefix }IconName` ]: value } ),
help: __( 'Example: check, star-filled, arrow-right', 'generatepress-child' ),
} ),
el( TextControl, {
label: __( 'Custom SVG markup', 'generatepress-child' ),
value: attributes[ `${ prefix }IconSvg` ],
onChange: ( value ) => setAttributes( { [ `${ prefix }IconSvg` ]: value } ),
help: __( 'Paste trusted SVG markup to override the core icon.', 'generatepress-child' ),
} ),
el( ToggleControl, {
label: __( 'Use custom SVG', 'generatepress-child' ),
checked: attributes[ `${ prefix }IconSource` ] === 'custom',
onChange: ( value ) => setAttributes( { [ `${ prefix }IconSource` ]: value ? 'custom' : 'core' } ),
} ),
]
);

registerBlockType( 'generatepress-child/product-card', {
edit( { attributes, setAttributes } ) {
const blockProps = useBlockProps( { className: 'pc-block-editor' } );

const updateBullet = ( id, value ) => {
const updated = attributes.bullets.map( ( bullet ) => ( bullet.id === id ? { ...bullet, text: value } : bullet ) );
setAttributes( { bullets: updated } );
};

const addBullet = () => {
const newBullet = { id: uuidv4(), text: __( 'New bullet', 'generatepress-child' ) };
setAttributes( { bullets: [ ...attributes.bullets, newBullet ] } );
};

const removeBullet = ( id ) => {
setAttributes( { bullets: attributes.bullets.filter( ( bullet ) => bullet.id !== id ) } );
};

const headerStyles = {
color: attributes.titleColor || undefined,
fontSize: attributes.titleFontSize || undefined,
};

const subheaderStyles = {
color: attributes.subheaderColor || undefined,
fontSize: attributes.subheaderFontSize || undefined,
};

return el(
Fragment,
null,
[
el(
InspectorControls,
null,
[
el(
PanelBody,
{ title: __( 'Layout & Card', 'generatepress-child' ), initialOpen: true },
[
el( BaseControl, { label: __( 'Background', 'generatepress-child' ) }, colorPalette( attributes.cardBackgroundColor, ( value ) => setAttributes( { cardBackgroundColor: value } ) ) ),
el( TextControl, {
label: __( 'Border color', 'generatepress-child' ),
value: attributes.cardBorderColor,
onChange: ( value ) => setAttributes( { cardBorderColor: value } ),
} ),
el( RangeControl, {
label: __( 'Border radius', 'generatepress-child' ),
min: 0,
max: 48,
value: attributes.cardBorderRadius,
onChange: ( value ) => setAttributes( { cardBorderRadius: value } ),
} ),
el( RangeControl, {
label: __( 'Row spacing', 'generatepress-child' ),
min: 0,
max: 48,
value: attributes.rowSpacing,
onChange: ( value ) => setAttributes( { rowSpacing: value } ),
} ),
]
),
el(
PanelBody,
{ title: __( 'Banner', 'generatepress-child' ), initialOpen: false },
[
el( TextControl, {
label: __( 'Banner text', 'generatepress-child' ),
value: attributes.bannerText,
onChange: ( value ) => setAttributes( { bannerText: value } ),
} ),
el( BaseControl, { label: __( 'Banner color 1', 'generatepress-child' ) }, colorPalette( attributes.bannerColor1, ( value ) => setAttributes( { bannerColor1: value } ) ) ),
el( BaseControl, { label: __( 'Banner color 2', 'generatepress-child' ) }, colorPalette( attributes.bannerColor2, ( value ) => setAttributes( { bannerColor2: value } ) ) ),
el( BaseControl, { label: __( 'Text color', 'generatepress-child' ) }, colorPalette( attributes.bannerTextColor, ( value ) => setAttributes( { bannerTextColor: value } ) ) ),
el( ToggleControl, {
label: __( 'Use gradient', 'generatepress-child' ),
checked: attributes.bannerUseGradient,
onChange: ( value ) => setAttributes( { bannerUseGradient: value } ),
} ),
]
),
el(
PanelBody,
{ title: __( 'Header & Rating', 'generatepress-child' ), initialOpen: false },
[
el( TextControl, {
label: __( 'Order number', 'generatepress-child' ),
value: attributes.orderNumber,
onChange: ( value ) => setAttributes( { orderNumber: value } ),
} ),
el( TextControl, {
label: __( 'Title', 'generatepress-child' ),
value: attributes.title,
onChange: ( value ) => setAttributes( { title: value } ),
} ),
el( TextControl, {
label: __( 'Subheader', 'generatepress-child' ),
value: attributes.subheaderText,
onChange: ( value ) => setAttributes( { subheaderText: value } ),
} ),
el( TextControl, {
label: __( 'Rating score', 'generatepress-child' ),
value: attributes.ratingScore,
onChange: ( value ) => setAttributes( { ratingScore: value } ),
} ),
el( TextControl, {
label: __( 'Rating title', 'generatepress-child' ),
value: attributes.ratingTitle,
onChange: ( value ) => setAttributes( { ratingTitle: value } ),
} ),
el( TextControl, {
label: __( 'Rating subheader', 'generatepress-child' ),
value: attributes.ratingSubheader,
onChange: ( value ) => setAttributes( { ratingSubheader: value } ),
} ),
el( RangeControl, {
label: __( 'Star count', 'generatepress-child' ),
min: 0,
max: 5,
value: attributes.ratingStarsCount,
onChange: ( value ) => setAttributes( { ratingStarsCount: value } ),
} ),
]
),
el(
PanelBody,
{ title: __( 'Bullets', 'generatepress-child' ), initialOpen: false },
[
attributes.bullets.map( ( bullet ) =>
el(
'div',
{ key: bullet.id, className: 'pc-bullet-row' },
[
el( RichText, {
tagName: 'div',
value: bullet.text,
onChange: ( value ) => updateBullet( bullet.id, value ),
placeholder: __( 'Bullet text…', 'generatepress-child' ),
} ),
el(
Button,
{ variant: 'secondary', onClick: () => removeBullet( bullet.id ) },
__( 'Remove', 'generatepress-child' )
),
]
)
),
el( Button, { variant: 'primary', onClick: addBullet }, __( 'Add bullet', 'generatepress-child' ) ),
]
),
el(
PanelBody,
{ title: __( 'Offer pill', 'generatepress-child' ), initialOpen: false },
[
el( TextControl, {
label: __( 'Offer text', 'generatepress-child' ),
value: attributes.offerText,
onChange: ( value ) => setAttributes( { offerText: value } ),
} ),
el( BaseControl, { label: __( 'Background', 'generatepress-child' ) }, colorPalette( attributes.offerBackgroundColor, ( value ) => setAttributes( { offerBackgroundColor: value } ) ) ),
el( TextControl, {
label: __( 'Border color', 'generatepress-child' ),
value: attributes.offerBorderColor,
onChange: ( value ) => setAttributes( { offerBorderColor: value } ),
} ),
]
),
el(
PanelBody,
{ title: __( 'CTA button', 'generatepress-child' ), initialOpen: false },
[
el( TextControl, {
label: __( 'Button text', 'generatepress-child' ),
value: attributes.ctaText,
onChange: ( value ) => setAttributes( { ctaText: value } ),
} ),
el( TextControl, {
label: __( 'Button URL', 'generatepress-child' ),
value: attributes.ctaUrl,
onChange: ( value ) => setAttributes( { ctaUrl: value } ),
} ),
el( ToggleControl, {
label: __( 'Open in new tab', 'generatepress-child' ),
checked: attributes.ctaOpenInNewTab,
onChange: ( value ) => setAttributes( { ctaOpenInNewTab: value } ),
} ),
el(
ButtonGroup,
null,
[
el( Button, { variant: attributes.ctaWidth === 'auto' ? 'primary' : 'secondary', onClick: () => setAttributes( { ctaWidth: 'auto' } ) }, __( 'Auto width', 'generatepress-child' ) ),
el( Button, { variant: attributes.ctaWidth === 'full' ? 'primary' : 'secondary', onClick: () => setAttributes( { ctaWidth: 'full' } ) }, __( 'Full width', 'generatepress-child' ) ),
el( Button, { variant: attributes.ctaWidth === 'fixed-centered' ? 'primary' : 'secondary', onClick: () => setAttributes( { ctaWidth: 'fixed-centered' } ) }, __( 'Fixed centered', 'generatepress-child' ) ),
]
),
el( BaseControl, { label: __( 'Button background', 'generatepress-child' ) }, colorPalette( attributes.ctaBackgroundColor, ( value ) => setAttributes( { ctaBackgroundColor: value } ) ) ),
el( BaseControl, { label: __( 'Button text color', 'generatepress-child' ) }, colorPalette( attributes.ctaTextColor, ( value ) => setAttributes( { ctaTextColor: value } ) ) ),
]
),
el(
PanelBody,
{ title: __( 'Footer & Link', 'generatepress-child' ), initialOpen: false },
[
el( TextControl, {
label: __( 'Footer text', 'generatepress-child' ),
value: attributes.footerText,
onChange: ( value ) => setAttributes( { footerText: value } ),
} ),
el( TextControl, {
label: __( 'Bottom link text', 'generatepress-child' ),
value: attributes.bottomLinkText,
onChange: ( value ) => setAttributes( { bottomLinkText: value } ),
} ),
el( TextControl, {
label: __( 'Bottom link URL', 'generatepress-child' ),
value: attributes.bottomLinkUrl,
onChange: ( value ) => setAttributes( { bottomLinkUrl: value } ),
} ),
el( ToggleControl, {
label: __( 'Open link in new tab', 'generatepress-child' ),
checked: attributes.bottomLinkOpenInNewTab,
onChange: ( value ) => setAttributes( { bottomLinkOpenInNewTab: value } ),
} ),
]
),
iconPanel( attributes, setAttributes, 'banner', __( 'Banner icon', 'generatepress-child' ) ),
iconPanel( attributes, setAttributes, 'bullets', __( 'Bullets icon', 'generatepress-child' ) ),
iconPanel( attributes, setAttributes, 'offer', __( 'Offer icon', 'generatepress-child' ) ),
iconPanel( attributes, setAttributes, 'cta', __( 'CTA icon', 'generatepress-child' ) ),
iconPanel( attributes, setAttributes, 'bottomLink', __( 'Bottom link icon', 'generatepress-child' ) ),
]
),
el(
'div',
blockProps,
el(
'div',
{ className: 'pc-card-preview' },
[
( attributes.bannerText || attributes.bannerIconName || attributes.bannerIconSvg ) && el( 'div', { className: 'pc-banner-preview' }, attributes.bannerText ),
el(
'div',
{ className: 'pc-header-preview' },
[
el( 'div', { className: 'pc-order-preview' }, attributes.orderNumber ),
el(
'div',
null,
[
el( RichText, {
tagName: attributes.titleHeadingLevel || 'h3',
className: 'pc-title-preview',
style: headerStyles,
value: attributes.title,
onChange: ( value ) => setAttributes( { title: value } ),
placeholder: __( 'Product title…', 'generatepress-child' ),
} ),
el( RichText, {
tagName: 'div',
className: 'pc-subheader-preview',
style: subheaderStyles,
value: attributes.subheaderText,
onChange: ( value ) => setAttributes( { subheaderText: value } ),
placeholder: __( 'Subheader…', 'generatepress-child' ),
} ),
]
),
el(
'div',
{ className: 'pc-rating-preview' },
[
el( 'div', { className: 'pc-rating-score' }, attributes.ratingScore ),
el( 'div', { className: 'pc-rating-title' }, attributes.ratingTitle ),
el( 'div', { className: 'pc-rating-subheader' }, attributes.ratingSubheader ),
]
)
]
),
attributes.bullets && attributes.bullets.length > 0 &&
el(
'ul',
{ className: 'pc-bullets-preview' },
attributes.bullets.map( ( bullet ) => el( 'li', { key: bullet.id }, bullet.text ) )
),
( attributes.offerText || attributes.offerIconName || attributes.offerIconSvg ) && el( 'div', { className: 'pc-offer-preview' }, attributes.offerText ),
attributes.ctaText && attributes.ctaUrl && el( 'div', { className: 'pc-cta-preview' }, attributes.ctaText ),
attributes.footerText && el( 'div', { className: 'pc-footer-preview' }, attributes.footerText ),
attributes.bottomLinkText && el( 'div', { className: 'pc-bottom-preview' }, attributes.bottomLinkText ),
]
)
)
]
);
},
save() {
return null;
},
} );
} )( window.wp );
