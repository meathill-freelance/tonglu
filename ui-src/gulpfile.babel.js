import path from 'path';
import gulp from 'gulp';
import pug from 'gulp-pug';
import stylus from 'gulp-stylus';
import rename from 'gulp-rename';
import {readFile, readDir} from './helper/fs';
import html2tpl from './helper/html2tpl';

/* global __dirname */

gulp.task('pug', () => {
  return gulp.src('demo/*.pug')
    .pipe(pug())
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
  const content = await readFile(path.resolve(__dirname, 'html/homepage.html'), 'utf8');
  let [header, body, footer] = content.split('<!--split-->');
  await html2tpl(header, path.resolve(__dirname, '../template/header.html'));
  await html2tpl(footer, path.resolve(__dirname, '../template/footer.html'));
  const files = await readDir(path.resolve(__dirname, 'html'), /\.html$/);
  await Promise.all(files.map(async file => {
    const content = await readFile(path.resolve(__dirname, `html/${file}`), 'utf8');
    const parts = content.split('<!--split-->');
    await html2tpl(parts.length > 1 ? parts[1] : content, `../template/${file}`);
  }));
});

gulp.task('build', gulp.parallel(
  'pug',
  'stylus',
  taskDone => taskDone(),
));

gulp.task('default', gulp.series(
  'build',
  'template',
  taskDone => taskDone(),
));
