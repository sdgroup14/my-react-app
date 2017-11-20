

var gulp = require('gulp'),
  sass = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps'),
  clean = require('gulp-clean'),
  concat = require('gulp-concat'),
  gulpCopy = require('gulp-copy'),
  watch = require('gulp-watch'),
  runSequence = require('run-sequence'),
  autoprefixer = require('gulp-autoprefixer'),
  csso = require('gulp-csso'),
  imagemin = require('gulp-imagemin'),
  imageminPngquant = require('imagemin-pngquant'),
  imageminMozjpeg = require('imagemin-mozjpeg'),
  uglify = require('gulp-uglify'),
  pump = require('pump'),
  processhtml = require('gulp-processhtml'),
  uncss = require('gulp-uncss'),
  notify = require("gulp-notify"),
  spritesmith = require('gulp.spritesmith'),
  browserSync = require('browser-sync'),
  processhtml = require('gulp-processhtml'),
  opts = { /* plugin options */ },
  svgSprite = require('gulp-svg-sprite'),
  svgmin = require('gulp-svgmin'),
  cheerio = require('gulp-cheerio'),
  replace = require('gulp-replace'),
  notifier = require('node-notifier'),
  concatCss = require('gulp-concat-css'),
  cleanCSS = require('gulp-clean-css');

gulp.task('sass', function() {
  gulp.src('src/sass/style.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', function(err) {
      return notify().write(
        'File: ' + err.file + '\n' +
        'On line: ' + err.line + '\n')
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('src/css'))
});

gulp.task('default', function() {
  gulp.watch(['src/sass/style.scss', 'src/sass/content/**/*.scss', 'src/sass/content/**/**/*.scss', 'src/sass/libs/*.scss'], ['sass']);
})


gulp.task('del', function() {
  return gulp
    .src(['dist/*', 'src/js/scripts.min.js', 'src/css/style.min.css', 'src/css/libs/libs.min.css'], { read: false })
    .pipe(clean());
});

gulp.task('concat-css-libs', function() {
  // return gulp.src('src/css/libs/*.css')
  //   .pipe(concat('libs.min.css', { newLine: ';' }))
  //   .pipe(gulp.dest('src/css/libs/'));

  return gulp.src('src/css/libs/*.css')
    .pipe(concatCss('libs.min.css'))
    .pipe(gulp.dest('src/css/libs/'));
});


gulp.task('concat-js', function() {
  return gulp.src(['src/js/libs/*.js', 'src/js/libs/modules/*.js', 'src/js/*.js'])
    .pipe(concat('scripts.min.js', { newLine: '\n' }))
    .pipe(gulp.dest('src/js/'));
});


gulp.task('concat-all-css', function() {
  // return gulp.src(['src/css/libs/libs.min.css', 'src/css/style.css'])
  //   .pipe(concat('style.min.css', { newLine: ';' }))
  //   .pipe(gulp.dest('src/css/'));
   return gulp.src(['src/css/libs/libs.min.css', 'src/css/style.css'])
    .pipe(concatCss("style.min.css"))
    .pipe(gulp.dest('src/css/'));
});


gulp.task('copy', function() {
  return gulp
    .src([
      '*.php',
      'fonts/**',
      'views/**',
      'video/**',
      'img/*.svg'
    ], { cwd: 'src' })
    .pipe(gulpCopy('dist/'));
});


gulp.task('autoprefixer', () =>
  gulp.src('src/css/style.css')
  .pipe(autoprefixer({
    browsers: ['last 15 versions', 'ie 8', 'ie 9', 'opera 11', 'firefox 15'],
    cascade: false
  }))
  .pipe(gulp.dest('src/css/'))
);

gulp.task('csso', function() {
  return gulp.src('src/css/style.min.css')
    .pipe(csso({
      restructure: false,
      debug: true
    }))
    .pipe(gulp.dest('dist/css/'));
});


gulp.task('imagemin', () =>
  gulp.src('src/img/**/**/*.{png,jpg,gif}')
  .pipe(imagemin([
    imageminPngquant(),
    imageminMozjpeg({
      progressive: true
    })
  ], {
    verbose: true
  }))
  .pipe(gulp.dest('dist/img/'))
);


gulp.task('uglify', function() {
  pump([
    gulp.src('src/js/scripts.min.js'),
    uglify(),
    gulp.dest('dist/js/')
  ]);
});


// gulp.task('uncss', function() {
//   return gulp.src('dist/css/style.min.css')
//     .pipe(uncss({
//       html: ['src/index.html', 'src/views/*.html', 'src/views/pages/*.html', 'src/views/templates/*.html', 'src/views/templates/*.html']
//     }))
//     .pipe(gulp.dest('dist/css/'));
// });

gulp.task('clean-css', function() {
    return gulp.src('dist/css/style.min.css')
    .pipe(cleanCSS())
    .pipe(gulp.dest('dist/css/'));
});



gulp.task('sprite', function() {
  var spriteData =
    gulp.src('./sprite/*.*') // путь, откуда берем картинки для спрайта
    .pipe(spritesmith({
      imgName: 'sprite.png',
      cssName: '_sprite_png.scss',
      algorithm: 'binary-tree',
    }));

  spriteData.img.pipe(gulp.dest('./src/img/')); // путь, куда сохраняем картинку
  spriteData.css.pipe(gulp.dest('./src/sass/libs/')); // путь, куда сохраняем стили
});


gulp.task('svg', function() {
  return gulp.src('svg-sprite/*.svg')
    // minify svg
    .pipe(svgmin({
      js2svg: {
        pretty: true
      }
    }))
    // remove all fill, style and stroke declarations in out shapes
    .pipe(cheerio({
      run: function($) {
        $('[fill]').removeAttr('fill');
        $('[stroke]').removeAttr('stroke');
        $('[style]').removeAttr('style');
        $('[class]').removeAttr('class');
        $('style').remove();
      },
      parserOptions: { xmlMode: true }
    }))
    // cheerio plugin create unnecessary string '&gt;', so replace it.
    .pipe(replace('&gt;', '>'))
    // build svg sprite
    .pipe(svgSprite({
      mode: {
        symbol: {
          sprite: "../../../src/img/sprite.svg",
          render: {
            scss: {
              dest: '../../../src/sass/libs/_sprite.scss',
              template: "svg-sprite/scss/sprite-template.scss"
            }
          }
        }
      }
    }))
    .pipe(gulp.dest('svg-sprite/done/'));
});

gulp.task('processhtml', function() {
  gulp.src('src/*.html')
    .pipe(processhtml(opts))
    .pipe(gulp.dest('dist'));
  gulp.src('src/*.php')
    .pipe(processhtml(opts))
    .pipe(gulp.dest('dist'));
});




gulp.task('prod', function(cb) {
  runSequence('del', ['concat-css-libs', 'copy', 'autoprefixer'], ['concat-js', 'concat-all-css'], ['csso', 'imagemin', 'uglify', 'processhtml'],['clean-css'], function() {
    return gulp.src('')
      .pipe(notify("Done"));
  });
});