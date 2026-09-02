<?php
/**
 * Meeting request custom post type and REST API endpoint.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the custom post type for meeting requests.
 */
function mca_register_meetings_cpt() {
	register_post_type( 'solicitud-reunion', array(
		'labels'       => array(
			'name'               => __( 'Solicitudes de Reunión', 'mca' ),
			'singular_name'      => __( 'Solicitud de Reunión', 'mca' ),
			'add_new_item'       => __( 'Agregar nueva solicitud', 'mca' ),
			'edit_item'          => __( 'Editar solicitud', 'mca' ),
			'view_item'          => __( 'Ver solicitud', 'mca' ),
			'search_items'       => __( 'Buscar solicitudes', 'mca' ),
			'not_found'          => __( 'No se encontraron solicitudes', 'mca' ),
			'not_found_in_trash' => __( 'No hay solicitudes en la papelera', 'mca' ),
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'show_in_rest' => false,
		'menu_icon'    => 'dashicons-calendar-alt',
		'supports'     => array( 'title' ),
		'capability_type' => 'post',
		'map_meta_cap'    => true,
	) );
}
add_action( 'init', 'mca_register_meetings_cpt' );

/**
 * Add custom columns to the meetings list table.
 */
function mca_meetings_columns( $columns ) {
	$columns['email']    = __( 'Correo', 'mca' );
	$columns['phone']    = __( 'Celular', 'mca' );
	$columns['company']  = __( 'Empresa', 'mca' );
	$columns['message']  = __( 'Requerimiento', 'mca' );
	$columns['date']     = __( 'Fecha', 'mca' );
	return $columns;
}
add_filter( 'manage_solicitud-reunion_posts_columns', 'mca_meetings_columns' );

/**
 * Render custom column content.
 */
function mca_meetings_column( $column, $post_id ) {
	switch ( $column ) {
		case 'email':
			echo esc_html( get_post_meta( $post_id, '_mca_meeting_email', true ) );
			break;
		case 'phone':
			echo esc_html( get_post_meta( $post_id, '_mca_meeting_phone', true ) );
			break;
		case 'company':
			echo esc_html( get_post_meta( $post_id, '_mca_meeting_company', true ) );
			break;
		case 'message':
			$mensaje = get_post_meta( $post_id, '_mca_meeting_message', true );
			echo esc_html( wp_trim_words( $mensaje, 12, '…' ) );
			break;
		case 'date':
			echo esc_html( get_the_date( 'd/m/Y H:i', $post_id ) );
			break;
	}
}
add_action( 'manage_solicitud-reunion_posts_custom_column', 'mca_meetings_column', 10, 2 );

/**
 * Make custom columns sortable.
 */
function mca_meetings_sortable( $columns ) {
	$columns['email']   = 'email';
	$columns['company'] = 'company';
	return $columns;
}
add_filter( 'manage_edit-solicitud-reunion_sortable_columns', 'mca_meetings_sortable' );

/**
 * Handle the REST API submission.
 */
function mca_handle_meeting_submission( $request ) {
	$nombre   = sanitize_text_field( $request->get_param( 'nombre' ) );
	$correo   = sanitize_email( $request->get_param( 'correo' ) );
	$celular  = sanitize_text_field( $request->get_param( 'celular' ) );
	$empresa  = sanitize_text_field( $request->get_param( 'empresa' ) );
	$mensaje  = sanitize_textarea_field( $request->get_param( 'mensaje' ) );

	// Validate required fields.
	$errors = array();
	if ( empty( $nombre ) ) {
		$errors[] = __( 'El nombre es obligatorio.', 'mca' );
	}
	if ( empty( $correo ) || ! is_email( $correo ) ) {
		$errors[] = __( 'El correo no es válido.', 'mca' );
	}
	if ( empty( $celular ) ) {
		$errors[] = __( 'El celular es obligatorio.', 'mca' );
	}
	if ( empty( $empresa ) ) {
		$errors[] = __( 'La empresa es obligatoria.', 'mca' );
	}
	if ( empty( $mensaje ) ) {
		$errors[] = __( 'El mensaje es obligatorio.', 'mca' );
	}

	if ( ! empty( $errors ) ) {
		return new WP_REST_Response( array(
			'success' => false,
			'message' => implode( ' ', $errors ),
		), 400 );
	}

	// Create the post.
	$post_id = wp_insert_post( array(
		'post_type'   => 'solicitud-reunion',
		'post_status' => 'publish',
		'post_title'  => sprintf( '%s — %s', $nombre, $empresa ),
	) );

	if ( is_wp_error( $post_id ) ) {
		return new WP_REST_Response( array(
			'success' => false,
			'message' => __( 'Error al guardar la solicitud.', 'mca' ),
		), 500 );
	}

	// Save meta fields.
	update_post_meta( $post_id, '_mca_meeting_name', $nombre );
	update_post_meta( $post_id, '_mca_meeting_email', $correo );
	update_post_meta( $post_id, '_mca_meeting_phone', $celular );
	update_post_meta( $post_id, '_mca_meeting_company', $empresa );
	update_post_meta( $post_id, '_mca_meeting_message', $mensaje );

	// Send notification email directly.
	// Use ignore_user_abort so the REST response returns even if
	// the email send takes a few seconds.
	$notification_data = array(
		'nombre'  => $nombre,
		'correo'  => $correo,
		'celular' => $celular,
		'empresa' => $empresa,
		'mensaje' => $mensaje,
		'post_id' => $post_id,
	);

	// Send email synchronously — SMTP with SSL on port 465 is fast.
	mca_send_meeting_notification( $notification_data );

	return new WP_REST_Response( array(
		'success' => true,
		'message' => __( '¡Solicitud enviada correctamente!', 'mca' ),
	), 200 );
}

/**
 * Register the REST API route.
 */
function mca_register_meeting_endpoint() {
	register_rest_route( 'mca/v1', '/meeting', array(
		'methods'             => 'POST',
		'callback'            => 'mca_handle_meeting_submission',
		'permission_callback' => '__return_true', // Public endpoint.
		'args'                => array(
			'nombre'  => array( 'required' => true, 'type' => 'string' ),
			'correo'  => array( 'required' => true, 'type' => 'string' ),
			'celular' => array( 'required' => true, 'type' => 'string' ),
			'empresa' => array( 'required' => true, 'type' => 'string' ),
			'mensaje' => array( 'required' => true, 'type' => 'string' ),
		),
	) );
}
add_action( 'rest_api_init', 'mca_register_meeting_endpoint' );

/**
 * Send notification email when a new meeting request is received.
 *
 * @param array $data {
 *     Meeting request data.
 *     @type string $nombre  Contact name.
 *     @type string $correo  Contact email.
 *     @type string $celular Contact phone.
 *     @type string $empresa Company name.
 *     @type string $mensaje Message body.
 *     @type int    $post_id Created post ID.
 * }
 */
function mca_send_meeting_notification( $data ) {
	// Get receivers
	$receivers = get_option( 'mca_meeting_receivers', array( get_option( 'admin_email' ) ) );
	if ( ! is_array( $receivers ) ) {
		$receivers = array_filter( array_map( 'trim', explode( "\n", $receivers ) ) );
	}
	$receivers = array_filter( array_map( 'sanitize_email', $receivers ) );
	
	if ( empty( $receivers ) ) {
		return; // No valid receivers configured.
	}
	
	$to = implode( ', ', $receivers );
	
	// Get sender settings
	$from_name = get_option( 'mca_meeting_from_name', get_bloginfo( 'name' ) );
	$from_email = get_option( 'mca_meeting_from_email', get_option( 'admin_email' ) );
	$subject_prefix = get_option( 'mca_meeting_subject_prefix', 'Nueva solicitud de reunión' );
	
	$subject = sprintf( '%s — %s', $subject_prefix, $data['empresa'] );
	
	$message = sprintf(
		__( "Nueva solicitud de reunión comercial:\n\n", 'mca' )
		. __( "Nombre: %s\n", 'mca' )
		. __( "Correo: %s\n", 'mca' )
		. __( "Celular: %s\n", 'mca' )
		. __( "Empresa: %s\n\n", 'mca' )
		. __( "Requerimiento:\n%s\n\n", 'mca' )
		. __( "Ver en admin: %s", 'mca' ),
		$data['nombre'],
		$data['correo'],
		$data['celular'],
		$data['empresa'],
		$data['mensaje'],
		admin_url( 'post.php?post=' . $data['post_id'] . '&action=edit' )
	);
	
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'From: ' . $from_name . ' <' . $from_email . '>',
	);
	
	// Configure SMTP if enabled
	if ( get_option( 'mca_meeting_smtp_enabled', '0' ) === '1' ) {
		add_action( 'phpmailer_init', 'mca_phpmailer_smtp_config' );
	}
	
	$sent = wp_mail( $to, $subject, $message, $headers );
	
	remove_action( 'phpmailer_init', 'mca_phpmailer_smtp_config' );
	
	if ( ! $sent ) {
		error_log( '[MCA] Email notification failed for post #' . $data['post_id'] . ' to: ' . $to );
	}
}

/**
 * PHPMailer SMTP configuration callback.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer instance.
 */
function mca_phpmailer_smtp_config( $phpmailer ) {
	$phpmailer->isSMTP();
	$phpmailer->Host       = get_option( 'mca_meeting_smtp_host', '' );
	$phpmailer->Port       = (int) get_option( 'mca_meeting_smtp_port', '465' );
	$phpmailer->SMTPSecure = get_option( 'mca_meeting_smtp_secure', 'ssl' );
	$phpmailer->SMTPAuth   = get_option( 'mca_meeting_smtp_auth', '1' ) === '1';
	$phpmailer->Username   = get_option( 'mca_meeting_smtp_user', '' );
	$phpmailer->Password   = get_option( 'mca_meeting_smtp_pass', '' );
	$phpmailer->CharSet    = 'UTF-8';
}

/**
 * AJAX handler for SMTP test.
 */
function mca_test_smtp_handler() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'No tienes permisos.', 'mca' ) ) );
	}
	
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'mca_test_smtp_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Token de seguridad inválido.', 'mca' ) ) );
	}
	
	$to = get_option( 'mca_meeting_from_email', get_option( 'admin_email' ) );
	$from_name = get_option( 'mca_meeting_from_name', get_bloginfo( 'name' ) );
	$from_email = get_option( 'mca_meeting_from_email', get_option( 'admin_email' ) );
	
	$subject = __( 'Email de prueba — Configuración SMTP', 'mca' );
	$message = __( "¡Este es un email de prueba!\n\nSi lo estás leyendo, la configuración SMTP está funcionando correctamente.\n\nEnviado desde: ", 'mca' ) . get_bloginfo( 'name' );
	
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'From: ' . $from_name . ' <' . $from_email . '>',
	);
	
	// Configure SMTP if enabled
	if ( get_option( 'mca_meeting_smtp_enabled', '0' ) === '1' ) {
		mca_configure_smtp();
	}
	
	$sent = wp_mail( $to, $subject, $message, $headers );
	
	// Remove SMTP config action after test
	remove_action( 'phpmailer_init', 'mca_phpmailer_smtp_config' );
	
	if ( $sent ) {
		wp_send_json_success( array( 'message' => sprintf( __( 'Email enviado correctamente a %s', 'mca' ), $to ) ) );
	} else {
		wp_send_json_error( array( 'message' => __( 'Error al enviar el email. Verifica la configuración SMTP.', 'mca' ) ) );
	}
}
add_action( 'wp_ajax_mca_test_smtp', 'mca_test_smtp_handler' );

/**
 * Add metabox to show meeting request details.
 */
function mca_meetings_add_metabox() {
	add_meta_box(
		'mca_meeting_details',
		__( 'Datos de la solicitud', 'mca' ),
		'mca_meetings_metabox_html',
		'solicitud-reunion',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'mca_meetings_add_metabox' );

/**
 * Add settings submenu under "Solicitudes de Reunión".
 */
function mca_meetings_menu() {
	add_submenu_page(
		'edit.php?post_type=solicitud-reunion',
		__( 'Configuración de notificaciones', 'mca' ),
		__( 'Configuración', 'mca' ),
		'manage_options',
		'mca-meeting-settings',
		'mca_meetings_settings_page'
	);
}
add_action( 'admin_menu', 'mca_meetings_menu' );

/**
 * Register settings.
 */
function mca_meetings_settings_init() {
	// General settings
	register_setting( 'mca_meeting_settings_general', 'mca_meeting_from_name' );
	register_setting( 'mca_meeting_settings_general', 'mca_meeting_from_email' );
	register_setting( 'mca_meeting_settings_general', 'mca_meeting_subject_prefix' );
	
	// Receiver emails
	register_setting( 'mca_meeting_settings_receivers', 'mca_meeting_receivers' );
	
	// SMTP settings
	register_setting( 'mca_meeting_settings_smtp', 'mca_meeting_smtp_enabled' );
	register_setting( 'mca_meeting_settings_smtp', 'mca_meeting_smtp_host' );
	register_setting( 'mca_meeting_settings_smtp', 'mca_meeting_smtp_port' );
	register_setting( 'mca_meeting_settings_smtp', 'mca_meeting_smtp_auth' );
	register_setting( 'mca_meeting_settings_smtp', 'mca_meeting_smtp_user' );
	register_setting( 'mca_meeting_settings_smtp', 'mca_meeting_smtp_pass' );
	register_setting( 'mca_meeting_settings_smtp', 'mca_meeting_smtp_secure' );
}
add_action( 'admin_init', 'mca_meetings_settings_init' );

/**
 * Get the active tab.
 */
function mca_meetings_get_tab() {
	return isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'receivers';
}

/**
 * Render the settings page with tabs.
 */
function mca_meetings_settings_page() {
	$active_tab = mca_meetings_get_tab();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Configuración de notificaciones', 'mca' ); ?></h1>
		
		<nav class="nav-tab-wrapper">
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=solicitud-reunion&page=mca-meeting-settings&tab=receivers' ) ); ?>" 
			   class="nav-tab <?php echo $active_tab === 'receivers' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Correos receptores', 'mca' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=solicitud-reunion&page=mca-meeting-settings&tab=general' ) ); ?>" 
			   class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'General', 'mca' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=solicitud-reunion&page=mca-meeting-settings&tab=smtp' ) ); ?>" 
			   class="nav-tab <?php echo $active_tab === 'smtp' ? 'nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'SMTP', 'mca' ); ?>
			</a>
		</nav>
		
		<div class="tab-content" style="margin-top: 20px;">
			<?php
			switch ( $active_tab ) {
				case 'general':
					mca_meetings_settings_general();
					break;
				case 'smtp':
					mca_meetings_settings_smtp();
					break;
				case 'receivers':
				default:
					mca_meetings_settings_receivers();
					break;
			}
			?>
		</div>
	</div>
	<?php
}

/**
 * Render the receivers settings tab.
 */
function mca_meetings_settings_receivers() {
	$receivers = get_option( 'mca_meeting_receivers', array( get_option( 'admin_email' ) ) );
	if ( ! is_array( $receivers ) ) {
		$receivers = array( $receivers );
	}
	?>
	<form method="post" action="options.php">
		<?php settings_fields( 'mca_meeting_settings_receivers' ); ?>
		
		<h2><?php esc_html_e( 'Correos receptores', 'mca' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Configura los correos que recibirán una notificación cuando se reciba una nueva solicitud de reunión. Puedes agregar múltiples correos separados por coma.', 'mca' ); ?>
		</p>
		
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="mca_meeting_receivers"><?php esc_html_e( 'Correos electrónicos', 'mca' ); ?></label>
				</th>
				<td>
					<textarea id="mca_meeting_receivers" 
							  name="mca_meeting_receivers" 
							  rows="4" 
							  class="large-text"
							  placeholder="correo1@ejemplo.com, correo2@ejemplo.com"><?php echo esc_textarea( implode( "\n", $receivers ) ); ?></textarea>
					<p class="description">
						<?php esc_html_e( 'Un correo por línea, o separados por coma. Si lo dejas vacío, se usará el email de administrador de WordPress.', 'mca' ); ?>
					</p>
				</td>
			</tr>
		</table>
		
		<?php submit_button(); ?>
	</form>
	
	<hr>
	
	<h3><?php esc_html_e( 'Vista previa del email', 'mca' ); ?></h3>
	<div style="background: #f9f9f9; border: 1px solid #ddd; padding: 20px; border-radius: 4px; max-width: 600px;">
		<p><strong><?php esc_html_e( 'Para:', 'mca' ); ?></strong> <?php echo esc_html( implode( ', ', $receivers ) ); ?></p>
		<p><strong><?php esc_html_e( 'Asunto:', 'mca' ); ?></strong> <?php esc_html_e( 'Nueva solicitud de reunión — [Empresa]', 'mca' ); ?></p>
		<hr style="border: 0; border-top: 1px solid #ddd; margin: 10px 0;">
		<p><strong><?php esc_html_e( 'Nombre:', 'mca' ); ?></strong> Juan Pérez</p>
		<p><strong><?php esc_html_e( 'Correo:', 'mca' ); ?></strong> juan@empresa.com</p>
		<p><strong><?php esc_html_e( 'Celular:', 'mca' ); ?></strong> +51 999 888 777</p>
		<p><strong><?php esc_html_e( 'Empresa:', 'mca' ); ?></strong> Mi Empresa SAC</p>
		<p><strong><?php esc_html_e( 'Requerimiento:', 'mca' ); ?></strong></p>
		<p style="background: #fff; padding: 10px; border: 1px solid #eee;"><?php esc_html_e( 'Necesitamos contratar un servicio de contact center...', 'mca' ); ?></p>
	</div>
	<?php
}

/**
 * Render the general settings tab.
 */
function mca_meetings_settings_general() {
	$from_name = get_option( 'mca_meeting_from_name', get_bloginfo( 'name' ) );
	$from_email = get_option( 'mca_meeting_from_email', get_option( 'admin_email' ) );
	$subject_prefix = get_option( 'mca_meeting_subject_prefix', 'Nueva solicitud de reunión' );
	?>
	<form method="post" action="options.php">
		<?php settings_fields( 'mca_meeting_settings_general' ); ?>
		
		<h2><?php esc_html_e( 'Configuración general', 'mca' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Configura el remitente y el asunto de los emails de notificación.', 'mca' ); ?>
		</p>
		
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="mca_meeting_from_name"><?php esc_html_e( 'Nombre del remitente', 'mca' ); ?></label>
				</th>
				<td>
					<input type="text" 
						   id="mca_meeting_from_name" 
						   name="mca_meeting_from_name" 
						   value="<?php echo esc_attr( $from_name ); ?>" 
						   class="regular-text">
					<p class="description">
						<?php esc_html_e( 'Nombre que aparece como remitente del email.', 'mca' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="mca_meeting_from_email"><?php esc_html_e( 'Email del remitente', 'mca' ); ?></label>
				</th>
				<td>
					<input type="email" 
						   id="mca_meeting_from_email" 
						   name="mca_meeting_from_email" 
						   value="<?php echo esc_attr( $from_email ); ?>" 
						   class="regular-text">
					<p class="description">
						<?php esc_html_e( 'Email que aparece como remitente. Debe ser un correo válido del dominio.', 'mca' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="mca_meeting_subject_prefix"><?php esc_html_e( 'Prefijo del asunto', 'mca' ); ?></label>
				</th>
				<td>
					<input type="text" 
						   id="mca_meeting_subject_prefix" 
						   name="mca_meeting_subject_prefix" 
						   value="<?php echo esc_attr( $subject_prefix ); ?>" 
						   class="regular-text">
					<p class="description">
						<?php esc_html_e( 'Texto que aparece al inicio del asunto del email. Ej: "Nueva solicitud de reunión".', 'mca' ); ?>
					</p>
				</td>
			</tr>
		</table>
		
		<?php submit_button(); ?>
	</form>
	<?php
}

/**
 * Render the SMTP settings tab.
 */
function mca_meetings_settings_smtp() {
	$smtp_enabled = get_option( 'mca_meeting_smtp_enabled', '0' );
	$smtp_host = get_option( 'mca_meeting_smtp_host', '' );
	$smtp_port = get_option( 'mca_meeting_smtp_port', '587' );
	$smtp_auth = get_option( 'mca_meeting_smtp_auth', '1' );
	$smtp_user = get_option( 'mca_meeting_smtp_user', '' );
	$smtp_pass = get_option( 'mca_meeting_smtp_pass', '' );
	$smtp_secure = get_option( 'mca_meeting_smtp_secure', 'tls' );
	?>
	<form method="post" action="options.php">
		<?php settings_fields( 'mca_meeting_settings_smtp' ); ?>
		
		<h2><?php esc_html_e( 'Configuración SMTP', 'mca' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Configura un servidor SMTP para el envío de notificaciones. Si lo dejas deshabilitado, se usará el envío nativo de WordPress (puede ir a spam).', 'mca' ); ?>
		</p>
		
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="mca_meeting_smtp_enabled"><?php esc_html_e( 'Habilitar SMTP', 'mca' ); ?></label>
				</th>
				<td>
					<label>
						<input type="checkbox" 
							   id="mca_meeting_smtp_enabled" 
							   name="mca_meeting_smtp_enabled" 
							   value="1" 
							   <?php checked( $smtp_enabled, '1' ); ?>>
						<?php esc_html_e( 'Usar servidor SMTP para el envío de emails', 'mca' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="mca_meeting_smtp_host"><?php esc_html_e( 'Servidor SMTP', 'mca' ); ?></label>
				</th>
				<td>
					<input type="text" 
						   id="mca_meeting_smtp_host" 
						   name="mca_meeting_smtp_host" 
						   value="<?php echo esc_attr( $smtp_host ); ?>" 
						   class="regular-text"
						   placeholder="smtp.gmail.com">
					<p class="description">
						<?php esc_html_e( 'Ejemplos: smtp.gmail.com, smtp.office365.com, smtp.sendgrid.net', 'mca' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="mca_meeting_smtp_port"><?php esc_html_e( 'Puerto', 'mca' ); ?></label>
				</th>
				<td>
					<input type="number" 
						   id="mca_meeting_smtp_port" 
						   name="mca_meeting_smtp_port" 
						   value="<?php echo esc_attr( $smtp_port ); ?>" 
						   class="small-text"
						   placeholder="587">
					<p class="description">
						<?php esc_html_e( 'Puertos comunes: 587 (TLS), 465 (SSL), 25 (sin cifrar - no recomendado)', 'mca' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="mca_meeting_smtp_secure"><?php esc_html_e( 'Cifrado', 'mca' ); ?></label>
				</th>
				<td>
					<select id="mca_meeting_smtp_secure" name="mca_meeting_smtp_secure">
						<option value="tls" <?php selected( $smtp_secure, 'tls' ); ?>><?php esc_html_e( 'TLS', 'mca' ); ?></option>
						<option value="ssl" <?php selected( $smtp_secure, 'ssl' ); ?>><?php esc_html_e( 'SSL', 'mca' ); ?></option>
						<option value="none" <?php selected( $smtp_secure, 'none' ); ?>><?php esc_html_e( 'Ninguno', 'mca' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="mca_meeting_smtp_auth"><?php esc_html_e( 'Autenticación', 'mca' ); ?></label>
				</th>
				<td>
					<label>
						<input type="checkbox" 
							   id="mca_meeting_smtp_auth" 
							   name="mca_meeting_smtp_auth" 
							   value="1" 
							   <?php checked( $smtp_auth, '1' ); ?>>
						<?php esc_html_e( 'Requiere autenticación (usuario y contraseña)', 'mca' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="mca_meeting_smtp_user"><?php esc_html_e( 'Usuario SMTP', 'mca' ); ?></label>
				</th>
				<td>
					<input type="text" 
						   id="mca_meeting_smtp_user" 
						   name="mca_meeting_smtp_user" 
						   value="<?php echo esc_attr( $smtp_user ); ?>" 
						   class="regular-text"
						   placeholder="tu@correo.com">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="mca_meeting_smtp_pass"><?php esc_html_e( 'Contraseña SMTP', 'mca' ); ?></label>
				</th>
				<td>
					<input type="password" 
						   id="mca_meeting_smtp_pass" 
						   name="mca_meeting_smtp_pass" 
						   value="<?php echo esc_attr( $smtp_pass ); ?>" 
						   class="regular-text"
						   autocomplete="off">
					<p class="description">
						<?php esc_html_e( 'Para Gmail usa una Contraseña de aplicación, no tu contraseña normal.', 'mca' ); ?>
					</p>
				</td>
			</tr>
		</table>
		
		<?php submit_button(); ?>
	</form>
	
	<hr>
	
	<h3><?php esc_html_e( 'Prueba de envío', 'mca' ); ?></h3>
	<p><?php esc_html_e( 'Envía un email de prueba para verificar que la configuración funciona correctamente.', 'mca' ); ?></p>
	
	<?php
	// Process test email if submitted
	if ( isset( $_POST['mca_send_test_email'] ) && check_admin_referer( 'mca_test_email_action' ) ) {
		$to = get_option( 'mca_meeting_from_email', get_option( 'admin_email' ) );
		$from_name = get_option( 'mca_meeting_from_name', get_bloginfo( 'name' ) );
		$from_email = get_option( 'mca_meeting_from_email', get_option( 'admin_email' ) );
		
		$subject = __( 'Email de prueba — Configuración SMTP', 'mca' );
		$message = __( "¡Este es un email de prueba!\n\nSi lo estás leyendo, la configuración está funcionando correctamente.\n\nEnviado desde: ", 'mca' ) . get_bloginfo( 'name' );
		
		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'From: ' . $from_name . ' <' . $from_email . '>',
		);
		
	// Configure SMTP if enabled
	$smtp_enabled = get_option( 'mca_meeting_smtp_enabled', '0' );
	error_log( '[MCA] smtp_enabled = ' . $smtp_enabled );
	if ( $smtp_enabled === '1' ) {
		add_action( 'phpmailer_init', 'mca_phpmailer_smtp_config' );
		error_log( '[MCA] phpmailer_init hook registered' );
	}
	
	$sent = wp_mail( $to, $subject, $message, $headers );
	
	// Always remove to avoid affecting other emails.
	remove_action( 'phpmailer_init', 'mca_phpmailer_smtp_config' );
		
		// Remove SMTP config action after test
		remove_action( 'phpmailer_init', 'mca_phpmailer_smtp_config' );
		
		if ( $sent ) {
			echo '<div class="notice notice-success"><p>' . esc_html( sprintf( __( 'Email enviado correctamente a %s', 'mca' ), $to ) ) . '</p></div>';
		} else {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Error al enviar el email. Verifica la configuración SMTP.', 'mca' ) . '</p></div>';
		}
	}
	?>
	
	<form method="post">
		<?php wp_nonce_field( 'mca_test_email_action' ); ?>
		<p>
			<input type="hidden" name="mca_send_test_email" value="1">
			<button type="submit" class="button button-secondary">
				<?php esc_html_e( 'Enviar email de prueba', 'mca' ); ?>
			</button>
		</p>
	</form>
	<?php
}

/**
 * Render the metabox content.
 */
function mca_meetings_metabox_html( $post ) {
	$name    = get_post_meta( $post->ID, '_mca_meeting_name', true );
	$email   = get_post_meta( $post->ID, '_mca_meeting_email', true );
	$phone   = get_post_meta( $post->ID, '_mca_meeting_phone', true );
	$company = get_post_meta( $post->ID, '_mca_meeting_company', true );
	$message = get_post_meta( $post->ID, '_mca_meeting_message', true );
	?>
	<style>
		.mca-details-table { width: 100%; border-collapse: collapse; }
		.mca-details-table th,
		.mca-details-table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
		.mca-details-table th { width: 140px; font-weight: 600; color: #1e293b; }
		.mca-details-table td { color: #334155; }
		.mca-details-message { white-space: pre-wrap; line-height: 1.6; }
	</style>
	<table class="mca-details-table">
		<tr>
			<th><?php esc_html_e( 'Nombre', 'mca' ); ?></th>
			<td><?php echo esc_html( $name ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Correo', 'mca' ); ?></th>
			<td><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Celular', 'mca' ); ?></th>
			<td><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Empresa', 'mca' ); ?></th>
			<td><?php echo esc_html( $company ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Requerimiento', 'mca' ); ?></th>
			<td class="mca-details-message"><?php echo esc_textarea( $message ); ?></td>
		</tr>
	</table>
	<?php
}
