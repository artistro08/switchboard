<?php
/**
 * Template Name: Kickstarter Landing
 * Description: A custom template for the Kickstarter landing page.
 */

get_header("kickstarter");

if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		the_content();
	endwhile;
endif;

get_footer("kickstarter");
