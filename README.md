# Chroma PWA Loading
PHP, Javascript & SASS component that provides an elegant PWA image loading solution. Inspired by Medium, Spotify and Google Images.

### Usage
#### PHP
```
if (class_exists('Chromma_Lazy_Load_Module')) {
  add_filter( 'the_content', 'Chromma_Lazy_Load_Module::content_lazyload_filter' );
}
```
#### HTML / PHP
 ```
 <img class="llreplace" />
 ```

#### JS Build

##### e.g. gulp:
```
const lazyload = '../../plugins/chromma-lazy-load/assets/lazy-load.js';
gulp.task('js', function() {
    return gulp
        .src([lazyload, './js/script.js'])
        .pipe(concat('myscript.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js/'));
});
```

A base scss file is also included.
