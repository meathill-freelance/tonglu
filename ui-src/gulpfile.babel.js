import path from 'path';
import gulp from 'gulp';
import pug from 'gulp-pug';
import stylus from 'gulp-stylus';
import rename from 'gulp-rename';

gulp.task('pug', () => {
  return gulp.src('demo/*.pug')
    .pipe(pug({

    }))
    .pipe(gulp.dest('html/'));
});

gulp.task('stylus', () => {
  return gulp.src('stylus/screen.styl')
    .pipe(stylus({
      'include css': true,
    }))
    .pipe(rename('style.css'))
    .pipe(gulp.dest('../'));
});

gulp.task('watch', () => {
  gulp.watch('demo/**/*.pug', gulp.parallel('pug'));
  gulp.watch('stylus/**/*.styl', gulp.parallel('stylus'));
});

gulp.task('template', async () => {

});

gulp.task('default', gulp.series(
  gulp.parallel(
    'pug',
    'stylus',
    taskDone => taskDone(),
  ),
  'template',
  taskDone => taskDone(),
));
