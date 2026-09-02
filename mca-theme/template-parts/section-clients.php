<?php
/**
 * Section: clients
 *
 * Panels are the mca_sector terms, logos are mca_client posts. The heading
 * comes from Personalizar > MCA — Contenido > Clientes.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

$mca_panels = mca_client_panels();

if ( ! $mca_panels ) {
	return;
}

/**
 * Print one logo.
 *
 * The optical height rides inline because it is per-logo data, not styling: a
 * logo added by the editor has no stylesheet rule waiting for it.
 *
 * @param WP_Post $client Client post.
 */
$mca_logo = static function ( $client ) {
	$src = mca_client_logo_url( $client );
	if ( '' === $src ) {
		return;
	}
	$cap = mca_meta( $client->ID, 'cap', '' );

	printf(
		'<img data-logo="%1$s" src="%2$s" alt="%3$s"%4$s loading="lazy" decoding="async">',
		esc_attr( $client->post_name ),
		esc_url( $src ),
		esc_attr( get_the_title( $client ) ),
		'' === $cap ? '' : ' style="--cap:' . esc_attr( (int) $cap ) . 'px"'
	);
};
?>
  <!-- ========================================================================
       06 — Clientes
       ======================================================================== -->
  <section class="section clients" aria-label="<?php esc_attr_e( 'Clientes', 'mca' ); ?>">
    <div class="container clients__inner">
      <h2 class="clients__title" data-reveal><?php echo esc_html( mca_option( 'clients_title' ) ); ?></h2>

      <div class="carousel clients__carousel" data-carousel aria-roledescription="<?php esc_attr_e( 'carrusel', 'mca' ); ?>" aria-label="<?php esc_attr_e( 'Clientes por sector', 'mca' ); ?>">
        <div class="carousel__viewport clients__panels" tabindex="0" aria-label="<?php esc_attr_e( 'Clientes por sector', 'mca' ); ?>">
          <?php foreach ( $mca_panels as $mca_index => $mca_panel ) : ?>
          <article class="client-panel" data-reveal<?php mca_reveal_delay( $mca_index, 100 ); ?>>
            <span class="pill pill--<?php echo esc_attr( $mca_panel['pill'] ); ?> client-panel__label"><?php echo esc_html( $mca_panel['term']->name ); ?></span>
            <div class="client-panel__logos">
              <?php
              $mca_clients = $mca_panel['clients'];
              $mca_total   = count( $mca_clients );
              $mca_i       = 0;

              while ( $mca_i < $mca_total ) {
              	$mca_client = $mca_clients[ $mca_i ];
              	$mca_paired = '1' === mca_meta( $mca_client->ID, 'pair', '' ) && isset( $mca_clients[ $mca_i + 1 ] );

              	if ( $mca_paired ) {
              		echo '<div class="client-panel__row">';
              		$mca_logo( $mca_client );
              		$mca_logo( $mca_clients[ $mca_i + 1 ] );
              		echo '</div>';
              		$mca_i += 2;
              		continue;
              	}

              	$mca_logo( $mca_client );
              	$mca_i++;
              }
              ?>
            </div>
          </article>
          <?php endforeach; ?>
        </div>

        <div class="carousel__controls">
          <button class="carousel__btn carousel__btn--prev" type="button" data-carousel-prev aria-label="<?php esc_attr_e( 'Anterior', 'mca' ); ?>">
            <svg class="icon-svg" aria-hidden="true"><use href="#i-arrow-right"></use></svg>
          </button>
          <div class="carousel__dots" data-carousel-dots role="tablist" aria-label="<?php esc_attr_e( 'Seleccionar elemento', 'mca' ); ?>"></div>
          <button class="carousel__btn carousel__btn--next" type="button" data-carousel-next aria-label="<?php esc_attr_e( 'Siguiente', 'mca' ); ?>">
            <svg class="icon-svg" aria-hidden="true"><use href="#i-arrow-right"></use></svg>
          </button>
        </div>
      </div>
    </div>
  </section>
