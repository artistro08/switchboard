<?php
if( have_rows('gallery') ): ?>
    <div class="advanced-carousel">
        <div class="advanced-carousel-container">
            <div class="splide image-carousel">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php while( have_rows('gallery') ) : the_row(); ?>
                            <?php $image = get_sub_field('image'); ?>
                            <li class="splide__slide">
                                <div class="main-image-container">
                                    <img class="main-image" src="<?= $image['sizes']['large']; ?>" alt="<?= $image['alt']; ?>">
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
            <div class="splide thumbnail-carousel">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php while( have_rows('gallery') ) : the_row(); ?>
                            <?php $image = get_sub_field('image'); ?>
                            <li class="splide__slide">
                                <div class="thumbnail-names-and-dates d-lg-none">
                                    <h2><?php the_sub_field('title'); ?></h2>
                                    <h3><?php echo esc_html(get_sub_field('date')); ?></h3>
                                </div>
                                <img class="thumbnail-image" src="<?= $image['sizes']['large']; ?>" alt="<?= $image['alt']; ?>">
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="advanced-carousel-content-container">
            <div class="splide content-carousel">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php while( have_rows('gallery') ) : the_row(); ?>
                            <?php $image = get_sub_field('image'); ?>
                            <li class="splide__slide">
                                <div class="content">
                                    <img class="content-image d-lg-none" src="<?= $image['sizes']['large']; ?>" alt="<?= $image['alt']; ?>">
                                    <h2><?php the_sub_field('title') ?></h2>
                                    <h3><?php echo esc_html(get_sub_field('date')); ?></h3>
                                    <?php the_sub_field('content') ?>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
            <button class="btn btn-close" id="advanced-carousel-close-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?>