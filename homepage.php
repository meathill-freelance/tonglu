<?php
/*
Template Name: Homepage
*/

/**
 * 首页模板
 *
 * @author Meathill <meathill@gmail.com>
 * @since 1.0.0
 *
 * Date: 2018/6/17
 * Time: 下午11:49
 */
require_once 'vendor/autoload.php';

get_header();

$tpl = new Mustache_Engine([
  'cache' => '/tmp/',
]);

while (have_posts()) {
  the_post();

  $title = get_the_title();
  $content = get_the_content();
  $content = apply_filters( 'the_content', $content );
  $content = str_replace( ']]>', ']]&gt;', $content );
  $thumbnail = get_the_post_thumbnail_url();
  $result = [
    'title' => $title,
    'content' => $content,
    'thumbnail' => $thumbnail,
  ];
  $template = dirname(__FILE__) . '/template/homepage.html';
  $template = file_get_contents($template);
  $html = $tpl->render($template, $result);
  echo $html;
}

get_footer();
