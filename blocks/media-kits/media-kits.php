<?php
$sort      = get_field("sort");
$args      = array(
    'post_type'      => 'media_kit',
    'posts_per_page' => -1,
    'order'          => $sort,
    'orderby'        => 'title',
);
$media_kits = new WP_Query($args); ?>

<?php if ($media_kits->have_posts()) : ?>
    <div class="row align-items-center">
        <?php while ($media_kits->have_posts()) : $media_kits->the_post(); ?>
            <a 
                class="col-12 media-kit-link" 
                href="<?php the_field('pdf_file', get_the_ID()) ?>"
                data-barba-prevent="self"
                target="_blank"
            >
                <div class="row ">
                    <?php if (has_post_thumbnail(get_the_ID())) : ?>
                        <div class="col-2 col-lg-3">
                            <img 
                                src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'full'); ?>" 
                                alt="<?php the_title(); ?>" 
                                class="media-kit-image"
                            >
                        </div>
                    <?php endif; ?>
                    <div class="col-10 col-lg-9">
                        <h4 class="media-kit-title fs-2 mb-0 lh-1"><?php the_title(); ?></h4>
                        <h5 class="media-kit-date fs-4 mb-0"><?php the_field('date', get_the_ID()); ?></h5>
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>

    <?php wp_reset_postdata(); ?>

<?php endif; ?>