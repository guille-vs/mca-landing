<?php
/**
 * Section: method
 *
 * Steps come from the mca_step post type; the heading from
 * Personalizar > MCA — Contenido > Metodología.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

$mca_steps = mca_items( 'mca_step' );

if ( ! $mca_steps ) {
	return;
}
?>
    <!-- ========================================================================
         08 — Metodología
         ======================================================================== -->
    <section class="section method" id="metodologia">
      <div class="container">
        <header class="section-head" data-reveal>
          <h2><?php echo esc_html( mca_option( 'method_title' ) ); ?></h2>
        </header>

        <div class="carousel method__carousel" data-carousel aria-roledescription="<?php esc_attr_e( 'carrusel', 'mca' ); ?>" aria-label="<?php esc_attr_e( 'Pasos de la metodología', 'mca' ); ?>">
          <ol class="carousel__viewport method__steps" tabindex="0" aria-label="<?php esc_attr_e( 'Pasos de la metodología', 'mca' ); ?>">
            <?php foreach ( $mca_steps as $mca_index => $mca_step ) : ?>
            <li class="method__step" data-reveal<?php mca_reveal_delay( $mca_index ); ?>>
              <span class="icon-badge icon-badge--gradient" aria-hidden="true">
                <span class="icon icon--<?php echo esc_attr( mca_icon_slug( mca_meta( $mca_step->ID, 'icon', 'search' ) ) ); ?>" aria-hidden="true"></span>
              </span>
              <?php // The word is editable, the number always follows the list order. ?>
              <p class="method__step-no"><?php echo esc_html( trim( mca_option( 'method_step_prefix' ) . ' ' . ( (int) $mca_index + 1 ) ) ); ?></p>
              <p class="method__step-label"><?php echo esc_html( get_the_title( $mca_step ) ); ?></p>
            </li>
            <?php endforeach; ?>
          </ol>
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
