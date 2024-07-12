<?php
/**
 * Header Navbar (bootstrap5)
 *
 * @package Understrap
 * @since 1.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
?>
<nav id="main-nav" class="navbar navbar-expand-lg" aria-labelledby="main-nav-label">

    <h2 id="main-nav-label" class="screen-reader-text">
        <?php esc_html_e( 'Main Navigation', 'understrap' ); ?>
    </h2>


    <div class="<?php echo esc_attr( $container ); ?>">

        <!-- Your site branding in the menu -->
        <?php get_template_part( 'global-templates/navbar-branding' ); ?>

        <div class="nav-links-container">
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown"
                aria-expanded="false"
                aria-label="<?php esc_attr_e( 'Toggle navigation', 'understrap' ); ?>"
            >
                <span class="visually-hidden">Toggle navigation</span>
                <img alt="Toggle navigation" class="toggler-icon" src="<?php echo get_stylesheet_directory_uri() . '/assets/img/navbar-toggler.svg' ?>">
            </button>

            <!-- The social menu goes here -->
            <?php
            wp_nav_menu(
                array(
                    'theme_location'  => 'social_areas',
                    'container'       => false,
                    'menu_class'      => 'navbar-nav split-navigation top',
                    'fallback_cb'     => '',
                    'menu_id'         => 'social-menu',
                    'depth'           => 1,
                    'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
                )
            );
            ?>

            <!-- The WordPress Menu goes here -->
            <?php
            wp_nav_menu(
                array(
                    'theme_location'  => 'primary',
                    'container_class' => 'collapse navbar-collapse split-navigation bottom',
                    'container_id'    => 'navbarNavDropdown',
                    'menu_class'      => 'navbar-nav ms-auto',
                    'fallback_cb'     => '',
                    'menu_id'         => 'main-menu',
                    'depth'           => 2,
                    'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
                )
            );
            ?>
            <img class="header-svg" src="<?php echo get_stylesheet_directory_uri() . '/assets/img/header-top-background.svg' ?>" alt="">
            <div class="header-gradient-left"></div>
            <div class="header-gradient-bottom"></div>
        </div>


    </div><!-- .container(-fluid) -->

</nav><!-- #main-nav -->
