<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;



/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts()
{
	wp_dequeue_style('understrap-styles');
	wp_deregister_style('understrap-styles');

	wp_dequeue_script('understrap-scripts');
	wp_deregister_script('understrap-scripts');
}
add_action('wp_enqueue_scripts', 'understrap_remove_scripts', 20);



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles()
{

	// Get the theme data.
	$the_theme = wp_get_theme();
	$custom_assets_version = '0.0.5';

	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles  = "/css/child-theme{$suffix}.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";

	wp_enqueue_style('child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $the_theme->get('Version'));
	wp_enqueue_style('bs-theme-overrides', get_stylesheet_directory_uri() . '/css/bs-theme-overrides.css', array(), $the_theme->get('Version'));
	wp_enqueue_style('futura-font', get_stylesheet_directory_uri() . '/css/futura-pt-condensed.css', array(), $the_theme->get('Version'));
	wp_enqueue_style('typekit', 'https://use.typekit.net/sky3sek.css');
	wp_enqueue_style('theme-styles', get_stylesheet_directory_uri() . '/css/styles.css', array(), $custom_assets_version);
	wp_enqueue_script('jquery');
	wp_enqueue_script('child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $the_theme->get('Version'), true);
	if (class_exists('acf')) {
		wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . get_field('google_maps_api_key', 'option') . '&callback=Function.prototype');
	}
	wp_enqueue_script('g-recaptcha', 'https://www.google.com/recaptcha/api.js?&onload=frmRecaptcha&render=explicit', [], false);
	wp_enqueue_script('barba-core', 'https://unpkg.com/@barba/core', array());
	wp_enqueue_script('barba-transitions', get_stylesheet_directory_uri() . '/js/barba-transitions.js', array(), $custom_assets_version, true);
	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}


}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles', 7800000);


/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain()
{
	load_child_theme_textdomain('understrap-child', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'add_child_theme_textdomain');



/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version()
{
	return 'bootstrap5';
}
add_filter('theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20);



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js()
{
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array('customize-preview'),
		'20130508',
		true
	);
}
add_action('customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js');


// Register the menu locations
if (! function_exists('switchboard_register_menus')) {

	function switchboard_register_menus()
	{
		register_nav_menus(array(
			'primary'      => __('Primary Menu', 'switchboard_child'),
			'social_areas' => __('Social Menu', 'switchboard_child'),
			'homepage'     => __('Homepage Menu', 'switchboard_child'),
		));
	}
	add_action('after_setup_theme', 'switchboard_register_menus', 0);
}

// Get menu by menu location
function menu_items($menu_slug)
{

	$menu_items = array();

	if (($locations = get_nav_menu_locations()) && isset($locations[$menu_slug])) {
		$menu = get_term($locations[$menu_slug]);

		$menu_items = wp_get_nav_menu_items($menu->term_id);


	}

	return $menu_items;

}
/**
 * 
 *  Generate a slug based on the text of the page
 * 
 * @param string $text
 * @return string
 */
function slugify($text, string $divider = '-')
{
	// replace non letter or digits by divider
	$text = preg_replace('~[^\pL\d]+~u', $divider, $text);

	// transliterate
	$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	// remove unwanted characters
	$text = preg_replace('~[^-\w]+~', '', $text);

	// trim
	$text = trim($text, $divider);

	// remove duplicate divider
	$text = preg_replace('~-+~', $divider, $text);

	// lowercase
	$text = strtolower($text);

	if (empty($text)) {
		return 'n-a';
	}

	return $text;
}

/**
 * Generate a random string
 *
 * @param int $length
 * @return string							
 */
function generateRandomString($length = 10)
{
	$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString     = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[random_int(0, $charactersLength - 1)];
	}
	return $randomString;
}

/**
 * Removes the parent themes template registrations
 */

function switchboard_remove_page_templates($templates)
{
	unset($templates['page-templates/blank.php']);
	unset($templates['page-templates/left-sidebarpage.php']);
	unset($templates['page-templates/right-sidebarpage.php']);
	unset($templates['page-templates/no-title.php']);
	unset($templates['page-templates/both-sidebarspage.php']);
	unset($templates['page-templates/empty.php']);
	unset($templates['page-templates/fullwidthpage.php']);
	return $templates;
}
add_filter('theme_page_templates', 'switchboard_remove_page_templates');

/**
 * Checks if the post is in the homepage menu, 
 * and sets the namespace accordingly
 * @param int $post_id
 * @return bool
 */
function switchboard_is_standalone_panel($post_id)
{
	$menu_items = [];

	foreach (menu_items('homepage') as $menu_item) {
		$menu_items[] = (int) $menu_item->object_id;
	}

	if (in_array($post_id, $menu_items)) {
		return false;
	} else {
		return true;
	}

}

/**
 * Sets the animation target for the primary menu items
 * @param mixed $atts
 * @param mixed $item
 * @param mixed $args
 * @return mixed
 * 
 * 
 */
function switchboard_set_animation_target($atts, $item, $args)
{

	// check if the item is in the primary menu
	if ($args->theme_location == 'primary') {
		/**
		 * Set the animation target based on 
		 * the homepage menu hierarchy
		 */

		$animation_target              = basename(get_permalink((int) $item->object_id));
		$url                           = preg_replace("(^https?://)", "", site_url());
		$top_level_homepage_menu_items = [];
		$child_homepage_menu_items     = [];

		// If the page spits out the base url, set the animation target to empty
		if (str_contains($animation_target, $url)) {
			$animation_target = str_replace($animation_target, $url, '');
		}

		// Get the top level (parent) homepage menu items 
		foreach (menu_items('homepage') as $menu_item) {
			if (! $menu_item->menu_item_parent) {
				$top_level_homepage_menu_items[] = $menu_item;
			}
		}

		// Get the child homepage menu items 
		foreach (menu_items('homepage') as $menu_item) {
			if ($menu_item->menu_item_parent) {
				$child_homepage_menu_items[] = $menu_item;
			}
		}

		/**
		 * Compare the menu items 
		 * set to the name if there's a match.
		 * 
		 * First checks if the child is a match,
		 * then checks if the child has a parent match.
		 * if successful, will override the 
		 * animation menu target
		 */
		foreach ($child_homepage_menu_items as $child_item) {
			if ($item->object_id == $child_item->object_id) {
				foreach ($top_level_homepage_menu_items as $parent_item) {
					if ($parent_item->ID == $child_item->menu_item_parent) {
						$animation_target = slugify($parent_item->title);
					}
				}
			}
		}

		// add the desired attributes:
		$atts['data-animation-target'] = $animation_target;

	}
	return $atts;
}
add_filter('nav_menu_link_attributes', 'switchboard_set_animation_target', 10, 3);

function get_page_ID()
{
	//if on the blog page
	if (is_home() && ! in_the_loop()) {
		$ID = get_option('page_for_posts');
	} elseif (is_post_type_archive()) {
		//reference a custom archive page based it's slug
		//eg. for a 'houses' custom post type, you would create a page called `houses` and store any archive front matter on this page
		$query   = get_queried_object();
		$slug    = $query->name;
		$pageobj = get_page_by_path($slug);
		$ID      = $pageobj->ID;
	} else {
		$ID = get_the_ID();
	}
	return $ID;
}

/**
 * ACF Fields Registration
 */

if (class_exists('acf')) {
	add_action('acf/include_fields', function () {
		if (! function_exists('acf_add_local_field_group')) {
			return;
		}

		// Blocks

		acf_add_local_field_group(array(
			'key'                   => 'group_667bf629e07a1',
			'title'                 => 'Carousel',
			'fields'                => array(
				array(
					'key'               => 'field_667bf63d5fcfb',
					'label'             => 'Gallery',
					'name'              => 'gallery',
					'aria-label'        => '',
					'type'              => 'repeater',
					'instructions'      => 'A custom carousel that displays content alongside captions.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'layout'            => 'block',
					'pagination'        => 0,
					'min'               => 1,
					'max'               => 0,
					'collapsed'         => 'field_667bf6685fcfc',
					'button_label'      => 'Add Row',
					'rows_per_page'     => 20,
					'sub_fields'        => array(
						array(
							'key'               => 'field_667bf6685fcfc',
							'label'             => 'Title',
							'name'              => 'title',
							'aria-label'        => '',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'maxlength'         => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'parent_repeater'   => 'field_667bf63d5fcfb',
						),
						array(
							'key'               => 'field_667c7c54d5b4a',
							'label'             => 'Date',
							'name'              => 'date',
							'aria-label'        => '',
							'type'              => 'date_picker',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'display_format'    => 'F j, Y',
							'return_format'     => 'F j, Y',
							'first_day'         => 1,
							'parent_repeater'   => 'field_667bf63d5fcfb',
						),
						array(
							'key'               => 'field_667bf6885fcfd',
							'label'             => 'Image',
							'name'              => 'image',
							'aria-label'        => '',
							'type'              => 'image',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'return_format'     => 'array',
							'library'           => 'all',
							'min_width'         => '',
							'min_height'        => '',
							'min_size'          => '',
							'max_width'         => '',
							'max_height'        => '',
							'max_size'          => '',
							'mime_types'        => '',
							'preview_size'      => 'medium',
							'parent_repeater'   => 'field_667bf63d5fcfb',
						),
						array(
							'key'               => 'field_667bf6995fcfe',
							'label'             => 'Content',
							'name'              => 'content',
							'aria-label'        => '',
							'type'              => 'wysiwyg',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'tabs'              => 'all',
							'toolbar'           => 'full',
							'media_upload'      => 1,
							'delay'             => 0,
							'parent_repeater'   => 'field_667bf63d5fcfb',
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/carousel',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

		acf_add_local_field_group(array(
			'key'                   => 'group_667b6bc36a4e4',
			'title'                 => 'Gallery',
			'fields'                => array(
				array(
					'key'               => 'field_667b6bc4f1af9',
					'label'             => 'Gallery',
					'name'              => 'gallery',
					'aria-label'        => '',
					'type'              => 'gallery',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'array',
					'library'           => 'all',
					'min'               => '',
					'max'               => '',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
					'insert'            => 'append',
					'preview_size'      => 'medium',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/gallery',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

		acf_add_local_field_group(array(
			'key'                   => 'group_668d658b3ca39',
			'title'                 => 'Media Kit',
			'fields'                => array(
				array(
					'key'               => 'field_668d67c7f9baf',
					'label'             => 'Sort',
					'name'              => 'sort',
					'aria-label'        => '',
					'type'              => 'select',
					'instructions'      => 'Sort the media kits by title',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'choices'           => array(
						'ASC'  => 'Ascending',
						'DESC' => 'Descending',
					),
					'default_value'     => false,
					'return_format'     => 'value',
					'multiple'          => 0,
					'allow_null'        => 0,
					'ui'                => 0,
					'ajax'              => 0,
					'placeholder'       => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/media-kits',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

		acf_add_local_field_group(array(
			'key'                   => 'group_668dbece5bd09',
			'title'                 => 'Press Releases',
			'fields'                => array(
				array(
					'key'               => 'field_668dbece86a65',
					'label'             => 'Sort',
					'name'              => 'sort',
					'aria-label'        => '',
					'type'              => 'select',
					'instructions'      => 'Sort the press releases by date',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'choices'           => array(
						'ASC'  => 'Ascending',
						'DESC' => 'Descending',
					),
					'default_value'     => false,
					'return_format'     => 'value',
					'multiple'          => 0,
					'allow_null'        => 0,
					'ui'                => 0,
					'ajax'              => 0,
					'placeholder'       => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/press-releases',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

		acf_add_local_field_group(array(
			'key'                   => 'group_668fb9a2e448e',
			'title'                 => 'Events',
			'fields'                => array(
				array(
					'key'               => 'field_668fb9a3a7fe8',
					'label'             => 'Show Filters',
					'name'              => 'show_filters',
					'aria-label'        => '',
					'type'              => 'true_false',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'message'           => 'Show the filter bar',
					'default_value'     => 0,
					'ui'                => 0,
					'ui_on_text'        => '',
					'ui_off_text'       => '',
				),
				array(
					'key'               => 'field_668fb9cea7fe9',
					'label'             => 'Hide Previous Events',
					'name'              => 'hide_previous_events',
					'aria-label'        => '',
					'type'              => 'true_false',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'message'           => 'Hide Events in the past',
					'default_value'     => 1,
					'ui'                => 0,
					'ui_on_text'        => '',
					'ui_off_text'       => '',
				),
				array(
					'key'               => 'field_668fb9cea7fe37',
					'label'             => 'Request Event Link',
					'name'              => 'request_event_link',
					'aria-label'        => '',
					'type'              => 'link',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'message'           => 'Link to "Request / Suggest Event". Leave blank to hide the link.',
					'default_value'     => '',
					'ui'                => 0,
					'ui_on_text'        => '',
					'ui_off_text'       => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/events',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

		acf_add_local_field_group(array(
			'key'                   => 'group_66917a41305cf',
			'title'                 => 'News Posts',
			'fields'                => array(
				array(
					'key'               => 'field_66917a4115d94',
					'label'             => 'Sort',
					'name'              => 'sort',
					'aria-label'        => '',
					'type'              => 'select',
					'instructions'      => 'Sort the post by the article date',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'choices'           => array(
						'ASC'  => 'Ascending',
						'DESC' => 'Descending',
					),
					'default_value'     => false,
					'return_format'     => 'value',
					'multiple'          => 0,
					'allow_null'        => 0,
					'ui'                => 0,
					'ajax'              => 0,
					'placeholder'       => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/news-post',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

		// Custom Fields

		acf_add_local_field_group(array(
			'key'                   => 'group_668d59b26576c',
			'title'                 => 'Media Kits',
			'fields'                => array(
				array(
					'key'               => 'field_668d59b2a6b25',
					'label'             => 'Date',
					'name'              => 'date',
					'aria-label'        => '',
					'type'              => 'date_picker',
					'instructions'      => 'Pick a date that this media kit was released',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'display_format'    => 'd/m/Y',
					'return_format'     => 'd/m/Y',
					'first_day'         => 1,
				),
				array(
					'key'               => 'field_668d59e1a6b26',
					'label'             => 'Media Kit File',
					'name'              => 'media_kit_file',
					'aria-label'        => '',
					'type'              => 'file',
					'instructions'      => 'Add a downloadable media kit to this Entry',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'url',
					'library'           => 'all',
					'min_size'          => '',
					'max_size'          => '',
					'mime_types'        => 'pdf,zip',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'media_kit',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

		acf_add_local_field_group(array(
			'key'                   => 'group_668f519f84b39',
			'title'                 => 'Event',
			'fields'                => array(
				array(
					'key'               => 'field_668f51a039f68',
					'label'             => 'Start Date',
					'name'              => 'start_date',
					'aria-label'        => '',
					'type'              => 'date_time_picker',
					'instructions'      => 'Pick a date that this event starts on',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '50',
						'class' => '',
						'id'    => '',
					),
					'display_format'    => 'F j, Y g:i a',
					'return_format'     => 'F j, Y g:i a',
					'first_day'         => 1,
				),
				array(
					'key'               => 'field_668f51f639f69',
					'label'             => 'End Date',
					'name'              => 'end_date',
					'aria-label'        => '',
					'type'              => 'date_time_picker',
					'instructions'      => 'Pick a date that this event ends on',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '50',
						'class' => '',
						'id'    => '',
					),
					'display_format'    => 'F j, Y g:i a',
					'return_format'     => 'F j, Y g:i a',
					'first_day'         => 1,
				),
				array(
					'key'               => 'field_668f526839f6a',
					'label'             => 'Location',
					'name'              => 'location',
					'aria-label'        => '',
					'type'              => 'google_map',
					'instructions'      => 'Choose a location for this event',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'center_lat'        => '',
					'center_lng'        => '',
					'zoom'              => '',
					'height'            => '',
				),
				array(
					'key'               => 'field_66917762750748e',
					'label'             => 'Description',
					'name'              => 'description',
					'aria-label'        => '',
					'type'              => 'textarea',
					'instructions'      => 'The description of the event',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'maxlength'         => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'event',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

		acf_add_local_field_group(array(
			'key'                   => 'group_66907bf381fd5',
			'title'                 => 'Show Floating Button',
			'fields'                => array(
				array(
					'key'               => 'field_66907bf4a6002',
					'label'             => 'Show Floating Button',
					'name'              => 'show_floating_button',
					'aria-label'        => '',
					'type'              => 'true_false',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'message'           => 'Show a button throughout the site in the bottom right hand corner',
					'default_value'     => 1,
					'ui'                => 0,
					'ui_on_text'        => '',
					'ui_off_text'       => '',
				),
				array(
					'key'               => 'field_66907c84a6004',
					'label'             => 'Floating Button Title',
					'name'              => 'floating_button_title',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'The text that displays when the floating button is enabled',
					'required'          => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_66907bf4a6002',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'maxlength'         => 50,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				),
				array(
					'key'               => 'field_66907cdda6005',
					'label'             => 'Floating Button Content',
					'name'              => 'floating_button_content',
					'aria-label'        => '',
					'type'              => 'textarea',
					'instructions'      => 'The text that displays when the floating button is clicked',
					'required'          => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_66907bf4a6002',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'maxlength'         => 256,
					'rows'              => '',
					'placeholder'       => '',
					'new_lines'         => '',
				),
				array(
					'key'               => 'field_66907d1da6007',
					'label'             => 'Floating Button Shortcode',
					'name'              => 'floating_button_shortcode',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Add shortcode to this floating button\'s content. Displays after the text',
					'required'          => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_66907bf4a6002',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'maxlength'         => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'theme-settings',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

		acf_add_local_field_group(array(
			'key'                   => 'group_66914e77d656d',
			'title'                 => 'API Keys',
			'fields'                => array(
				array(
					'key'               => 'field_66914e78d6949',
					'label'             => 'Google Maps API Key',
					'name'              => 'google_maps_api_key',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Add your Google Maps API Key. Used in Events to get and show maps',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'maxlength'         => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'theme-settings',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

		acf_add_local_field_group(array(
			'key'                   => 'group_669177450ad7d',
			'title'                 => 'News Post Fields',
			'fields'                => array(
				array(
					'key'               => 'field_669177457500d',
					'label'             => 'URL',
					'name'              => 'url',
					'aria-label'        => '',
					'type'              => 'url',
					'instructions'      => 'The Link to the article',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '50',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
				),
				array(
					'key'               => 'field_669177817500f',
					'label'             => 'Date',
					'name'              => 'date',
					'aria-label'        => '',
					'type'              => 'date_picker',
					'instructions'      => 'The date the article was posted',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '50',
						'class' => '',
						'id'    => '',
					),
					'display_format'    => 'F j, Y',
					'return_format'     => 'F j, Y',
					'first_day'         => 1,
				),
				array(
					'key'               => 'field_669177627500e',
					'label'             => 'Author',
					'name'              => 'author',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'The author of the post',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'maxlength'         => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'news_post',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		));

	});

	// Add ACF Options Page

	add_action('acf/init', function () {
		acf_add_options_page(array(
			'page_title'  => 'Theme Options',
			'menu_slug'   => 'theme-settings',
			'parent_slug' => 'options-general.php',
			'menu_title'  => 'Theme Options',
			'position'    => 1000000,
			'redirect'    => false,
			'description' => 'Change your theme options here',
			'capability'  => 'edit_themes',
		));
	});
}

/**
 * Custom Post Type Registration
 */

add_action('init', function () {
	register_post_type('media_kit', array(
		'labels'             => array(
			'name'                     => 'Media Kits',
			'singular_name'            => 'Media Kit',
			'menu_name'                => 'Media Kits',
			'all_items'                => 'All Media Kits',
			'edit_item'                => 'Edit Media Kit',
			'view_item'                => 'View Media Kit',
			'view_items'               => 'View Media Kits',
			'add_new_item'             => 'Add new Media Kit',
			'add_new'                  => 'Add new',
			'new_item'                 => 'New Media Kit',
			'parent_item_colon'        => 'Parent Media Kit:',
			'search_items'             => 'Search Media Kits',
			'not_found'                => 'No Media Kits found',
			'not_found_in_trash'       => 'No Media Kits found in trash',
			'archives'                 => 'Media Kit archives',
			'attributes'               => 'Media Kits attributes',
			'featured_image'           => 'Featured image for this Media Kit',
			'set_featured_image'       => 'Set featured image for this Media Kit',
			'remove_featured_image'    => 'Remove featured image for this Media Kit',
			'use_featured_image'       => 'Use as featured image for this Media Kit',
			'insert_into_item'         => 'Insert into Media Kit',
			'uploaded_to_this_item'    => 'Upload to this Media Kit',
			'filter_items_list'        => 'Filter Media Kits list',
			'items_list_navigation'    => 'Media Kits list navigation',
			'items_list'               => 'Media Kits list',
			'item_published'           => 'Media Kit published',
			'item_published_privately' => 'Media Kit published privately.',
			'item_reverted_to_draft'   => 'Media Kit reverted to draft.',
			'item_scheduled'           => 'Media Kit scheduled',
			'item_updated'             => 'Media Kit updated.',

		),
		'rewrite'            => array('slug' => 'media-kit'),
		'description'        => 'Add PDF Downloadable Media Kits',
		'public'             => true,
		'publicly_queryable' => false,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-excerpt-view',
		'supports'           => array(
			0 => 'title',
			1 => 'thumbnail',
		),
		'can_export'         => false,
		'delete_with_user'   => false,
	));

	register_post_type('press_release', array(
		'labels'           => array(
			'name'                     => 'Press Releases',
			'singular_name'            => 'Press Release',
			'menu_name'                => 'Press Releases',
			'all_items'                => 'All Press Releases',
			'edit_item'                => 'Edit Press Release',
			'view_item'                => 'View Press Release',
			'view_items'               => 'View Press Releases',
			'add_new_item'             => 'Add New Press Release',
			'add_new'                  => 'Add New Press Release',
			'new_item'                 => 'New Press Release',
			'parent_item_colon'        => 'Parent Press Release:',
			'search_items'             => 'Search Press Releases',
			'not_found'                => 'No press releases found',
			'not_found_in_trash'       => 'No press releases found in Trash',
			'archives'                 => 'Press Release Archives',
			'attributes'               => 'Press Release Attributes',
			'insert_into_item'         => 'Insert into press release',
			'uploaded_to_this_item'    => 'Uploaded to this press release',
			'filter_items_list'        => 'Filter press releases list',
			'filter_by_date'           => 'Filter press releases by date',
			'items_list_navigation'    => 'Press Releases list navigation',
			'items_list'               => 'Press Releases list',
			'item_published'           => 'Press Release published.',
			'item_published_privately' => 'Press Release published privately.',
			'item_reverted_to_draft'   => 'Press Release reverted to draft.',
			'item_scheduled'           => 'Press Release scheduled.',
			'item_updated'             => 'Press Release updated.',
			'item_link'                => 'Press Release Link',
			'item_link_description'    => 'A link to a press release.',
		),
		'rewrite'          => array('slug' => 'press-release'),
		'description'      => 'Add press releases to your site.',
		'public'           => true,
		'show_in_rest'     => true,
		'menu_icon'        => 'dashicons-edit-page',
		'supports'         => array(
			0 => 'title',
			1 => 'editor',
			2 => 'custom-fields',
		),
		'delete_with_user' => false,
	));

	register_post_type('event', array(
		'labels'             => array(
			'name'                     => 'Events',
			'singular_name'            => 'Event',
			'menu_name'                => 'Events',
			'all_items'                => 'All Events',
			'edit_item'                => 'Edit Event',
			'view_item'                => 'View Event',
			'view_items'               => 'View Events',
			'add_new_item'             => 'Add New Event',
			'add_new'                  => 'Add New Event',
			'new_item'                 => 'New Event',
			'parent_item_colon'        => 'Parent Event:',
			'search_items'             => 'Search Events',
			'not_found'                => 'No events found',
			'not_found_in_trash'       => 'No events found in Trash',
			'archives'                 => 'Event Archives',
			'attributes'               => 'Event Attributes',
			'insert_into_item'         => 'Insert into event',
			'uploaded_to_this_item'    => 'Uploaded to this event',
			'filter_items_list'        => 'Filter events list',
			'filter_by_date'           => 'Filter events by date',
			'items_list_navigation'    => 'Events list navigation',
			'items_list'               => 'Events list',
			'item_published'           => 'Event published.',
			'item_published_privately' => 'Event published privately.',
			'item_reverted_to_draft'   => 'Event reverted to draft.',
			'item_scheduled'           => 'Event scheduled.',
			'item_updated'             => 'Event updated.',
			'item_link'                => 'Event Link',
			'item_link_description'    => 'A link to a event.',
		),
		'description'        => 'Add Events to your Website',
		'public'             => true,
		'publicly_queryable' => false,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-calendar-alt',
		'supports'           => array(
			0 => 'title',
			1 => 'custom-fields',
		),
		'delete_with_user'   => false,
	));

	register_post_type('news_post', array(
		'labels'           => array(
			'name'                     => 'News',
			'singular_name'            => 'Post',
			'menu_name'                => 'News',
			'all_items'                => 'All Posts',
			'edit_item'                => 'Edit Post',
			'view_item'                => 'View Post',
			'view_items'               => 'View Post',
			'add_new_item'             => 'Add New Post',
			'add_new'                  => 'Add New Post',
			'new_item'                 => 'New Post',
			'parent_item_colon'        => 'Parent Post:',
			'search_items'             => 'Search News Post',
			'not_found'                => 'No news post found',
			'not_found_in_trash'       => 'No news post found in Trash',
			'archives'                 => 'Post Archives',
			'attributes'               => 'Post Attributes',
			'insert_into_item'         => 'Insert into post',
			'uploaded_to_this_item'    => 'Uploaded to this post',
			'filter_items_list'        => 'Filter news post list',
			'filter_by_date'           => 'Filter news post by date',
			'items_list_navigation'    => 'News Post list navigation',
			'items_list'               => 'News Post list',
			'item_published'           => 'Post published.',
			'item_published_privately' => 'Post published privately.',
			'item_reverted_to_draft'   => 'Post reverted to draft.',
			'item_scheduled'           => 'Post scheduled.',
			'item_updated'             => 'Post updated.',
			'item_link'                => 'Post Link',
			'item_link_description'    => 'A link to a post.',
		),
		'public'           => true,
		'show_in_rest'     => true,
		'menu_icon'        => 'dashicons-admin-post',
		'supports'         => array(
			0 => 'title',
			1 => 'thumbnail',
			2 => 'custom-fields',
		),
		'delete_with_user' => false,
	));

});


/**
 * ACF Custom Block Registration
 *
 * @link https://developer.wordpress.org/reference/hooks/init/
 */

if (class_exists('acf')) {
	function switchboard_register_acf_blocks()
	{
		register_block_type(__DIR__ . '/blocks/gallery');
		register_block_type(__DIR__ . '/blocks/carousel');
		register_block_type(__DIR__ . '/blocks/media-kits');
		register_block_type(__DIR__ . '/blocks/press-releases');
		register_block_type(__DIR__ . '/blocks/events');
		register_block_type(__DIR__ . '/blocks/news-post');
	}
	add_action('init', 'switchboard_register_acf_blocks');
	function acf_block_scripts()
	{
		wp_register_script('fancybox', get_stylesheet_directory_uri() . '/blocks/gallery/fancybox.js', ['acf']);
		wp_register_script('fancybox-init', get_stylesheet_directory_uri() . '/blocks/gallery/fancybox-init.js', ['acf']);
		wp_register_script('splide', get_stylesheet_directory_uri() . '/blocks/carousel/splide.min.js', ['acf']);
		wp_register_script('carousel', get_stylesheet_directory_uri() . '/blocks/carousel/carousel.js', ['acf']);
		wp_register_script('add-to-calendar', 'https://cdn.jsdelivr.net/npm/add-to-calendar-button@2', ['acf']);
	}

	add_action('init', 'acf_block_scripts');
}

/**
 * ACF Google Maps API key Registration
 */
function switchboard_google_map_api($api)
{	if (class_exists('acf')) {
		$api['key'] = get_field('google_maps_api_key', 'option');
		return $api;
	}
}
add_filter('acf/fields/google_map/api', 'switchboard_google_map_api');

/**
 * Disable Comments
 */

add_action('admin_init', function () {
	// Redirect any user trying to access comments page
	global $pagenow;

	if ($pagenow === 'edit-comments.php') {
		wp_redirect(admin_url());
		exit;
	}

	// Remove comments metabox from dashboard
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

	// Disable support for comments and trackbacks in post types
	foreach (get_post_types() as $post_type) {
		if (post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
	remove_menu_page('edit-comments.php');
});


/**
 * Style Understrap's 'understrap_posted_on' function better
 */

if (! function_exists('understrap_posted_on')) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function understrap_posted_on()
	{
		$post = get_post();
		if (! $post) {
			return;
		}



		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if (get_the_time('U') !== get_the_modified_time('U')) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr(get_the_date('c')), // @phpstan-ignore-line -- post exists
			esc_html(get_the_date()), // @phpstan-ignore-line -- post exists
			esc_attr(get_the_modified_date('c')), // @phpstan-ignore-line -- post exists
			esc_html(get_the_modified_date()) // @phpstan-ignore-line -- post exists
		);

		$posted_on = apply_filters(
			'understrap_posted_on',
			sprintf(
				'<span class="posted-on">%1$s <a href="%2$s" class="post-date" rel="bookmark">%3$s</a></span>',
				esc_html_x('', 'post date', 'understrap'),
				esc_url(get_permalink()), // @phpstan-ignore-line -- post exists
				apply_filters('understrap_posted_on_time', $time_string)
			)
		);

		$author_id = (int) get_the_author_meta('ID');

		$author_name = '';

		if (0 === $author_id) {
			$byline = '';
		} else {
			$author_name = get_the_author_meta('display_name', $author_id);
			if (esc_html(get_the_author_meta('first_name', $author_id))) {
				$author_name = esc_html(get_the_author_meta('first_name', $author_id)) . ' ' . esc_html(get_the_author_meta('last_name', $author_id));
			}
			$byline = apply_filters(
				'understrap_posted_by',
				sprintf(
					'<span class="byline"> <span class="author vcard"> <a class="url" href="%2$s">%3$s</a>%1$s</span></span>',
					$posted_on ? esc_html_x(', ', 'post author', 'understrap') : esc_html_x('Posted by', 'post author', 'understrap'),
					esc_url(get_author_posts_url($author_id)),
					$author_name,
				)
			);
		}

		echo $byline . $posted_on; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Add a custom class to the Formidable Forms form
 * 
 * 
 */

add_action('frm_form_classes', 'switchboard_form_classes');
function switchboard_form_classes($form)
{
	echo 'switchboard-form';
}