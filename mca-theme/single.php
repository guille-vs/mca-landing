<?php
/**
 * Single post.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
  the_post();
  ?>
  <article class="section entry">
    <div class="container entry__inner">
      <header class="section-head">
        <span class="eyebrow"><?php echo esc_html( get_the_date() ); ?></span>
        <h1><?php the_title(); ?></h1>
      </header>

      <?php if ( has_post_thumbnail() ) : ?>
        <figure class="entry__media">
          <?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
        </figure>
      <?php endif; ?>

      <div class="entry__content">
        <?php the_content(); ?>
      </div>
    </div>
  </article>
  <?php
endwhile;

get_footer();
