<?php
/**
 * Customizer settings.
 *
 * Every single-value field on the landing lives here: section headings, hero
 * and CTA copy, button labels and the section photographs. Repeatable content
 * (services, stats, steps, reasons, client logos) is not here — it is a custom
 * post type, see inc/post-types.php.
 *
 * Each field's `default` is the copy the templates used to carry inline, so an
 * untouched install renders exactly what it rendered before.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Section and field definitions.
 *
 * type: text | textarea | url | image
 *
 * @return array
 */
function mca_customizer_schema() {
	return array(
		'mca_header'   => array(
			'title'       => __( 'Cabecera', 'mca' ),
			'description' => __( 'La barra superior. Los enlaces del menú se editan en Apariencia → Menús.', 'mca' ),
			'fields'      => array(
				'header_cta_label' => array(
					'label'       => __( 'Texto del botón', 'mca' ),
					'default'     => 'Agenda tu reunión',
					'description' => __( 'Su destino se configura en «Contacto».', 'mca' ),
				),
			),
		),
		'mca_hero'     => array(
			'title'  => __( 'Portada (hero)', 'mca' ),
			'fields' => array(
				'hero_title'      => array(
					'label'   => __( 'Titular', 'mca' ),
					'type'    => 'textarea',
					'default' => '17 años convirtiendo contacto en resultados para la banca peruana.',
				),
				'hero_text'       => array(
					'label'   => __( 'Párrafo', 'mca' ),
					'type'    => 'textarea',
					'default' => 'BPO & Contact Center, Business Analytics y Gestión de Campo, especializados en el sector financiero: Bancos, cajas y cooperativas. Nuestros procesos cuentan con certificación ISO 9001, y operamos bajo la metodología COPC obteniendo resultados tangibles y medibles, los mismos que hoy nos permiten liderar la venta en estos sectores.',
				),
				'hero_btn_label'  => array(
					'label'   => __( 'Texto del botón principal', 'mca' ),
					'default' => 'Solicitar reunión comercial',
				),
				'hero_btn_url'    => array(
					'label'       => __( 'Destino del botón principal', 'mca' ),
					'default'     => '#contacto',
					'description' => __( 'Un ancla como #contacto, o una URL completa.', 'mca' ),
				),
				'hero_btn2_label' => array(
					'label'       => __( 'Texto del botón de WhatsApp', 'mca' ),
					'default'     => 'Hablar por Whatsapp',
					'description' => __( 'El número se cambia en la sección «Contacto» de este mismo panel.', 'mca' ),
				),
				'hero_image'      => array(
					'label'       => __( 'Imagen principal', 'mca' ),
					'type'        => 'image',
					'description' => __( 'PNG con fondo transparente. Vacío = la imagen que trae el tema.', 'mca' ),
				),
				'hero_ribbon'     => array(
					'label'       => __( 'Fondo decorativo', 'mca' ),
					'type'        => 'image',
					'description' => __( 'La cinta que cruza la portada detrás del texto.', 'mca' ),
				),
			),
		),
		'mca_stats'    => array(
			'title'       => __( 'Cifras', 'mca' ),
			'edit_post_type' => 'mca_stat',
			'edit_label'     => __( 'Editar las tarjetas de cifras', 'mca' ),
			'description' => __( 'Las tarjetas se editan en «MCA · Cifras» del menú lateral.', 'mca' ),
			'fields'      => array(
				'stats_image' => array(
					'label' => __( 'Fotografía de fondo', 'mca' ),
					'type'  => 'image',
				),
			),
		),
		'mca_about'    => array(
			'title'  => __( 'Quiénes somos', 'mca' ),
			'fields' => array(
				'about_eyebrow' => array(
					'label'   => __( 'Antetítulo', 'mca' ),
					'default' => 'Quiénes somos',
				),
				'about_title'   => array(
					'label'   => __( 'Título', 'mca' ),
					'type'    => 'textarea',
					'default' => 'Factor humano, tecnología y experiencia especializada',
				),
				'about_text_1'  => array(
					'label'   => __( 'Primer párrafo', 'mca' ),
					'type'    => 'textarea',
					'default' => 'Master Center Américas inicia operaciones en 2009 con el mejor recurso humano, nos diferenciamos por tener verdaderos embajadores no empleados, en nuestra reciente encuesta de satisfacción, nuestros colaboradores nos califican como:',
				),
				'about_text_2'  => array(
					'label'       => __( 'Segundo párrafo', 'mca' ),
					'type'        => 'textarea',
					'default'     => 'Mejores empleadores por contar con excelentes condiciones remunerativas, tener un staff sólido y comprometido lo cual hace que se sientan como en casa.',
					'description' => __( 'Déjalo vacío para omitirlo.', 'mca' ),
				),
				'about_text_3'  => array(
					'label'       => __( 'Tercer párrafo', 'mca' ),
					'type'        => 'textarea',
					'default'     => 'Combinamos talento especializado con tecnología, procesos certificados y generamos información viva y de valor para la toma de decisiones y el cumplimiento de las metas comerciales.',
					'description' => __( 'Déjalo vacío para omitirlo.', 'mca' ),
				),
			),
		),
		'mca_services' => array(
			'title'       => __( 'Soluciones', 'mca' ),
			'edit_post_type' => 'mca_service',
			'edit_label'     => __( 'Editar las tarjetas de soluciones', 'mca' ),
			'description' => __( 'Las tarjetas se editan en «MCA · Servicios» del menú lateral.', 'mca' ),
			'fields'      => array(
				'services_title' => array(
					'label'   => __( 'Título de la sección', 'mca' ),
					'type'    => 'textarea',
					'default' => 'Soluciones de gestión y servicio.',
				),
			),
		),
		'mca_clients'  => array(
			'title'       => __( 'Clientes', 'mca' ),
			'edit_post_type' => 'mca_client',
			'edit_label'     => __( 'Editar los logos de clientes', 'mca' ),
			'description' => __( 'Los logos se editan en «MCA · Clientes», y los paneles en su submenú «Sectores».', 'mca' ),
			'fields'      => array(
				'clients_title' => array(
					'label'   => __( 'Título de la sección', 'mca' ),
					'type'    => 'textarea',
					'default' => 'Confían en nosotros bancos, financieras y organismos del estado',
				),
			),
		),
		'mca_why'      => array(
			'title'       => __( 'Por qué trabajar con nosotros', 'mca' ),
			'edit_post_type' => 'mca_reason',
			'edit_label'     => __( 'Editar los motivos', 'mca' ),
			'description' => __( 'Los tres motivos se editan en «MCA · Por qué nosotros».', 'mca' ),
			'fields'      => array(
				'why_title' => array(
					'label'   => __( 'Título del panel', 'mca' ),
					'type'    => 'textarea',
					'default' => '¿Por qué elegirnos como su aliado estratégico?',
				),
				'why_image' => array(
					'label'       => __( 'Imagen de fondo', 'mca' ),
					'type'        => 'image',
					'description' => __( 'Ocupa toda la franja, con un velo azul encima. Usa una foto apaisada y ancha (2000×600 o más). Cuanto más clara sea, menos se verá bajo el velo: el velo no se puede bajar sin perder legibilidad del texto.', 'mca' ),
				),
			),
		),
		'mca_method'   => array(
			'title'       => __( 'Metodología', 'mca' ),
			'edit_post_type' => 'mca_step',
			'edit_label'     => __( 'Editar los pasos', 'mca' ),
			'description' => __( 'Los pasos se editan en «MCA · Metodología».', 'mca' ),
			'fields'      => array(
				'method_title' => array(
					'label'   => __( 'Título de la sección', 'mca' ),
					'type'    => 'textarea',
					'default' => 'Ciclo virtuoso, te ofrecemos',
				),
				'method_step_prefix' => array(
					'label'       => __( 'Palabra antes del número', 'mca' ),
					'default'     => 'Paso',
					'description' => __( 'Se muestra como «Paso 1», «Paso 2»… El número lo pone el orden de la lista.', 'mca' ),
				),
			),
		),
		'mca_blog'     => array(
			'title'       => __( 'Blog', 'mca' ),
			'description' => __( 'Se muestran las tres entradas más recientes. Sin entradas publicadas, la sección no aparece.', 'mca' ),
			'fields'      => array(
				'blog_title' => array(
					'label'   => __( 'Título de la sección', 'mca' ),
					'type'    => 'textarea',
					'default' => 'Blog / artículos destacados',
				),
				'blog_link_label' => array(
					'label'   => __( 'Texto del enlace de cada tarjeta', 'mca' ),
					'default' => 'Leer artículo',
				),
			),
		),
		'mca_cta'      => array(
			'title'  => __( 'Llamada a la acción final', 'mca' ),
			'fields' => array(
				'cta_title'     => array(
					'label'   => __( 'Título', 'mca' ),
					'type'    => 'textarea',
					'default' => 'Compártenos tu necesidad y hablemos de soluciones con resultados tangibles.',
				),
				'cta_text'      => array(
					'label'   => __( 'Párrafo', 'mca' ),
					'type'    => 'textarea',
					'default' => 'Agenda una reunión con nuestro equipo comercial y revisemos cómo aplicar 17 años de experiencia financiera a tu operación.',
				),
				'cta_btn_label' => array(
					'label'       => __( 'Texto del botón', 'mca' ),
					'default'     => 'Solicitar reunión comercial',
					'description' => __( 'Este botón abre el formulario en una ventana modal.', 'mca' ),
				),
				'cta_btn2_label' => array(
					'label'   => __( 'Texto del botón de WhatsApp', 'mca' ),
					'default' => 'Hablar por Whatsapp',
				),
			),
		),
		'mca_modal'    => array(
			'title'       => __( 'Formulario de reunión', 'mca' ),
			'description' => __( 'La ventana que abre el botón «Solicitar reunión comercial». Los nombres de los campos no se editan aquí: definen qué datos se recogen.', 'mca' ),
			'fields'      => array(
				'modal_eyebrow'   => array(
					'label'   => __( 'Antetítulo', 'mca' ),
					'default' => 'Reunión comercial',
				),
				'modal_title'     => array(
					'label'   => __( 'Título', 'mca' ),
					'default' => 'Cuéntanos qué necesitas',
				),
				'modal_lead'      => array(
					'label'   => __( 'Texto introductorio', 'mca' ),
					'type'    => 'textarea',
					'default' => 'Déjanos tus datos y un consultor te contacta para agendar la reunión.',
				),
				'modal_submit'    => array(
					'label'   => __( 'Texto del botón de envío', 'mca' ),
					'default' => 'Enviar solicitud',
				),
				'modal_cancel'    => array(
					'label'   => __( 'Texto del botón de cancelar', 'mca' ),
					'default' => 'Cancelar',
				),
			),
		),
		'mca_contacto' => array(
			'title'       => __( 'Contacto', 'mca' ),
			'description' => __( 'Datos de contacto y destinos de los botones de llamada a la acción.', 'mca' ),
			'fields'      => array(
				'phone'         => array(
					'label'       => __( 'Teléfono', 'mca' ),
					'default'     => '+51 980565176',
					'description' => __( 'Se muestra tal cual lo escribas. El enlace para llamar se genera quitando todo lo que no sea dígito, así que puedes usar espacios o guiones sin romperlo.', 'mca' ),
				),
				'email'         => array(
					'label'    => __( 'Correo', 'mca' ),
					'default'  => 'infocomercial@mastercenter.pe',
					'sanitize' => 'sanitize_email',
				),
				'whatsapp'      => array(
					'label'       => __( 'Enlace de WhatsApp', 'mca' ),
					'type'        => 'url',
					'default'     => 'https://wa.me/51980565176',
					'description' => __( 'Solo el número, formato https://wa.me/51XXXXXXXXX (sin espacios ni signo +). El mensaje va en el campo de abajo, no aquí. Alimenta los dos botones «Hablar por Whatsapp».', 'mca' ),
				),
				'whatsapp_message' => array(
					'label'       => __( 'Mensaje con el que llega el chat', 'mca' ),
					'type'        => 'textarea',
					'default'     => 'Hola, quisiera conversar sobre los servicios de Master Center Américas.',
					'description' => __( 'Se escribe solo en el chat de quien pulsa el botón; aún puede editarlo antes de enviarlo. Déjalo vacío para abrir el chat en blanco.', 'mca' ),
				),
				'cta_url'       => array(
					'label'       => __( 'Destino de «Agenda tu reunión»', 'mca' ),
					'default'     => '#contacto',
					'description' => __( 'Un ancla como #contacto, o una URL completa.', 'mca' ),
				),
				'linkedin'      => array(
					'label'   => __( 'LinkedIn', 'mca' ),
					'type'    => 'url',
					'default' => '',
				),
				'facebook'      => array(
					'label'   => __( 'Facebook', 'mca' ),
					'type'    => 'url',
					'default' => '',
				),
				'footer_contact_title' => array(
					'label'   => __( 'Título del bloque de contacto en el pie', 'mca' ),
					'default' => 'Contáctanos',
				),
				'footer_blurb'  => array(
					'label'   => __( 'Texto del pie', 'mca' ),
					'type'    => 'textarea',
					'default' => '17 años convirtiendo contacto en resultados para la banca peruana.',
				),
				'form_endpoint' => array(
					'label'       => __( 'Destino del formulario', 'mca' ),
					'type'        => 'url',
					'default'     => '',
					'description' => __( 'URL que recibe la solicitud de reunión. Mientras esté vacío, el formulario avisa que no está conectado en lugar de simular un envío.', 'mca' ),
				),
			),
		),
	);
}

/**
 * Read-only control: a button that jumps to the admin screen of a collection.
 *
 * The repeatable content of five sections lives in custom post types, which the
 * Customizer cannot edit. Without this link the section shows a lone title
 * field and reads as if the rest of the fields were missing.
 */
function mca_define_link_control() {
	if ( class_exists( 'MCA_Customize_Link_Control' ) || ! class_exists( 'WP_Customize_Control' ) ) {
		return;
	}

	/**
	 * Renders an outbound link instead of an input.
	 */
	class MCA_Customize_Link_Control extends WP_Customize_Control {
		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'mca_link';

		/**
		 * Destination URL.
		 *
		 * @var string
		 */
		public $url = '';

		/**
		 * Button caption.
		 *
		 * @var string
		 */
		public $button = '';

		/**
		 * Print the control.
		 */
		public function render_content() {
			echo '<p style="margin:0 0 8px">' . esc_html( $this->label ) . '</p>';
			printf(
				'<a class="button button-primary" href="%s" target="_blank" rel="noopener">%s</a>',
				esc_url( $this->url ),
				esc_html( $this->button )
			);
			if ( $this->description ) {
				echo '<p class="description" style="margin-top:8px">' . esc_html( $this->description ) . '</p>';
			}
		}
	}
}

/**
 * Sanitiser for a field definition.
 *
 * @param array $field Field definition.
 * @return string Callable name.
 */
function mca_field_sanitizer( $field ) {
	if ( ! empty( $field['sanitize'] ) ) {
		return $field['sanitize'];
	}
	$type = isset( $field['type'] ) ? $field['type'] : 'text';
	if ( 'image' === $type ) {
		return 'absint';
	}
	if ( 'url' === $type ) {
		return 'esc_url_raw';
	}
	if ( 'textarea' === $type ) {
		return 'sanitize_textarea_field';
	}
	return 'sanitize_text_field';
}

/**
 * Register the content panel and every section in the schema.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function mca_customize_register( $wp_customize ) {
	mca_define_link_control();

	$wp_customize->add_panel(
		'mca_content',
		array(
			'title'       => __( 'MCA — Contenido', 'mca' ),
			'priority'    => 25,
			'description' => __( 'Textos e imágenes de la landing. Las listas repetibles (servicios, cifras, pasos, motivos y clientes) se editan desde el menú lateral, no aquí.', 'mca' ),
		)
	);

	$priority = 10;

	foreach ( mca_customizer_schema() as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'       => $section['title'],
				'panel'       => 'mca_content',
				'priority'    => $priority,
				'description' => isset( $section['description'] ) ? $section['description'] : '',
			)
		);
		$priority += 10;

		if ( ! empty( $section['edit_post_type'] ) ) {
			$wp_customize->add_control(
				new MCA_Customize_Link_Control(
					$wp_customize,
					$section_id . '_edit_link',
					array(
						'section'     => $section_id,
						'settings'    => array(),
						'priority'    => 1,
						'label'       => __( 'Esta lista no se edita aquí: cada elemento es una entrada propia.', 'mca' ),
						'button'      => $section['edit_label'],
						'url'         => admin_url( 'edit.php?post_type=' . $section['edit_post_type'] ),
						'description' => __( 'Se abre en una pestaña nueva. El orden se controla con el campo «Orden» de la caja Atributos.', 'mca' ),
					)
				)
			);
		}

		foreach ( $section['fields'] as $key => $field ) {
			$setting_id = 'mca_' . $key;
			$type        = isset( $field['type'] ) ? $field['type'] : 'text';

			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => isset( $field['default'] ) ? $field['default'] : '',
					'sanitize_callback' => mca_field_sanitizer( $field ),
					'transport'         => 'refresh',
				)
			);

			if ( 'image' === $type ) {
				$wp_customize->add_control(
					new WP_Customize_Media_Control(
						$wp_customize,
						$setting_id,
						array(
							'label'       => $field['label'],
							'section'     => $section_id,
							'mime_type'   => 'image',
							'description' => isset( $field['description'] ) ? $field['description'] : '',
						)
					)
				);
				continue;
			}

			$wp_customize->add_control(
				$setting_id,
				array(
					'label'       => $field['label'],
					'section'     => $section_id,
					'type'        => ( 'url' === $type ) ? 'url' : $type,
					'description' => isset( $field['description'] ) ? $field['description'] : '',
				)
			);
		}
	}
}
add_action( 'customize_register', 'mca_customize_register' );
