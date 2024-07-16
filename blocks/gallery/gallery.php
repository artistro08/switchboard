<?php 
$images = get_field('gallery');
$gallery_id = generateRandomString(10);
if( $images ): ?>
    <div class="row">
        <?php foreach( $images as $image ): ?>
            <div class="col-12 col-lg-4">
                <a 
                    class="simple-gallery-image" 
                    href="<?php echo esc_url($image['url']); ?>" 
                    data-fancybox="<?= $gallery_id ?>"
                    data-fancybox-caption="<?php echo esc_html($image['caption']); ?>"
                    data-barba-prevent
                >
                     <img class="img-fluid" src="<?php echo esc_url($image['sizes']['large']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <div class="is_empty_container">
        <h1>There are no images here<br> <span class="fw-normal">Check back later</span></h1>
    </div>
<?php endif; ?>