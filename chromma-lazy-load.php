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
require_once( plugin_dir_path( __FILE__ ) . '/classes/chromma-lazy-load-abstract.php');


/*
How To Use.
Note that by default the filter doesn't work on all content, only where applied in the codebase

Using PHP filter
e.g.
if (class_exists('Chromma_Lazy_Load_Abstract'))
{
  add_filter( 'the_content', 'Chromma_Lazy_Load_Abstract::content_lazyload_filter' );
}


Using js
The included js will fire on any image where the llreplace css class exists, therefore the developer can apply this class at their discretion
The included js is not automatically enqueued, therefore the developer will have to pull it into their workflow however they choose
e.g. gulp:
const lazyload = '../../plugins/chromma-lazy-load/assets/lazy-load.js';
gulp.task('js', function() {
    return gulp
        .src([lazyload, jQuery, './js/script.js'])
        .pipe(concat('myscript.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js/'));
});

scss file is also included as a styling template.
*/
