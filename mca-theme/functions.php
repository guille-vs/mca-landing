<?php
/**
 * MCA theme bootstrap.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

define( 'MCA_VERSION', '1.0.0' );

/**
 * Default for a field, read from the Customizer schema.
 *
 * add_setting()'s own `default` only reaches the Customizer preview, never
 * get_theme_mod() on the front end. Rather than repeat every default in the
 * templates, the schema stays the single source of truth and this flattens it
 * once per request.
 *
 * @param string $key Option key, without the mca_ prefix.
 * @return mixed
 */
function mca_option_default( $key ) {
	static $defaults = null;

	if ( null === $defaults ) {
		$defaults = array();
		if ( function_exists( 'mca_customizer_schema' ) ) {
			foreach ( mca_customizer_schema() as $section ) {
				foreach ( $section['fields'] as $field_key => $field ) {
					$defaults[ $field_key ] = isset( $field['default'] ) ? $field['default'] : '';
				}
			}
		}
	}

	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Read a theme option, falling back to its schema default.
 *
 * @param string $key     Option key, without the mca_ prefix.
 * @param mixed  $default Explicit fallback; omit to use the schema default.
 * @return mixed
 */
function mca_option( $key, $default = null ) {
	if ( null === $default ) {
		$default = mca_option_default( $key );
	}

	/*
	 * null is the sentinel for "never saved", which is the only case that may
	 * fall back to the default. An empty string is a deliberate choice by the
	 * editor — clearing the WhatsApp greeting or the second "about" paragraph
	 * has to stay cleared, and the earlier version silently refilled it.
	 */
	$value = get_theme_mod( 'mca_' . $key, null );
	return ( null === $value ) ? $default : $value;
}

/**
 * Navigation shown before a menu is assigned, so the header is never empty.
 */
function mca_nav_fallback() {
	$items = array(
		'#nosotros'    => __( 'Nosotros', 'mca' ),
		'#servicios'   => __( 'Servicios', 'mca' ),
		'#metodologia' => __( 'Metodología', 'mca' ),
		'#contacto'    => __( 'Contacto', 'mca' ),
	);
	$base = is_front_page() ? '' : home_url( '/' );
	foreach ( $items as $href => $label ) {
		printf(
			'<a class="nav__link" href="%s">%s</a>',
			esc_url( $base . $href ),
			esc_html( $label )
		);
	}
}

/**
 * Heading for the archive templates.
 *
 * get_the_archive_title() prefixes with "Category:" and similar; this keeps
 * the page headings clean.
 *
 * @return string
 */
function mca_archive_title() {
	if ( is_home() ) {
		$page_for_posts = (int) get_option( 'page_for_posts' );
		return $page_for_posts ? get_the_title( $page_for_posts ) : __( 'Blog', 'mca' );
	}
	if ( is_search() ) {
		/* translators: %s: search term */
		return sprintf( __( 'Resultados para «%s»', 'mca' ), get_search_query() );
	}
	if ( is_category() || is_tag() || is_tax() ) {
		return single_term_title( '', false );
	}
	if ( is_author() ) {
		return get_the_author();
	}
	return __( 'Artículos', 'mca' );
}

/**
 * Theme supports and menus.
 */
function mca_setup() {
	load_theme_textdomain( 'mca', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 48,
			'width'       => 334,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Navegación principal', 'mca' ),
		)
	);

	// The blog cards are a 4:3-ish thumbnail beside the excerpt.
	add_image_size( 'mca-post-card', 520, 520, true );
}
add_action( 'after_setup_theme', 'mca_setup' );

/**
 * Stylesheets, in dependency order.
 *
 * Each section keeps its own file so a template part and its styles stay
 * together. They are chained with dependencies rather than relying on
 * registration order, because the cascade genuinely matters here: the
 * component defaults must load before the sections that override them.
 */
function mca_assets() {
	$base = get_theme_file_uri( 'assets/css/' );
	$dir  = get_theme_file_path( 'assets/css/' );

	$sheets = array(
		'mca-fonts'      => 'fonts.css',
		'mca-tokens'     => 'tokens.css',
		'mca-base'       => 'base.css',
		'mca-components' => 'components.css',
		'mca-icons'      => 'icons.css',
		'mca-carousel'   => 'components/carousel.css',
		'mca-modal'      => 'components/modal.css',
		'mca-header'     => 'sections/header.css',
		'mca-hero'       => 'sections/hero.css',
		'mca-stats'      => 'sections/stats.css',
		'mca-about'      => 'sections/about.css',
		'mca-services'   => 'sections/services.css',
		'mca-clients'    => 'sections/clients.css',
		'mca-why'        => 'sections/why.css',
		'mca-method'     => 'sections/method.css',
		'mca-blog'       => 'sections/blog.css',
		'mca-cta'        => 'sections/cta.css',
		'mca-footer'     => 'sections/footer.css',
		'mca-whatsapp-fab' => 'sections/whatsapp-fab.css',
	);

	$previous = array();
	foreach ( $sheets as $handle => $file ) {
		wp_enqueue_style(
			$handle,
			$base . $file,
			$previous,
			file_exists( $dir . $file ) ? filemtime( $dir . $file ) : MCA_VERSION
		);
		$previous = array( $handle );
	}

	// style.css carries the theme header; WordPress expects it enqueued too.
	wp_enqueue_style( 'mca-style', get_stylesheet_uri(), $previous, MCA_VERSION );

	$js = get_theme_file_path( 'assets/js/main.js' );
	wp_enqueue_script(
		'mca-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		file_exists( $js ) ? filemtime( $js ) : MCA_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'mca_assets' );

/**
 * The reveal animation is CSS-driven and hidden until this class exists, so it
 * has to be set before first paint or the page flashes empty.
 */
function mca_js_class() {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
}
add_action( 'wp_head', 'mca_js_class', 1 );

require get_template_directory() . '/inc/content.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/meetings.php';

if ( is_admin() ) {
	require get_template_directory() . '/inc/meta-boxes.php';
	require get_template_directory() . '/inc/seed.php';
}
