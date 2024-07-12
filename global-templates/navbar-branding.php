<?php
/**
 * Navbar branding
 *
 * @package Understrap
 * @since 1.2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<?php if ( is_front_page() && is_home() ) : ?>
    <a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="url">
        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/switchboard-logo.svg' ?>" alt="The Game of Switchboard" class="logo">
    </a>

<?php else : ?>
    <a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="url">
        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/switchboard-logo.svg' ?>" alt="The Game of Switchboard" class="logo">
    </a>
<?php endif; ?>
