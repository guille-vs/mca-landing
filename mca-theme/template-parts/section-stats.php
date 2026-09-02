<?php
/**
 * Section: stats
 *
 * Cards come from the mca_stat post type; the photograph from
 * Personalizar > MCA — Contenido > Cifras.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

$mca_stats = mca_items( 'mca_stat' );
?>
  <!-- ========================================================================
       03 — Trust stats
       ======================================================================== -->
  <section class="stats" aria-label="<?php esc_attr_e( 'Cifras y certificaciones', 'mca' ); ?>">
    <div class="container">
      <div class="stats__stack">
        <?php if ( $mca_stats ) : ?>
        <div class="carousel stats__carousel" data-carousel aria-roledescription="<?php esc_attr_e( 'carrusel', 'mca' ); ?>" aria-label="<?php esc_attr_e( 'Cifras y certificaciones', 'mca' ); ?>">
          <ul class="carousel__viewport stats__grid" tabindex="0" aria-label="<?php esc_attr_e( 'Cifras y certificaciones', 'mca' ); ?>">
            <?php foreach ( $mca_stats as $mca_index => $mca_stat ) : ?>
              <?php
              $mca_value = get_the_title( $mca_stat );
              $mca_count = (string) mca_meta( $mca_stat->ID, 'count_to', '' );
              ?>
            <li class="stats__card" data-reveal<?php mca_reveal_delay( $mca_index ); ?>>
              <span class="icon icon--<?php echo esc_attr( mca_icon_slug( mca_meta( $mca_stat->ID, 'icon', 'star-check' ) ) ); ?> stats__icon" aria-hidden="true"></span>
              <p class="stats__value">
                <?php
                // The animated span only wraps the leading number, so "17+ años"
                // counts up on the 17 and keeps "+ años" static. When the title
                // does not start with that number the value prints untouched.
                if ( '' !== $mca_count && 0 === strpos( $mca_value, $mca_count ) ) {
                	printf(
                		'<span class="counter" data-count-to="%1$s">%1$s</span>%2$s',
                		esc_attr( $mca_count ),
                		esc_html( substr( $mca_value, strlen( $mca_count ) ) )
                	);
                } else {
                	echo esc_html( $mca_value );
                }
                ?>
              </p>
              <p class="stats__desc"><?php echo esc_html( mca_meta( $mca_stat->ID, 'text', '' ) ); ?></p>
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
        <?php endif; ?>

        <figure class="stats__photo" data-reveal>
          <img src="<?php echo esc_url( mca_image_url( 'stats_image', 'assets/img/photos/office.jpg' ) ); ?>"
               alt="<?php echo esc_attr( mca_image_alt( 'stats_image', __( 'Equipo de Master Center Américas trabajando en la plataforma de contact center', 'mca' ) ) ); ?>"
               width="2000" height="1335" loading="lazy" decoding="async">
        </figure>
      </div>
    </div>
  </section>
