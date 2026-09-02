<?php
/**
 * Section: about
 *
 * Copy comes from Personalizar > MCA — Contenido > Quiénes somos.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

$mca_about_1 = mca_option( 'about_text_1' );
$mca_about_2 = mca_option( 'about_text_2' );
$mca_about_3 = mca_option( 'about_text_3' );
?>
  <!-- ========================================================================
       04 — Quiénes somos
       ======================================================================== -->
  <section class="section about" id="nosotros">
    <div class="container about__inner">
      <div data-reveal>
        <span class="eyebrow"><?php echo esc_html( mca_option( 'about_eyebrow' ) ); ?></span>
        <h2 class="about__title"><?php echo esc_html( mca_option( 'about_title' ) ); ?></h2>
      </div>

      <div class="about__body" data-reveal style="--reveal-delay:120ms">
        <?php if ( '' !== $mca_about_1 ) : ?>
        <p><?php echo esc_html( $mca_about_1 ); ?></p>
        <?php endif; ?>
        <?php if ( '' !== $mca_about_2 ) : ?>
        <p><?php echo esc_html( $mca_about_2 ); ?></p>
        <?php endif; ?>
        <?php if ( '' !== $mca_about_3 ) : ?>
        <p><?php echo esc_html( $mca_about_3 ); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </section>
