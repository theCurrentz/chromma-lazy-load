# chromma-lazy-load
PHP &amp; Javascript module that provides an elegant lazy loading image solution. Inspired by Medium, Spotify and Google Images.

## How To Use.
Note that by default the filter doesn't work on all content, only where applied in the codebase

### Using PHP filter
'''
if (class_exists('Chromma_Lazy_Load_Abstract'))
{
  add_filter( 'the_content', 'Chromma_Lazy_Load_Abstract::content_lazyload_filter' );
}
'''


### Using JS
The included js will fire on any image where the llreplace css class exists, therefore the developer can apply this class at their discretion.
The included js is not automatically enqueued, therefore the developer will have to pull it into their workflow however they choose.
#### e.g. gulp:
'''
const lazyload = '../../plugins/chromma-lazy-load/assets/lazy-load.js';
gulp.task('js', function() {
    return gulp
        .src([lazyload, jQuery, './js/script.js'])
        .pipe(concat('myscript.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js/'));
});
'''

A base scss file is also included.
