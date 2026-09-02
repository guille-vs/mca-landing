<?php
/**
 * One blog card. Used by the front page carousel and by the archive grid.
 *
 * Falls back to the design's placeholder image when a post has no featured
 * image, so a card never renders with an empty media box.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'post' ); ?> data-reveal>
  <div class="post__media">
    <?php if ( has_post_thumbnail() ) : ?>
      <?php the_post_thumbnail( 'mca-post-card', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); ?>
    <?php else : ?>
      <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/photos/blog-thumb.jpg' ) ); ?>"
           alt="" loading="lazy" decoding="async">
    <?php endif; ?>
  </div>
  <div class="post__body">
    <h3 class="post__title"><?php the_title(); ?></h3>
    <a class="link-arrow post__link" href="<?php the_permalink(); ?>">
      <?php echo esc_html( mca_option( 'blog_link_label' ) ); ?>
      <span class="visually-hidden"><?php the_title_attribute(); ?></span>
      <svg class="icon-svg" aria-hidden="true"><use href="#i-arrow-right"></use></svg>
    </a>
  </div>
</article>
