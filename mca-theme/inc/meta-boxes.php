<?php
/**
 * Meta boxes for the collection post types, plus the sector term fields.
 *
 * The fields are declared once in mca_field_schema() and both the render and
 * the save pass walk that same array, so a new field cannot be displayed and
 * left unsaved (or the reverse).
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions per post type.
 *
 * type: text | textarea | number | icon | checkbox
 *
 * @return array<string,array<string,array>>
 */
function mca_field_schema() {
	return array(
		'mca_service' => array(
			'icon' => array(
				'type'  => 'icon',
				'label' => __( 'Icono', 'mca' ),
			),
			'text' => array(
				'type'  => 'textarea',
				'label' => __( 'Descripción', 'mca' ),
				'help'  => __( 'Una o dos frases. Se muestra bajo el título de la tarjeta.', 'mca' ),
			),
		),
		'mca_stat'    => array(
			'count_to' => array(
				'type'  => 'number',
				'label' => __( 'Número que se anima', 'mca' ),
				'help'  => __( 'El título debe empezar por este número (título «17+ años», aquí «17»). Déjalo vacío si la cifra no es numérica, como «ISO 9001:2015».', 'mca' ),
			),
			'icon'     => array(
				'type'  => 'icon',
				'label' => __( 'Icono', 'mca' ),
			),
			'text'     => array(
				'type'  => 'textarea',
				'label' => __( 'Descripción', 'mca' ),
			),
		),
		'mca_step'    => array(
			'icon' => array(
				'type'  => 'icon',
				'label' => __( 'Icono', 'mca' ),
				'help'  => __( 'El número «Paso N» se genera solo según el campo «Orden» de la caja Atributos.', 'mca' ),
			),
		),
		'mca_reason'  => array(
			'text' => array(
				'type'  => 'textarea',
				'label' => __( 'Descripción', 'mca' ),
			),
		),
		'mca_client'  => array(
			'cap'  => array(
				'type'  => 'number',
				'label' => __( 'Altura óptica (px)', 'mca' ),
				'help'  => __( 'Cada marca se percibe de distinto tamaño a igual altura. Punto de partida: 62 dividido entre la raíz cuadrada de (ancho / alto) de la imagen; luego ajusta a ojo hasta que pese igual que las demás. Vacío = 44 px.', 'mca' ),
			),
			'pair' => array(
				'type'  => 'checkbox',
				'label' => __( 'Compartir fila con el siguiente logo', 'mca' ),
				'help'  => __( 'Coloca este logo y el siguiente lado a lado en vez de uno debajo del otro.', 'mca' ),
			),
		),
	);
}

/**
 * Register one meta box per collection type.
 */
function mca_add_meta_boxes() {
	foreach ( mca_field_schema() as $post_type => $fields ) {
		add_meta_box(
			'mca_fields',
			__( 'Datos de la landing', 'mca' ),
			'mca_render_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'mca_add_meta_boxes' );

/**
 * Render the fields declared for the current post type.
 *
 * @param WP_Post $post Post being edited.
 */
function mca_render_meta_box( $post ) {
	$schema = mca_field_schema();
	if ( ! isset( $schema[ $post->post_type ] ) ) {
		return;
	}

	wp_nonce_field( 'mca_save_fields', 'mca_fields_nonce' );

	echo '<table class="form-table" role="presentation"><tbody>';

	foreach ( $schema[ $post->post_type ] as $key => $field ) {
		$id    = 'mca_field_' . $key;
		$name  = 'mca_fields[' . $key . ']';
		$value = get_post_meta( $post->ID, '_mca_' . $key, true );

		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';

		switch ( $field['type'] ) {
			case 'icon':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
				foreach ( mca_icons() as $slug => $label ) {
					printf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $slug ),
						selected( $value, $slug, false ),
						esc_html( $label )
					);
				}
				echo '</select>';
				break;

			case 'textarea':
				echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="3" class="large-text">' . esc_textarea( $value ) . '</textarea>';
				break;

			case 'number':
				echo '<input type="number" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" class="small-text">';
				break;

			case 'checkbox':
				echo '<label><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1"' . checked( $value, '1', false ) . '> ' . esc_html( $field['label'] ) . '</label>';
				break;

			default:
				echo '<input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" class="regular-text">';
		}

		if ( ! empty( $field['help'] ) ) {
			echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
		}

		echo '</td></tr>';
	}

	echo '</tbody></table>';
}

/**
 * Persist the fields declared for the saved post type.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function mca_save_meta_box( $post_id, $post ) {
	$schema = mca_field_schema();
	if ( ! isset( $schema[ $post->post_type ] ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['mca_fields_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['mca_fields_nonce'] ) ), 'mca_save_fields' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$submitted = array();
	if ( isset( $_POST['mca_fields'] ) && is_array( $_POST['mca_fields'] ) ) {
		$submitted = wp_unslash( $_POST['mca_fields'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- every value is sanitised per field type below.
	}

	foreach ( $schema[ $post->post_type ] as $key => $field ) {
		$raw = isset( $submitted[ $key ] ) ? $submitted[ $key ] : '';

		switch ( $field['type'] ) {
			case 'icon':
				$value = mca_icon_slug( sanitize_key( $raw ) );
				break;
			case 'textarea':
				$value = sanitize_textarea_field( $raw );
				break;
			case 'number':
				$value = ( '' === trim( (string) $raw ) ) ? '' : (string) absint( $raw );
				break;
			case 'checkbox':
				$value = ( '1' === (string) $raw ) ? '1' : '';
				break;
			default:
				$value = sanitize_text_field( $raw );
		}

		if ( '' === $value ) {
			delete_post_meta( $post_id, '_mca_' . $key );
		} else {
			update_post_meta( $post_id, '_mca_' . $key, $value );
		}
	}
}
add_action( 'save_post', 'mca_save_meta_box', 10, 2 );

/* -------------------------------------------------------------------------
 * Sector term fields: the pill colour and the panel order.
 * ---------------------------------------------------------------------- */

/**
 * Pill styles available to a sector.
 *
 * @return array<string,string>
 */
function mca_pill_styles() {
	return array(
		'blue' => __( 'Azul (fondo azul, texto blanco)', 'mca' ),
		'navy' => __( 'Marino (fondo oscuro, texto blanco)', 'mca' ),
		'sky'  => __( 'Celeste (fondo claro, texto marino)', 'mca' ),
	);
}

/**
 * Render the pill and order controls.
 *
 * @param string $pill     Current pill style.
 * @param string $position Current position.
 * @param bool   $rows     True to wrap in table rows (edit screen), false for divs (add screen).
 */
function mca_sector_controls( $pill, $position, $rows ) {
	$open_pill  = $rows ? '<tr class="form-field"><th scope="row">' : '<div class="form-field">';
	$mid_pill   = $rows ? '</th><td>' : '';
	$close_pill = $rows ? '</td></tr>' : '</div>';

	echo $open_pill; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup.
	echo '<label for="mca_pill">' . esc_html__( 'Estilo de la etiqueta', 'mca' ) . '</label>';
	echo $mid_pill; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup.
	echo '<select id="mca_pill" name="mca_pill">';
	foreach ( mca_pill_styles() as $value => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $value ),
			selected( $pill, $value, false ),
			esc_html( $label )
		);
	}
	echo '</select>';
	echo $close_pill; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup.

	echo $open_pill; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup.
	echo '<label for="mca_position">' . esc_html__( 'Orden del panel', 'mca' ) . '</label>';
	echo $mid_pill; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup.
	echo '<input type="number" id="mca_position" name="mca_position" value="' . esc_attr( '' === $position ? '0' : $position ) . '">';
	echo '<p class="description">' . esc_html__( 'Menor número, más a la izquierda.', 'mca' ) . '</p>';
	echo $close_pill; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup.
}

/**
 * Fields on the "add sector" form.
 */
function mca_sector_add_fields() {
	mca_sector_controls( 'blue', '0', false );
}
add_action( 'mca_sector_add_form_fields', 'mca_sector_add_fields' );

/**
 * Fields on the "edit sector" form.
 *
 * @param WP_Term $term Term being edited.
 */
function mca_sector_edit_fields( $term ) {
	mca_sector_controls(
		get_term_meta( $term->term_id, '_mca_pill', true ),
		get_term_meta( $term->term_id, '_mca_position', true ),
		true
	);
}
add_action( 'mca_sector_edit_form_fields', 'mca_sector_edit_fields' );

/**
 * Persist the sector term fields.
 *
 * @param int $term_id Term ID.
 */
function mca_sector_save_fields( $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	// The taxonomy screens carry their own nonce; bail when it is absent.
	if ( ! isset( $_POST['_wpnonce'] ) && ! isset( $_POST['_wpnonce_add-tag'] ) ) {
		return;
	}

	if ( isset( $_POST['mca_pill'] ) ) {
		$pill = sanitize_key( wp_unslash( $_POST['mca_pill'] ) );
		update_term_meta( $term_id, '_mca_pill', array_key_exists( $pill, mca_pill_styles() ) ? $pill : 'blue' );
	}

	if ( isset( $_POST['mca_position'] ) ) {
		update_term_meta( $term_id, '_mca_position', absint( wp_unslash( $_POST['mca_position'] ) ) );
	}
}
add_action( 'created_mca_sector', 'mca_sector_save_fields' );
add_action( 'edited_mca_sector', 'mca_sector_save_fields' );
