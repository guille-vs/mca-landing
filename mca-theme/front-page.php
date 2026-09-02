<?php
/**
 * Front page: the landing, assembled from one part per section.
 *
 * The order here is the order the design specifies; each part is
 * self-contained so a section can be reordered or dropped from this file
 * alone.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;

get_header();

foreach ( array( 'hero', 'stats', 'about', 'services', 'clients', 'why', 'method', 'blog', 'cta' ) as $mca_section ) {
	get_template_part( 'template-parts/section', $mca_section );
}

get_footer();
