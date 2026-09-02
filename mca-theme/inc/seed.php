<?php
/**
 * One-time seeding of the landing's collections.
 *
 * The copy shipped hardcoded in the templates. Moving it to custom post types
 * would have left an empty page on first activation, so this writes the current
 * content into the database once and then stays out of the way.
 *
 * It is deliberately not idempotent per item: it runs once, guarded by an
 * option, and never touches the data again. Re-running it would resurrect
 * entries the editor deleted on purpose.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bump to re-seed on a future release. The stored value is compared, so an
 * already-seeded site only runs the newer pass.
 */
define( 'MCA_SEED_VERSION', 2 );

/**
 * The content that used to live in the templates.
 *
 * @return array
 */
function mca_seed_data() {
	return array(
		'mca_service' => array(
			array(
				'title' => 'Telemarketing',
				'meta'  => array(
					'icon' => 'headset',
					'text' => 'Campañas de captación y venta con protocolos y speech auditados bajo COPC.',
				),
			),
			array(
				'title' => 'Atención al cliente',
				'meta'  => array(
					'icon' => 'headset',
					'text' => 'Servicio permanente centrado en la experiencia y satisfacción plena del cliente.',
				),
			),
			array(
				'title' => 'Gestión de campo – Convenios',
				'meta'  => array(
					'icon' => 'headset',
					'text' => 'Cobertura en terreno con seguimiento y reportería estratégica en tiempo real.',
				),
			),
			array(
				'title' => 'Cobranzas',
				'meta'  => array(
					'icon' => 'headset',
					'text' => 'Gestión de recuperación con enfoque en resultados y cumplimiento normativo.',
				),
			),
			array(
				'title' => 'Back Office y reclamos',
				'meta'  => array(
					'icon' => 'headset',
					'text' => 'Procesamiento administrativo y gestión de reclamos con trazabilidad completa.',
				),
			),
			array(
				'title' => 'Canales digitales',
				'meta'  => array(
					'icon' => 'nodes',
					'text' => 'Plataforma omnicanal para administrar tus RR.SS, WhatsApp, mailing en una sola plataforma. Además de nuestros agentes virtuales de voz y canal escrito.',
				),
			),
		),
		'mca_stat'    => array(
			array(
				'title' => '17+ años',
				'meta'  => array(
					'count_to' => '17',
					'icon'     => 'star-check',
					'text'     => 'de experiencia operando desde 2009',
				),
			),
			array(
				'title' => '100% capital',
				'meta'  => array(
					'count_to' => '100',
					'icon'     => 'flag',
					'text'     => 'nacional peruano',
				),
			),
			array(
				'title' => 'ISO 9001:2015',
				'meta'  => array(
					'icon' => 'seal-check',
					'text' => 'Procesos de calidad certificados',
				),
			),
			array(
				'title' => 'Metodología COPC',
				'meta'  => array(
					'icon' => 'gear-refresh',
					'text' => 'Gestión basada en resultados',
				),
			),
		),
		'mca_step'    => array(
			array(
				'title' => 'Diagnóstico',
				'meta'  => array( 'icon' => 'search' ),
			),
			array(
				'title' => 'Estrategia',
				'meta'  => array( 'icon' => 'puzzle' ),
			),
			array(
				'title' => 'Implementación',
				'meta'  => array( 'icon' => 'chart-cycle' ),
			),
			array(
				'title' => 'Medición',
				'meta'  => array( 'icon' => 'chart-up' ),
			),
			array(
				'title' => 'Optimización',
				'meta'  => array( 'icon' => 'gear-cycle' ),
			),
		),
		'mca_reason'  => array(
			array(
				'title' => 'Especialización financiera',
				'meta'  => array( 'text' => '17 años enfocados en banca y financieras.' ),
			),
			array(
				'title' => 'Altos estándares de calidad',
				'meta'  => array( 'text' => 'Certificados en ISO 9001, metodología COPC y en camino hacia la certificación ISO 27000.' ),
			),
			array(
				'title' => 'Gestión antifraude',
				'meta'  => array( 'text' => 'Gestión de mesas de control, que garantizan 100% de la calidad de las ventas y sus procesos, que tienen como resultado ventas certificadas y libres de malas praxis.' ),
			),
			array(
				'title' => 'En campo Convenios',
				'meta'  => array( 'text' => 'Nos diferenciamos por lograr automatizar el proceso con tecnología que nos permite controlar y medir en tiempo real, especializando a la FF.VV y aumentando su productividad.' ),
			),
		),
	);
}

/**
 * Client sectors and their logos, in display order.
 *
 * The slug matters: with no featured image set, the template falls back to
 * assets/img/logos/{slug}.png, which is how the bundled logos keep working
 * without copying eleven files into the media library.
 *
 * @return array
 */
function mca_seed_clients() {
	return array(
		array(
			'sector'   => 'Sector financiero',
			'pill'     => 'blue',
			'position' => 0,
			'logos'    => array(
				array(
					'slug'  => 'bbva',
					'title' => 'BBVA',
					'cap'   => '34',
				),
				array(
					'slug'  => 'financiera-oh',
					'title' => 'Financiera Oh!',
					'cap'   => '26',
				),
				array(
					'slug'  => 'diners-club',
					'title' => 'Diners Club International',
					'cap'   => '60',
				),
				array(
					'slug'  => 'scotiabank',
					'title' => 'Scotiabank',
					'cap'   => '58',
				),
			),
		),
		array(
			'sector'   => 'Sector estatal',
			'pill'     => 'navy',
			'position' => 1,
			'logos'    => array(
				array(
					'slug'  => 'fondo-mivivienda',
					'title' => 'Fondo Mivivienda',
					'cap'   => '50',
				),
				array(
					'slug'  => 'mac',
					'title' => 'MAC — Mejor Atención al Ciudadano',
					'cap'   => '36',
				),
				array(
					'slug'  => 'gilat',
					'title' => 'Gilat Satellite Networks',
					'cap'   => '68',
				),
			),
		),
		array(
			'sector'   => 'Otros sectores',
			'pill'     => 'sky',
			'position' => 2,
			'logos'    => array(
				array(
					'slug'  => '3m',
					'title' => '3M',
					'cap'   => '45',
					'pair'  => '1',
				),
				array(
					'slug'  => 'pecsa-gas',
					'title' => 'Pecsa Gas',
					'cap'   => '72',
				),
				array(
					'slug'  => 'quimica-suiza',
					'title' => 'Química Suiza',
					'cap'   => '45',
				),
				array(
					'slug'  => 'kimberly-clark',
					'title' => 'Kimberly-Clark',
					'cap'   => '26',
				),
			),
		),
	);
}

/**
 * Insert one collection item.
 *
 * @param string $post_type Post type.
 * @param string $title     Post title.
 * @param string $slug      Post slug, or empty to derive it.
 * @param int    $order     menu_order.
 * @param array  $meta      Meta values keyed without the _mca_ prefix.
 * @return int Inserted post ID, 0 on failure.
 */
function mca_seed_insert( $post_type, $title, $slug, $order, $meta ) {
	$post_id = wp_insert_post(
		array(
			'post_type'   => $post_type,
			'post_title'  => $title,
			'post_name'   => $slug,
			'post_status' => 'publish',
			'menu_order'  => $order,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return 0;
	}

	foreach ( $meta as $key => $value ) {
		update_post_meta( $post_id, '_mca_' . $key, $value );
	}

	return $post_id;
}

/**
 * Items added after the first release.
 *
 * A site seeded at version 1 already has its collections, so the initial pass
 * skips them wholesale. Entries listed here are added individually and only
 * when nothing carries that title yet, which keeps a deletion by the editor
 * from being undone on the next upgrade.
 *
 * @return array<int,array{version:int,post_type:string,title:string,meta:array}>
 */
function mca_seed_additions() {
	return array(
		array(
			'version'   => 2,
			'post_type' => 'mca_reason',
			'title'     => 'En campo Convenios',
			'meta'      => array( 'text' => 'Nos diferenciamos por lograr automatizar el proceso con tecnología que nos permite controlar y medir en tiempo real, especializando a la FF.VV y aumentando su productividad.' ),
		),
	);
}

/**
 * Apply the additions a site has not seen yet.
 *
 * @param int $desde Seed version already stored for this site.
 */
function mca_seed_apply_additions( $desde ) {
	foreach ( mca_seed_additions() as $item ) {
		if ( $item['version'] <= $desde ) {
			continue;
		}

		$orden = 0;
		foreach ( mca_items( $item['post_type'] ) as $existente ) {
			if ( $existente->post_title === $item['title'] ) {
				continue 2;
			}
			$orden = max( $orden, (int) $existente->menu_order );
		}

		mca_seed_insert( $item['post_type'], $item['title'], '', $orden + 1, $item['meta'] );
	}
}

/**
 * Write the seed content, once.
 */
function mca_seed_content() {
	$almacenada = (int) get_option( 'mca_seed_version', 0 );

	if ( $almacenada >= MCA_SEED_VERSION ) {
		return;
	}

	// Claim the run before doing any work: two concurrent requests on first
	// load would otherwise both pass the check above and double the content.
	update_option( 'mca_seed_version', MCA_SEED_VERSION, true );

	// An already-seeded site only needs the newer entries.
	if ( $almacenada > 0 ) {
		mca_seed_apply_additions( $almacenada );
		return;
	}

	foreach ( mca_seed_data() as $post_type => $items ) {
		// Never seed on top of existing entries — an editor may have started
		// filling this collection by hand before the seeder ever ran.
		if ( mca_items( $post_type, 1 ) ) {
			continue;
		}
		foreach ( $items as $order => $item ) {
			mca_seed_insert( $post_type, $item['title'], '', $order, $item['meta'] );
		}
	}

	if ( mca_items( 'mca_client', 1 ) ) {
		return;
	}

	$order = 0;
	foreach ( mca_seed_clients() as $group ) {
		$term = wp_insert_term( $group['sector'], 'mca_sector' );
		if ( is_wp_error( $term ) ) {
			$existing = get_term_by( 'name', $group['sector'], 'mca_sector' );
			if ( ! $existing ) {
				continue;
			}
			$term = array( 'term_id' => $existing->term_id );
		}

		update_term_meta( $term['term_id'], '_mca_pill', $group['pill'] );
		update_term_meta( $term['term_id'], '_mca_position', $group['position'] );

		foreach ( $group['logos'] as $logo ) {
			$meta = array( 'cap' => $logo['cap'] );
			if ( ! empty( $logo['pair'] ) ) {
				$meta['pair'] = '1';
			}

			$post_id = mca_seed_insert( 'mca_client', $logo['title'], $logo['slug'], $order, $meta );
			if ( $post_id ) {
				wp_set_object_terms( $post_id, (int) $term['term_id'], 'mca_sector' );
			}
			$order++;
		}
	}
}

/**
 * Seed after the post types exist.
 *
 * after_switch_theme fires before init on the activation request, so the run is
 * deferred to admin_init, by which point register_post_type() has happened.
 */
function mca_maybe_seed() {
	if ( ! is_admin() || ! current_user_can( 'edit_posts' ) ) {
		return;
	}
	mca_seed_content();
}
add_action( 'admin_init', 'mca_maybe_seed' );
