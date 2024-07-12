<?php
/**
 * Post rendering content according to caller of get_template_part
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article class="mb-3" <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">
		<div class="row align-items-center">
			<?php if (has_post_thumbnail(get_the_ID())) : ?>
				<div class="col-2 col-lg-3">
					<a href="<?php echo get_the_permalink() ?>">
						<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="<?php the_title(); ?>" class="post-image">
					</a>
				</div>
			<?php endif; ?>
			<div class="col-10 col-lg-9">
				<?php the_title(
					sprintf('<h2 class="entry-title fs-2 mb-0 lh-1"><a class="text-decoration-none" href="%s" rel="bookmark">', esc_url(get_permalink())),
					'</a></h2>'
				); ?>
				<?php if ( 'post' === get_post_type() ) : ?>
					<h5 class="post-date fs-4 mb-0"><?php understrap_posted_on(); ?></h5>
				<?php endif; ?>
			</div>
		</div>
	</header><!-- .entry-header -->



</article><!-- #post-<?php the_ID(); ?> -->
