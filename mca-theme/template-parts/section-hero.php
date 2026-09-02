<?php
/**
 * Section: hero
 *
 * Copy and images come from Personalizar > MCA — Contenido > Portada.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

$mca_hero_text = mca_option( 'hero_text' );
$mca_whatsapp  = mca_whatsapp_url();
?>
  <!-- ========================================================================
       02 — Hero
       ======================================================================== -->
  <section class="hero" id="inicio">
    <div class="container hero__inner">
      <div class="hero__copy">
        <h1 class="hero__title" data-reveal><?php echo esc_html( mca_option( 'hero_title' ) ); ?></h1>

        <?php if ( '' !== $mca_hero_text ) : ?>
        <p class="hero__text lead" data-reveal style="--reveal-delay:90ms">
          <?php echo esc_html( $mca_hero_text ); ?>
        </p>
        <?php endif; ?>

        <div class="hero__actions" data-reveal style="--reveal-delay:180ms">
          <a class="btn btn--primary" href="<?php echo esc_url( mca_option( 'hero_btn_url' ) ); ?>"><?php echo esc_html( mca_option( 'hero_btn_label' ) ); ?></a>
          <?php if ( '' !== $mca_whatsapp ) : ?>
          <a class="btn btn--ghost" href="<?php echo esc_url( $mca_whatsapp ); ?>" target="_blank" rel="noopener">
            <?php echo esc_html( mca_option( 'hero_btn2_label' ) ); ?>
          </a>
          <?php endif; ?>
        </div>
      </div>

      <figure class="hero__visual" data-reveal style="--reveal-delay:240ms">
        <img src="<?php echo esc_url( mca_image_url( 'hero_image', 'assets/img/photos/laptop-analytics.png' ) ); ?>"
             alt="<?php echo esc_attr( mca_image_alt( 'hero_image', __( 'Panel de Business Analytics de Master Center Américas mostrando indicadores en vivo', 'mca' ) ) ); ?>"
             width="1200" height="762" fetchpriority="high" decoding="async">
      </figure>
    </div>

    <img class="hero__ribbon" src="<?php echo esc_url( mca_image_url( 'hero_ribbon', 'assets/img/photos/hero-ribbon.png' ) ); ?>"
         alt="" width="1600" height="910" aria-hidden="true" decoding="async">

    <!-- Curve traced from the PDF: ~125 units of rise across the 1920 canvas. -->
    <div class="wave wave--bottom" aria-hidden="true">
      <svg viewBox="0 0 1920 125" preserveAspectRatio="none">
        <path d="M0 125C480 55 1200 90 1920 0v125z" fill="#ffffff"/>
      </svg>
    </div>
  </section>
