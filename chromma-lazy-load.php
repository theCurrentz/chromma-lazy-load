<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
Plugin Name: Chromma Lazy Load
Author: Parker Westfall
Description: PHP & Javascript module that provides an elegant lazy loading image solution. Inspired by Medium, Spotify and Google Search.
Version: 1.0
NOT LICENSED
*/

//require lazy load abstract class
require_once( plugin_dir_path( __FILE__ ) . '/classes/chromma-lazy-load-module.php');
//require caption override
require_once( plugin_dir_path( __FILE__ ) . '/includes/caption_shortcode_override.php');

//require lazy load admin page
require_once( plugin_dir_path( __FILE__ ) . '/includes/chromma_lazy_load_admin_page.php');

//require functions to get thumbnail dimensions
//@src https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
require_once( plugin_dir_path( __FILE__ ) . '/includes/get-image-sizes.php');
