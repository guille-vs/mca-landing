<?php
/**
 * Editable content helpers.
 *
 * The landing used to carry its copy inline. Everything now comes from the
 * Customizer (single fields) or from the custom post types registered in
 * post-types.php (repeatable collections). These helpers are the seam between
 * the two so the templates stay declarative.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icons available to the editor.
 *
 * Keys match the PNG masks in assets/img/icons and the `.icon--*` rules in
 * assets/css/icons.css. Adding an icon means adding all three; this list is
 * what the admin dropdowns render, so an unlisted icon cannot be picked.
 *
 * @return array<string,string> slug => human label.
 */
function mca_icons() {
	return array(
		'headset'      => __( 'Diadema / contact center', 'mca' ),
		'nodes'        => __( 'Nodos / canales digitales', 'mca' ),
		'star-check'   => __( 'Estrella con check', 'mca' ),
		'seal-check'   => __( 'Sello certificado', 'mca' ),
		'flag'         => __( 'Bandera', 'mca' ),
		'gear-refresh' => __( 'Engranaje con flechas', 'mca' ),
		'gear-cycle'   => __( 'Engranaje en ciclo', 'mca' ),
		'chart-cycle'  => __( 'Gráfico en ciclo', 'mca' ),
		'chart-up'     => __( 'Gráfico ascendente', 'mca' ),
		'search'       => __( 'Lupa / diagnóstico', 'mca' ),
		'puzzle'       => __( 'Pieza de rompecabezas', 'mca' ),
		'mail'         => __( 'Sobre / correo', 'mca' ),
		'phone'        => __( 'Teléfono', 'mca' ),
		'linkedin'     => __( 'LinkedIn', 'mca' ),
		'facebook'     => __( 'Facebook', 'mca' ),
	);
}

/**
 * Validate an icon slug against the registry.
 *
 * A stored slug can outlive its CSS rule (icon renamed, theme downgraded).
 * Falling back keeps a silent blank circle from reaching the page.
 *
 * @param string $slug     Stored slug.
 * @param string $fallback Slug to use when $slug is unknown.
 * @return string
 */
function mca_icon_slug( $slug, $fallback = 'headset' ) {
	return array_key_exists( $slug, mca_icons() ) ? $slug : $fallback;
}

/**
 * Read a post meta value written by this theme.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key without the _mca_ prefix.
 * @param mixed  $default Value to use when the meta is empty.
 * @return mixed
 */
function mca_meta( $post_id, $key, $default = '' ) {
	$value = get_post_meta( $post_id, '_mca_' . $key, true );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * Fetch an ordered collection.
 *
 * Ordered by menu_order so the editor controls the sequence by drag or by the
 * "Orden" box, with the title as a stable tiebreaker: two items sharing order 0
 * would otherwise swap places between requests and make the carousel jump.
 *
 * @param string $post_type Registered post type.
 * @param int    $limit     Maximum items, -1 for all.
 * @return WP_Post[]
 */
function mca_items( $post_type, $limit = -1 ) {
	return get_posts(
		array(
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'numberposts'      => $limit,
			'orderby'          => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'suppress_filters' => false,
		)
	);
}

/**
 * Render an image field stored as an attachment ID, with a bundled fallback.
 *
 * Every hero/section image shipped with the theme, so an empty field must fall
 * back to the file rather than leave a hole. Returns the URL only; callers own
 * the markup because their width/height and loading hints differ.
 *
 * @param string $key           Customizer key, without the mca_ prefix.
 * @param string $fallback_file Theme-relative path used when nothing is set.
 * @return string
 */
function mca_image_url( $key, $fallback_file ) {
	$id = (int) mca_option( $key, 0 );
	if ( $id > 0 ) {
		$src = wp_get_attachment_image_url( $id, 'full' );
		if ( $src ) {
			return $src;
		}
	}
	return get_theme_file_uri( $fallback_file );
}

/**
 * Alt text for an image field, falling back to the bundled description.
 *
 * @param string $key      Customizer key, without the mca_ prefix.
 * @param string $fallback Alt text used when nothing is set.
 * @return string
 */
function mca_image_alt( $key, $fallback ) {
	$id = (int) mca_option( $key, 0 );
	if ( $id > 0 ) {
		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		if ( '' !== $alt ) {
			return $alt;
		}
	}
	return $fallback;
}

/**
 * WhatsApp destination, with the greeting pre-filled.
 *
 * The number and the message are two separate fields on purpose: changing the
 * phone must not force the editor to retype the greeting, and vice versa.
 * add_query_arg preserves anything the editor already appended to the link.
 *
 * @return string Empty when no link is configured, so callers can hide the button.
 */
function mca_whatsapp_url() {
	$base = trim( (string) mca_option( 'whatsapp' ) );
	if ( '' === $base ) {
		return '';
	}

	$message = trim( (string) mca_option( 'whatsapp_message' ) );
	if ( '' === $message ) {
		return $base;
	}

	// add_query_arg expects the value already encoded.
	return add_query_arg( 'text', rawurlencode( $message ), $base );
}

/**
 * Print the inline reveal delay used to stagger a list.
 *
 * The first item carries no delay so it animates immediately; the rest step by
 * $step milliseconds.
 *
 * @param int $index Zero-based position.
 * @param int $step  Milliseconds between items.
 */
function mca_reveal_delay( $index, $step = 80 ) {
	if ( $index < 1 ) {
		return;
	}
	printf( ' style="--reveal-delay:%dms"', (int) ( $index * $step ) );
}
