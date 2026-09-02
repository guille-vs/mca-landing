<?php
/**
 * Custom post types for the landing's repeatable collections.
 *
 * These are content, not pages: none of them has a URL of its own, so they are
 * registered private (public => false) with the admin UI switched on. That
 * keeps them out of search, feeds and sitemaps while still giving the editor a
 * normal WordPress screen for each collection.
 *
 * Order is always menu_order, exposed through the "Atributos" box.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shared arguments for every collection type.
 *
 * @param array $labels    Label set.
 * @param array $supports  Supported features.
 * @param string $icon     Dashicon name.
 * @param int    $position Admin menu position.
 * @return array
 */
function mca_cpt_args( $labels, $supports, $icon, $position ) {
	return array(
		'labels'             => $labels,
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => false,
		'publicly_queryable' => false,
		'exclude_from_search'=> true,
		'has_archive'        => false,
		'rewrite'            => false,
		'query_var'          => false,
		'hierarchical'       => false,
		'menu_icon'          => $icon,
		'menu_position'      => $position,
		'supports'           => $supports,
		'capability_type'    => 'post',
	);
}

/**
 * Build a label set from the singular and plural names.
 *
 * @param string $singular Singular name.
 * @param string $plural   Plural name.
 * @param string $menu     Admin menu label.
 * @return array
 */
function mca_cpt_labels( $singular, $plural, $menu ) {
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'menu_name'          => $menu,
		/* translators: %s: singular item name */
		'add_new_item'       => sprintf( __( 'Añadir %s', 'mca' ), $singular ),
		/* translators: %s: singular item name */
		'edit_item'          => sprintf( __( 'Editar %s', 'mca' ), $singular ),
		/* translators: %s: singular item name */
		'new_item'           => sprintf( __( 'Nuevo %s', 'mca' ), $singular ),
		/* translators: %s: plural item name */
		'search_items'       => sprintf( __( 'Buscar %s', 'mca' ), $plural ),
		/* translators: %s: plural item name */
		'not_found'          => sprintf( __( 'No hay %s todavía', 'mca' ), $plural ),
		'all_items'          => $plural,
	);
}

/**
 * Register the collections and the client sector taxonomy.
 */
function mca_register_post_types() {
	register_post_type(
		'mca_service',
		mca_cpt_args(
			mca_cpt_labels( __( 'servicio', 'mca' ), __( 'Servicios', 'mca' ), __( 'MCA · Servicios', 'mca' ) ),
			array( 'title', 'page-attributes' ),
			'dashicons-screenoptions',
			26
		)
	);

	register_post_type(
		'mca_stat',
		mca_cpt_args(
			mca_cpt_labels( __( 'cifra', 'mca' ), __( 'Cifras', 'mca' ), __( 'MCA · Cifras', 'mca' ) ),
			array( 'title', 'page-attributes' ),
			'dashicons-chart-bar',
			27
		)
	);

	register_post_type(
		'mca_step',
		mca_cpt_args(
			mca_cpt_labels( __( 'paso', 'mca' ), __( 'Pasos', 'mca' ), __( 'MCA · Metodología', 'mca' ) ),
			array( 'title', 'page-attributes' ),
			'dashicons-controls-repeat',
			28
		)
	);

	register_post_type(
		'mca_reason',
		mca_cpt_args(
			mca_cpt_labels( __( 'motivo', 'mca' ), __( 'Motivos', 'mca' ), __( 'MCA · Por qué nosotros', 'mca' ) ),
			array( 'title', 'page-attributes' ),
			'dashicons-awards',
			29
		)
	);

	register_post_type(
		'mca_client',
		mca_cpt_args(
			mca_cpt_labels( __( 'cliente', 'mca' ), __( 'Clientes', 'mca' ), __( 'MCA · Clientes', 'mca' ) ),
			array( 'title', 'page-attributes', 'thumbnail' ),
			'dashicons-groups',
			30
		)
	);

	register_taxonomy(
		'mca_sector',
		'mca_client',
		array(
			'labels'            => array(
				'name'          => __( 'Sectores', 'mca' ),
				'singular_name' => __( 'sector', 'mca' ),
				'add_new_item'  => __( 'Añadir sector', 'mca' ),
				'edit_item'     => __( 'Editar sector', 'mca' ),
				'menu_name'     => __( 'Sectores', 'mca' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => false,
			'hierarchical'      => true,
			'rewrite'           => false,
			'query_var'         => false,
		)
	);
}
add_action( 'init', 'mca_register_post_types' );

/**
 * Sector panels, in display order, each with the clients that belong to it.
 *
 * Empty sectors are dropped: an editor who removes every logo from a sector
 * should see the panel disappear, not an empty card.
 *
 * @return array<int,array{term:WP_Term,pill:string,clients:WP_Post[]}>
 */
function mca_client_panels() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'mca_sector',
			'hide_empty' => true,
			'meta_key'   => '_mca_position',
			'orderby'    => 'meta_value_num',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) || ! $terms ) {
		return array();
	}

	$panels = array();
	foreach ( $terms as $term ) {
		$clients = get_posts(
			array(
				'post_type'   => 'mca_client',
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby'     => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'tax_query'   => array(
					array(
						'taxonomy' => 'mca_sector',
						'field'    => 'term_id',
						'terms'    => $term->term_id,
					),
				),
			)
		);

		if ( ! $clients ) {
			continue;
		}

		$pill = get_term_meta( $term->term_id, '_mca_pill', true );

		$panels[] = array(
			'term'    => $term,
			'pill'    => in_array( $pill, array( 'blue', 'navy', 'sky' ), true ) ? $pill : 'blue',
			'clients' => $clients,
		);
	}

	return $panels;
}

/**
 * Logo URL for a client.
 *
 * The eleven seeded clients have no featured image: their logo is the bundled
 * file named after the post slug. A featured image always wins, so an editor
 * can replace a bundled logo without touching the theme files.
 *
 * @param WP_Post $client Client post.
 * @return string Empty when neither source resolves.
 */
function mca_client_logo_url( $client ) {
	$thumb = get_post_thumbnail_id( $client );
	if ( $thumb ) {
		$src = wp_get_attachment_image_url( $thumb, 'full' );
		if ( $src ) {
			return $src;
		}
	}

	$file = 'assets/img/logos/' . $client->post_name . '.png';
	return file_exists( get_theme_file_path( $file ) ) ? get_theme_file_uri( $file ) : '';
}
