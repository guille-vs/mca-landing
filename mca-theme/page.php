<?php
/**
 * Single page.
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
        <h1><?php the_title(); ?></h1>
      </header>
      <div class="entry__content">
        <?php
        the_content();
        wp_link_pages( array( 'before' => '<nav class="entry__pages">', 'after' => '</nav>' ) );
        ?>
      </div>
    </div>
  </article>
  <?php
endwhile;

get_footer();
