<?php

/**
 * Fuction yang digunakan di theme ini.
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

add_action('after_setup_theme', 'velocitychild_theme_setup', 9);
add_action('customize_register', 'velocitychild_customize_register', 30);

function velocitychild_theme_setup()
{

	// Load justg_child_enqueue_parent_style after theme setup
	add_action('wp_enqueue_scripts', 'justg_child_enqueue_parent_style', 20);

	//remove action from Parent Theme
	remove_action('justg_header', 'justg_header_menu');
	remove_action('justg_do_footer', 'justg_the_footer_open');
	remove_action('justg_do_footer', 'justg_the_footer_content');
	remove_action('justg_do_footer', 'justg_the_footer_close');
	remove_theme_support('widgets-block-editor');
}

function velocitychild_customize_register($wp_customize)
{
	$wp_customize->add_setting(
		'welcome_text',
		array(
			'default'           => 'SELAMAT DATANG DI WEBSITE KAMI',
			'sanitize_callback' => 'sanitize_text_field',
			'type'              => 'theme_mod',
		)
	);

	$wp_customize->add_control(
		'welcome_text',
		array(
			'type'        => 'text',
			'label'       => esc_html__('Welcome Text', 'justg'),
			'description' => esc_html__('Enter your welcome text', 'justg'),
			'section'     => 'title_tagline',
			'priority'    => 10,
		)
	);

	$wp_customize->remove_control('display_header_text');
}

if (!function_exists('velocity_mobil3_no_image_url')) {
	function velocity_mobil3_no_image_url()
	{
		return trailingslashit(get_stylesheet_directory_uri()) . 'img/no-image.webp';
	}
}

if (!function_exists('velocity_mobil3_post_thumb_url')) {
	function velocity_mobil3_post_thumb_url($post_id, $size = 'large')
	{
		$post_id = (int) $post_id;
		if ($post_id > 0 && has_post_thumbnail($post_id)) {
			$image_url = get_the_post_thumbnail_url($post_id, $size);
			if (!empty($image_url)) {
				return $image_url;
			}
		}

		return velocity_mobil3_no_image_url();
	}
}

if (!function_exists('velocity_mobil3_render_post_thumb')) {
	function velocity_mobil3_render_post_thumb($post_id, $args = array())
	{
		$post_id = (int) $post_id;
		if ($post_id <= 0) {
			return '';
		}

		$defaults = array(
			'size'          => 'large',
			'ratio'         => '4x3',
			'img_class'     => 'w-100 h-100 velocity-thumb-image',
			'wrapper_class' => '',
			'link'          => true,
			'link_class'    => 'd-block',
		);
		$args = wp_parse_args($args, $defaults);

		$ratio_class = 'ratio ratio-' . preg_replace('/[^0-9x]/', '', (string) $args['ratio']);
		$wrapper_css = trim($ratio_class . ' ' . $args['wrapper_class']);
		$image_url   = velocity_mobil3_post_thumb_url($post_id, $args['size']);
		$post_title  = wp_strip_all_tags(get_the_title($post_id));
		$alt_text    = $post_title;
		$thumb_id    = get_post_thumbnail_id($post_id);

		if (!empty($thumb_id)) {
			$thumb_alt = trim((string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true));
			if ($thumb_alt !== '') {
				$alt_text = $thumb_alt;
			}
		}

		$image_html = sprintf(
			'<div class="%1$s"><img class="%2$s" src="%3$s" alt="%4$s" loading="lazy" decoding="async"></div>',
			esc_attr($wrapper_css),
			esc_attr($args['img_class']),
			esc_url($image_url),
			esc_attr($alt_text)
		);

		if (!$args['link']) {
			return $image_html;
		}

		$post_link = get_permalink($post_id);
		if (empty($post_link)) {
			return $image_html;
		}

		return sprintf(
			'<a class="%1$s" href="%2$s" title="%3$s" aria-label="%4$s">%5$s</a>',
			esc_attr($args['link_class']),
			esc_url($post_link),
			esc_attr($post_title),
			esc_attr($post_title),
			$image_html
		);
	}
}


///remove breadcrumbs
add_action('wp_head', function () {
	if (!is_single()) {
		remove_action('justg_before_title', 'justg_breadcrumb');
	}
});

if (!function_exists('justg_header_open')) {
	function justg_header_open()
	{
		echo '<header id="wrapper-header">';
		echo '<div id="wrapper-navbar" class="px-2 px-md-0" itemscope itemtype="http://schema.org/WebSite">';
	}
}
if (!function_exists('justg_header_close')) {
	function justg_header_close()
	{
		echo '</div>';
		echo '</header>';
	}
}


///add action builder part
add_action('justg_header', 'justg_header_berita');
function justg_header_berita()
{
	require_once(get_stylesheet_directory() . '/inc/part-header.php');
}
add_action('justg_do_footer', 'justg_footer_berita');
function justg_footer_berita()
{
	require_once(get_stylesheet_directory() . '/inc/part-footer.php');
}
add_action('justg_before_wrapper_content', 'justg_before_wrapper_content');
function justg_before_wrapper_content()
{
	echo '<div class="px-2">';
	echo '<div class="card rounded-top rounded-0 border-light border-top-0 border-bottom-0 shadow px-2 container">';
}
add_action('justg_after_wrapper_content', 'justg_after_wrapper_content');
function justg_after_wrapper_content()
{
	echo '</div>';
	echo '</div>';
}


// excerpt more
if ( ! function_exists( 'velocity_custom_excerpt_more' ) ) {
	function velocity_custom_excerpt_more( $more ) {
		return '...';
	}
}
add_filter( 'excerpt_more', 'velocity_custom_excerpt_more' );

// excerpt length
function velocity_excerpt_length($length){
	return 40;
}
add_filter('excerpt_length','velocity_excerpt_length');


//register widget
add_action('widgets_init', 'justg_widgets_init', 20);
if (!function_exists('justg_widgets_init')) {
	function justg_widgets_init()
	{
		register_sidebar(
			array(
				'name'          => __('Main Sidebar', 'justg'),
				'id'            => 'main-sidebar',
				'description'   => __('Main sidebar widget area', 'justg'),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
				'show_in_rest'   => false,
			)
		);
	}
}


if (!function_exists('justg_right_sidebar_check')) {
	function justg_right_sidebar_check()
	{
		if (is_singular('fl-builder-template')) {
			return;
		}
		if (!is_active_sidebar('main-sidebar')) {
			return;
		}
		echo '<div class="widget-area right-sidebar pt-3 pt-md-0 ps-md-3 ps-0 pe-0 col-md-4 order-3" id="right-sidebar" role="complementary">';
		do_action('justg_before_main_sidebar');
		dynamic_sidebar('main-sidebar');
		do_action('justg_after_main_sidebar');
		echo '</div>';
	}
}
