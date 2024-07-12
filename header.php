<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
use Tribe\Events\Views\V2\Template_Bootstrap;

$bootstrap_version  = get_theme_mod('understrap_bootstrap_version', 'bootstrap4');
$navbar_type        = get_theme_mod('understrap_navbar_type', 'collapse');

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?> <?php understrap_body_attributes(); ?> data-barba="wrapper">
        <?php do_action('wp_body_open'); ?>

        <!-- ******************* The Navbar Area ******************* -->
        <header id="wrapper-navbar" class="navbar-header">

            <a class="skip-link <?php echo understrap_get_screen_reader_class(true); ?>" href="#content">
                <?php esc_html_e('Skip to content', 'understrap'); ?>
            </a>

            <?php get_template_part('global-templates/navbar', $navbar_type . '-' . $bootstrap_version); ?>

        </header><!-- #wrapper-navbar -->
        <main id="main-container" 
            <?php if (! is_front_page()) {
                echo 'class="panel-open"';
            } ?> 
            data-barba="container"
            <?php if (is_front_page()) {
                echo 'data-barba-namespace="default"';
            } else {
                if (is_home()) {
                    echo 'data-barba-namespace="open-panel"';
                } elseif (isset($post) && switchboard_is_standalone_panel($post->ID)) {
                    echo 'data-barba-namespace="standalone-open-panel"'; 
                } else {
                    echo 'data-barba-namespace="open-panel"'; 
                }
            } ?>
            onclick=""
        >
            