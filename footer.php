<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
?>
<?php // Closing main from header.php. ?>
<img class="home-page-overlay d-lg-none" src="<?php echo get_stylesheet_directory_uri() . '/assets/img/home-page-overlay.svg' ?>" alt="<?php echo get_bloginfo('name'); ?>">
</main>
<?php if (class_exists('acf')) : ?>
    <?php if (get_field('show_floating_button', 'option')): ?>
        <div class="floating-button">
            <button class="floating-button-link" id="floating-button-link" data-bs-toggle="collapse" href="#floatingButtonContent" role="button" aria-expanded="false" aria-controls="floatingButtonContent">
                <?php the_field('floating_button_title', 'option'); ?>
            </button>
            <div class="collapse floating-button-content shadow-lg" id="floatingButtonContent">
                <div class="content">
                    <img class="floating-button-content-image" src="<?php echo get_stylesheet_directory_uri() . '/assets/img/switchboard-form-header.svg' ?>" alt="<?php echo get_bloginfo('name'); ?>">
                    <div><p class="fs-3"><?php the_field('floating_button_content', 'option'); ?></p></div>
                    <?php echo do_shortcode(get_field('floating_button_shortcode', 'option')); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<footer>
    <!-- The social menu goes here -->
    <?php
    wp_nav_menu(
        array(
            'theme_location'  => 'social_areas',
            'container'       => false,
            'menu_class'      => 'nav split-navigation footer',
            'fallback_cb'     => '',
            'menu_id'         => 'social-footer-menu',
            'depth'           => 1,
            'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
        )
    );
    ?>

    <img alt="" class="footer-background" src="<?php echo get_stylesheet_directory_uri() . '/assets/img/header-top-background.svg' ?>">
</footer>
<?php wp_footer(); ?>


</body>

</html>

