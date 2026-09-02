<?php
/**
 * 404.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="section">
  <div class="container">
    <header class="section-head">
      <span class="eyebrow">404</span>
      <h1><?php esc_html_e( 'No encontramos esa página', 'mca' ); ?></h1>
      <p class="lead"><?php esc_html_e( 'El enlace puede estar roto o la página se movió.', 'mca' ); ?></p>
    </header>
    <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Volver al inicio', 'mca' ); ?></a>
  </div>
</section>

<?php
get_footer();
