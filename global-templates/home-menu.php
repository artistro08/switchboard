<?php

/**
 * Home Animated Menu Setup
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
$index = 0;

?>

<?php foreach (menu_items('homepage') as $menu_item) : ?>

    <?php if(!$menu_item->menu_item_parent) :?>
        <?php
            $panel_image = get_stylesheet_directory_uri() . '/assets/img/panels/panel_' . $index . '.svg';
        ?>


        <section class="panel">
            <a id="<?php echo 'section-' . slugify($menu_item->title) ?>" class="panel-image-and-title-container" href="<?php echo esc_url($menu_item->url); ?>">
                <img alt="<?php echo esc_url($menu_item->post_title); ?>" class="panel-image" src="<?php echo $panel_image; ?>">
                <h3 class="panel-title"><?php echo $menu_item->title; ?></h3>
            </a>
            <div class="panel-content"></div>
        </section>

        <?php
            $index++;
            if ($index == 5) {
                $index = 0;
            }
        ?>
    <?php endif; ?>


<?php endforeach; ?>
<section class="standalone-panel" id="standalone-container"></section>


