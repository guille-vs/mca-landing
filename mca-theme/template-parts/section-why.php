<?php
/**
 * Section: why
 *
 * A full-bleed band: the photograph spans the viewport, a navy veil sits over
 * it, and the reasons — mca_reason posts — lay out in columns on top. Heading
 * and photo come from Personalizar > MCA — Contenido > Por qué trabajar con
 * nosotros.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

$mca_reasons = mca_items( 'mca_reason' );
?>
    <!-- ========================================================================
         07 — ¿Por qué elegirnos como su aliado estratégico?
         ======================================================================== -->
    <section class="why" id="por-que">
      <?php
      /*
       * Decorative: the veil over it makes any subject unreadable, so an empty
       * alt is correct and screen readers skip straight to the copy.
       */
      ?>
      <img class="why__bg"
           src="<?php echo esc_url( mca_image_url( 'why_image', 'assets/img/photos/why-agents.jpg' ) ); ?>"
           alt="" aria-hidden="true"
           width="2000" height="600" loading="lazy" decoding="async">

      <div class="container why__inner">
        <h2 class="why__title" data-reveal><?php echo esc_html( mca_option( 'why_title' ) ); ?></h2>

        <?php if ( $mca_reasons ) : ?>
        <ul class="why__list">
          <?php foreach ( $mca_reasons as $mca_index => $mca_reason ) : ?>
          <li class="why__item" data-reveal<?php mca_reveal_delay( $mca_index ); ?>>
            <h3 class="why__item-title"><?php echo esc_html( get_the_title( $mca_reason ) ); ?></h3>
            <p class="why__item-text"><?php echo esc_html( mca_meta( $mca_reason->ID, 'text', '' ) ); ?></p>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </section>
