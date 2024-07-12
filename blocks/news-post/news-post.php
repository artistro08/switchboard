<?php
$sort      = get_field("sort");
$args      = array(
    'post_type'      => 'news_post',
    'posts_per_page' => -1,
    'order'          => $sort,
    'meta_key'       => 'date',
    'orderby'        => 'meta_value',
);
$news_posts = new WP_Query($args); ?>

<?php if ($news_posts->have_posts()) : ?>
    <div class="row">
        <?php while ($news_posts->have_posts()) : $news_posts->the_post(); ?>
            <a 
                class="col-12 news-post-link" 
                href="<?php echo esc_attr(get_field('url', get_the_ID())); ?>"
                target="_blank"
            >
                <div class="row ">
                    <?php if (has_post_thumbnail(get_the_ID())) : ?>
                        <div class="col-2 col-lg-3">
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="<?php the_title(); ?>"
                                class="media-kit-image">
                        </div>
                    <?php endif; ?>
                    <div class="col-10 col-lg-9">
                        <h4 class="news-post-title fs-2 mb-0 lh-1"><?php the_title(); ?></h4>
                        <h5 class="news-post-date fs-4 mb-0"><?php the_field('author', get_the_ID()); ?>,&nbsp;<?php the_field('date', get_the_ID()); ?></h5>
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>

    <?php wp_reset_postdata(); ?>

<?php endif; ?>