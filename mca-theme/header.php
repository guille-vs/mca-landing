<?php
/**
 * Header: document head, skip link, icon sprite and the site navigation.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">

<?php
/*
 * The stylesheets are enqueued in functions.php, in dependency order. The font
 * preload has to be printed here rather than enqueued: `crossorigin` is
 * mandatory even same-origin, or the preload is discarded and the file
 * downloaded twice.
 */
?>
<link rel="preload" href="<?php echo esc_url( get_theme_file_uri( 'assets/fonts/manrope-variable.woff2' ) ); ?>" as="font" type="font/woff2" crossorigin>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#contenido"><?php esc_html_e( 'Saltar al contenido', 'mca' ); ?></a>

<?php get_template_part( 'template-parts/icon-sprite' ); ?>

<header class="site-header" id="site-header" data-js="header">
  <div class="container site-header__inner">
    <a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%s, ir al inicio', 'mca' ), get_bloginfo( 'name' ) ) ); ?>">
      <?php if ( has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
      <?php else : ?>
        <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/brand/logo-mca.png' ) ); ?>"
             alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="1785" height="257">
      <?php endif; ?>
    </a>

    <button class="nav-toggle" type="button" data-js="nav-toggle"
            aria-expanded="false" aria-controls="nav-primary" aria-label="<?php esc_attr_e( 'Abrir menú', 'mca' ); ?>">
      <span class="nav-toggle__bars" aria-hidden="true"></span>
    </button>

    <nav class="nav" id="nav-primary" data-js="nav" aria-label="<?php esc_attr_e( 'Navegación principal', 'mca' ); ?>">
      <?php
      wp_nav_menu(
        array(
          'theme_location' => 'primary',
          'container'      => false,
          'items_wrap'     => '%3$s',
          'depth'          => 1,
          'fallback_cb'    => 'mca_nav_fallback',
          'link_before'    => '',
        )
      );
      ?>
      <?php
      $cta_url = mca_option( 'cta_url' );
      if ( strpos( $cta_url, '#' ) === 0 && ! is_front_page() ) {
        $cta_url = home_url( '/' . $cta_url );
      }
      ?>
      <a class="btn btn--primary" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( mca_option( 'header_cta_label' ) ); ?></a>
    </nav>
  </div>
</header>


<main id="contenido">
