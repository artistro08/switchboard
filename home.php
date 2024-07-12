<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
$id               = (int)get_option('page_for_posts');
$index            = 0;
$is_standalone    = true;
$menu_item_parent = 0;

get_header(); ?>

<?php if (is_front_page()) : ?>
    <?php get_template_part('global-templates/home-menu'); ?>
<?php else : ?>
    <?php 
        // Get the child menu items for comparison
        $child_items = [];
        foreach (menu_items('homepage') as $menu_item) {
            if ($menu_item->menu_item_parent) {
                $child_items[] = (int)$menu_item->object_id;
            }
        } 
    ?>
    <?php 
        // Get the parent menu items for comparison
        $parent_items = [];
        foreach (menu_items('homepage') as $menu_item) {
            if (!$menu_item->menu_item_parent) {
                $parent_items[] = (int)$menu_item->ID;
            }
        } 
    ?>
    <?php foreach (menu_items('homepage') as $menu_item) : // Custom Loop through menu items ?>
        
        <?php if(!(int)$menu_item->menu_item_parent) : // Get top level menu items ?>
            
            <?php 
                $panel_image  = get_stylesheet_directory_uri() . '/assets/img/panels/panel_' . $index . '.svg'; // Get panel image. Loops through an index of 5
                $menu_post_id = (int)$menu_item->object_id; // Get the post ID of the menu item
                $has_parent   = in_array($id, $child_items); // Determine if item has a parent by checking if the item is in the array created earlier
                $is_child     = false; // Determine if item is a child by checking if the item is in the array created earlier
            ?>
            <?php if ($menu_post_id === $id || $has_parent) : // Get the post if ID matches the current page or the parent page ?>
				
                <?php foreach (menu_items('homepage') as $child) : // Check if the current menu item is a child of the current page ?>
                    <?php if ((int)$child->menu_item_parent === $menu_item->ID) : ?>
                        <?php foreach ($parent_items as $parent) : ?>
							
                            <?php if ((int)$parent === (int)$child->menu_item_parent) : ?>
                                <?php if ((int)$child->object_id === $id) : ?>
                                    <?php $is_child = true; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
				
                <?php if($is_child) : ?>
                    <?php $panel_image = get_stylesheet_directory_uri() . '/assets/img/panels/panel_' . $index . '.svg'; ?>
                    <section class="active panel">
                        <div id="<?php echo 'section-' . slugify($menu_item->title) ?>" class="panel-image-and-title-container">
                            <img alt="<?php echo esc_url($menu_item->post_title); ?>" class="panel-image" src="<?php echo $panel_image; ?>">
                            <h3 class="panel-title"><?php echo $menu_item->title; ?></h3>
                            <ul class="nav panel-menu flex-lg-column">
                                <?php foreach (menu_items('homepage') as $child) : ?>
                                    <?php if ((int)$child->menu_item_parent === $menu_item->ID) : ?>
                                        <li class="nav-item">
                                            <a class="nav-link <?php if ((int)$child->object_id === $id) {echo 'active';} ?>" href="<?php echo esc_url($child->url); ?>">
                                                <?php echo $child->title ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="panel-content">
                            <div class="panel-content-container">
								<h1 class="mb-0 lh-1">News</h1>
								<hr>
                                <?php
									if (have_posts()) {
										// Start the Loop.
										while (have_posts()) {
											the_post();

											/*
											 * Include the Post-Format-specific template for the content.
											 * If you want to override this in a child theme, then include a file
											 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
											 */
											get_template_part('loop-templates/content', get_post_format());
										}
									} else {
										get_template_part('loop-templates/content', 'none');
									}
									?>
                            </div>
                        </div>
                    </section>
                    <?php else : ?>
                        <?php $is_standalone = false; ?>
                    
                    <?php if(is_post_type_archive()) : ?>
                        <section class="active panel">
                            <div id="<?php echo 'section-' . slugify($menu_item->title) ?>" class="panel-image-and-title-container">
                                <img alt="<?php echo esc_url($menu_item->post_title); ?>" class="panel-image" src="<?php echo $panel_image; ?>">
                                <h3 class="panel-title"><?php echo $menu_item->title; ?></h3>
                                <ul class="nav panel-menu flex-lg-column">
                                    <?php foreach (menu_items('homepage') as $child) : ?>
                                        <?php if ((int)$child->menu_item_parent === $menu_item->ID) : ?>
                                            <li class="nav-item">
                                                <a class="nav-link <?php if ((int)$child->object_id === $id) {echo 'active';} ?>" href="<?php echo esc_url($child->url); ?>">
                                                    <?php echo $child->title ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="panel-content">
                                <div class="panel-content-container">
									<h1 class="mb-0 lh-1">News</h1>
									<hr>
                                    <?php
										if (have_posts()) {
											// Start the Loop.
											while (have_posts()) {
												the_post();

												/*
												 * Include the Post-Format-specific template for the content.
												 * If you want to override this in a child theme, then include a file
												 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
												 */
												get_template_part('loop-templates/content', get_post_format());
											}
										} else {
											get_template_part('loop-templates/content', 'none');
										}
										?>
                                </div>
                            </div>
                        </section>
                        
                        <?php $is_standalone = false; ?>
                        
                    <?php else : ?>
                        <section class="panel">
                            <a id="<?php echo 'section-' . slugify($menu_item->title) ?>" class="panel-image-and-title-container" href="<?php echo esc_url($menu_item->url); ?>">
                                <img alt="<?php echo esc_url($menu_item->post_title); ?>" class="panel-image" src="<?php echo $panel_image; ?>">
                                <h3 class="panel-title"><?php echo $menu_item->title; ?></h3>
                            </a>
                            <div class="panel-content"></div>
                        </section>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <?php foreach (menu_items('homepage') as $child) : // Check if the current menu item is a child of the current page ?>
                    <?php if ((int) $child->menu_item_parent === $menu_item->ID) : ?>
                        <?php foreach ($parent_items as $parent) : ?>
                            <?php if ((int) $parent === (int) $child->menu_item_parent) : ?>
                                <?php if ((int) $child->object_id === $id) : ?>
                                    <?php $is_child = true; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if($is_child) : ?>
                    <section class="active panel">
                        <div id="<?php echo 'section-' . slugify($menu_item->title) ?>" class="panel-image-and-title-container">
                            <img alt="<?php echo esc_url($menu_item->post_title); ?>" class="panel-image" src="<?php echo $panel_image; ?>">
                            <h3 class="panel-title"><?php echo $menu_item->title; ?></h3>
                            <ul class="nav panel-menu flex-lg-column">
                                <?php foreach (menu_items('homepage') as $child) : ?>
                                    <?php if ((int)$child->menu_item_parent === $menu_item->ID) : ?>
                                        <li class="nav-item">
                                            <a class="nav-link <?php if ((int)$child->object_id === $id) {echo 'active';} ?>" href="<?php echo esc_url($child->url); ?>">
                                                <?php echo $child->title ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="panel-content">
                            <div class="panel-content-container">
								<h1 class="mb-0 lh-1">News</h1>
								<hr>
                                <?php
									if (have_posts()) {
										// Start the Loop.
										while (have_posts()) {
											the_post();

											/*
											 * Include the Post-Format-specific template for the content.
											 * If you want to override this in a child theme, then include a file
											 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
											 */
											get_template_part('loop-templates/content', get_post_format());
										}
									} else {
										get_template_part('loop-templates/content', 'none');
									}
									?>
                            </div>
                        </div>
                    </section>
                    
                    <?php $is_standalone = false; ?>
                <?php else : ?>
                    <section class="panel">
                        <a id="<?php echo 'section-' . slugify($menu_item->title) ?>" class="panel-image-and-title-container" href="<?php echo esc_url($menu_item->url); ?>">
                            <img alt="<?php echo esc_url($menu_item->post_title); ?>" class="panel-image" src="<?php echo $panel_image; ?>">
                            <h3 class="panel-title"><?php echo $menu_item->title; ?></h3>
                        </a>
                        <div class="panel-content"></div>
                    </section>
                <?php endif; ?>
            <?php endif; ?>
            <?php
                // Used to set the index of the panel image
                $index++;
                if ($index == 5) {
                    $index = 0;
                }
                $child_items = []; // Reset the child items array to prevent other panels from being displayed
            ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($is_standalone) : ?>
        <section class="standalone-panel active" id="standalone-container">
            <div class="standalone-panel-content-container" id="standalone-panel-content-container">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-lg-8 mx-auto">
                            <h1 class="mb-0 lh-1">News</h1>
							<hr>
                            <?php
								if (have_posts()) {
									// Start the Loop.
									while (have_posts()) {
										the_post();

										/*
										 * Include the Post-Format-specific template for the content.
										 * If you want to override this in a child theme, then include a file
										 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
										 */
										get_template_part('loop-templates/content', get_post_format());
									}
								} else {
									get_template_part('loop-templates/content', 'none');
								}
								?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php else :  ?>
        <section class="standalone-panel" id="standalone-container"></section>
    <?php endif; ?>
<?php endif; ?>



<?php get_footer(); 
