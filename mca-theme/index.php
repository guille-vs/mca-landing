<?php
/**
 * Fallback template. WordPress requires it; it also serves the blog archive.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="section blog">
  <div class="container">
    <header class="section-head">
      <h1><?php echo esc_html( mca_archive_title() ); ?></h1>
    </header>

    <?php if ( have_posts() ) : ?>
      <div class="blog__grid">
        <?php
        while ( have_posts() ) :
          the_post();
          get_template_part( 'template-parts/post-card' );
        endwhile;
        ?>
      </div>

      <?php
      the_posts_pagination(
        array(
          'mid_size'  => 1,
          'prev_text' => esc_html__( 'Anterior', 'mca' ),
          'next_text' => esc_html__( 'Siguiente', 'mca' ),
        )
      );
      ?>
    <?php else : ?>
      <p><?php esc_html_e( 'No hay artículos publicados todavía.', 'mca' ); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php
get_footer();
