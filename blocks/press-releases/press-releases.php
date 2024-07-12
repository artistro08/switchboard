<?php
$sort      = get_field("sort");
$args      = array(
    'post_type'      => 'press_release',
    'posts_per_page' => -1,
    'order'          => $sort,
    'orderby'        => 'date',
);
$press_releases = new WP_Query($args); ?>

<?php if ($press_releases->have_posts()) : ?>
    <div class="row align-items-center">
        <?php while ($press_releases->have_posts()) : $press_releases->the_post(); ?>
            <a 
                class="col-12 press-release-link" 
                href="<?php echo get_the_permalink(); ?>"
            >
                <div class="col-12">
                    <h4 class="press-release-title fs-2 mb-0 lh-1"><?php the_title(); ?></h4>
                    <h5 class="press-release-date fs-4 mb-0"><?php echo get_the_date(); ?></h5>
                </div>
            </a>
        <?php endwhile; ?>
    </div>

    <?php wp_reset_postdata(); ?>

<?php endif; ?>