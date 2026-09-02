<?php
/**
 * Section: services
 *
 * Cards come from the mca_service post type; the heading from
 * Personalizar > MCA — Contenido > Soluciones.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

$mca_services = mca_items( 'mca_service' );

if ( ! $mca_services ) {
	return;
}
?>
  <!-- ========================================================================
       05 — Servicios
       ======================================================================== -->
  <section class="section services" id="servicios">
    <div class="container">
      <header class="section-head" data-reveal>
        <h2><?php echo esc_html( mca_option( 'services_title' ) ); ?></h2>
      </header>

      <div class="carousel services__carousel" data-carousel aria-roledescription="<?php esc_attr_e( 'carrusel', 'mca' ); ?>" aria-label="<?php esc_attr_e( 'Soluciones de gestión y servicio', 'mca' ); ?>">
        <ul class="carousel__viewport services__grid" tabindex="0" aria-label="<?php esc_attr_e( 'Soluciones de gestión y servicio', 'mca' ); ?>">
          <?php foreach ( $mca_services as $mca_index => $mca_service ) : ?>
          <li class="service" data-reveal<?php mca_reveal_delay( $mca_index % 3 ); ?>>
            <span class="icon-badge service__badge" aria-hidden="true">
              <span class="icon icon--<?php echo esc_attr( mca_icon_slug( mca_meta( $mca_service->ID, 'icon' ) ) ); ?>" aria-hidden="true"></span>
            </span>
            <h3 class="service__title"><?php echo esc_html( get_the_title( $mca_service ) ); ?></h3>
            <p class="service__text"><?php echo esc_html( mca_meta( $mca_service->ID, 'text', '' ) ); ?></p>
          </li>
          <?php endforeach; ?>
        </ul>
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
