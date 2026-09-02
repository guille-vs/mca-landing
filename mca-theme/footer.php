<?php
/**
 * Footer: contact details, social links, the meeting modal and wp_footer.
 *
 * Every value the client will want to change lives in the Customizer, under
 * "MCA — Contacto". See inc/customizer.php.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

$mca_phone     = mca_option( 'phone' );
$mca_email     = mca_option( 'email' );
$mca_linkedin  = mca_option( 'linkedin' );
$mca_facebook  = mca_option( 'facebook' );
?>
</main>


<footer class="site-footer">
  <div class="container">
    <div class="site-footer__top">
      <div class="site-footer__logo">
        <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/brand/logo-mca.png' ) ); ?>"
             alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="1785" height="257" loading="lazy">
      </div>

      <p class="site-footer__blurb"><?php echo esc_html( mca_option( 'footer_blurb' ) ); ?></p>

      <div>
        <h2 class="site-footer__heading"><?php echo esc_html( mca_option( 'footer_contact_title' ) ); ?></h2>
        <ul class="contact-list">
          <li>
            <a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^\d+]/', '', $mca_phone ) ); ?>">
              <span class="icon icon--phone" aria-hidden="true"></span>
              <?php echo esc_html( $mca_phone ); ?>
            </a>
          </li>
          <li>
            <a href="<?php echo esc_url( 'mailto:' . $mca_email ); ?>">
              <span class="icon icon--mail" aria-hidden="true"></span>
              <?php echo esc_html( $mca_email ); ?>
            </a>
          </li>
        </ul>
      </div>

      <?php if ( $mca_linkedin || $mca_facebook ) : ?>
        <div class="social">
          <?php if ( $mca_linkedin ) : ?>
            <a href="<?php echo esc_url( $mca_linkedin ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'LinkedIn de %s', 'mca' ), get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener">
              <span class="icon icon--linkedin" aria-hidden="true"></span>
            </a>
          <?php endif; ?>
          <?php if ( $mca_facebook ) : ?>
            <a href="<?php echo esc_url( $mca_facebook ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Facebook de %s', 'mca' ), get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener">
              <span class="icon icon--facebook" aria-hidden="true"></span>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <p class="site-footer__legal">
      <?php
      printf(
        /* translators: 1: year, 2: site name */
        esc_html__( '© %1$s %2$s', 'mca' ),
        esc_html( gmdate( 'Y' ) ),
        esc_html( get_bloginfo( 'name' ) )
      );
      ?>
    </p>
  </div>
</footer>

<?php get_template_part( 'template-parts/modal-reunion' ); ?>

<?php wp_footer(); ?>

<?php
/*
 * Floating WhatsApp button. Shares the number and greeting with the two
 * "Hablar por Whatsapp" buttons, so changing them in the Customizer moves all
 * three. It disappears when no link is configured.
 */
$mca_fab = mca_whatsapp_url();
if ( '' !== $mca_fab ) :
?>
<a class="whatsapp-fab"
   href="<?php echo esc_url( $mca_fab ); ?>"
   target="_blank"
   rel="noopener"
   aria-label="<?php esc_attr_e( 'Contactar por WhatsApp', 'mca' ); ?>">
  <svg class="whatsapp-fab__icon" aria-hidden="true" focusable="false"><use href="#i-whatsapp"></use></svg>
</a>
<?php endif; ?>

</body>
</html>
