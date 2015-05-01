<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Quail Forever
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function quail_forever_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'render'    => 'quail_forever_infinite_scroll_render',
		'footer'    => 'page',
	) );
} // end function quail_forever_jetpack_setup
add_action( 'after_setup_theme', 'quail_forever_jetpack_setup' );

function quail_forever_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content', get_post_format() );
	}
} // end function quail_forever_infinite_scroll_render