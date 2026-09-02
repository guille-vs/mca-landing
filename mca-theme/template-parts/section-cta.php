<?php
/**
 * Section: cta
 *
 * Copy comes from Personalizar > MCA — Contenido > Llamada a la acción final.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

$mca_cta_text  = mca_option( 'cta_text' );
$mca_cta_label = mca_option( 'cta_btn_label' );
$mca_whatsapp  = mca_whatsapp_url();
?>
  <!-- ========================================================================
       10 — CTA final
       ======================================================================== -->
  <section class="cta" id="contacto">
    <div class="cta__shoulder" aria-hidden="true">
      <svg viewBox="0 0 1920 125" preserveAspectRatio="none">
        <path d="M0 125C480 55 1200 90 1920 0v125z" fill="#16273e"/>
      </svg>
    </div>

    <div class="container cta__inner">
      <div>
        <h2 class="cta__title" data-reveal><?php echo esc_html( mca_option( 'cta_title' ) ); ?></h2>
        <?php if ( '' !== $mca_cta_text ) : ?>
        <p class="cta__text" data-reveal style="--reveal-delay:100ms">
          <?php echo esc_html( $mca_cta_text ); ?>
        </p>
        <?php endif; ?>
      </div>

      <div class="cta__actions" data-reveal style="--reveal-delay:180ms">
        <div class="cta__primary">
          <a class="btn btn--primary" href="#modal-reunion" data-modal-open><?php echo esc_html( $mca_cta_label ); ?></a>
          <a class="btn-arrow" href="#modal-reunion" data-modal-open aria-label="<?php echo esc_attr( $mca_cta_label ); ?>">
            <svg class="icon-svg" aria-hidden="true"><use href="#i-arrow-right"></use></svg>
          </a>
        </div>
        <?php if ( '' !== $mca_whatsapp ) : ?>
        <a class="btn btn--ghost" href="<?php echo esc_url( $mca_whatsapp ); ?>" target="_blank" rel="noopener">
          <?php echo esc_html( mca_option( 'cta_btn2_label' ) ); ?>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </section>
