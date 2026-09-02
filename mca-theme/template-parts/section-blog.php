<?php
/**
 * Section: blog — the three most recent posts.
 *
 * The whole section disappears when there is nothing published, rather than
 * rendering an empty carousel.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

$mca_posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $mca_posts->have_posts() ) {
	return;
}
?>
  <!-- ========================================================================
       09 — Blog
       ======================================================================== -->
  <section class="section blog" aria-label="<?php esc_attr_e( 'Blog y artículos destacados', 'mca' ); ?>">
    <div class="container">
      <header class="section-head" data-reveal>
        <h2><?php echo esc_html( mca_option( 'blog_title' ) ); ?></h2>
      </header>

      <div class="carousel blog__carousel" data-carousel aria-roledescription="<?php esc_attr_e( 'carrusel', 'mca' ); ?>" aria-label="<?php esc_attr_e( 'Artículos destacados', 'mca' ); ?>">
        <div class="carousel__viewport blog__grid" tabindex="0" aria-label="<?php esc_attr_e( 'Artículos destacados', 'mca' ); ?>">
          <?php
          while ( $mca_posts->have_posts() ) :
            $mca_posts->the_post();
            get_template_part( 'template-parts/post-card' );
          endwhile;
          wp_reset_postdata();
          ?>
        </div>

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
