<?php
/**
 * Created by PhpStorm.
 * User: meathill
 * Date: 2018/6/16
 * Time: 下午10:52
 */

if (!function_exists('tonglu_setup')) {
  function tonglu_setup() {
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1440, 120, true );

    register_nav_menus([
      'primary' => '主导航',
    ]);

    add_theme_support('html5', [
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption',
    ]);

    add_theme_support('custom-logo', [
      'height'      => 213,
      'width'       => 80,
      'flex-height' => true,
    ]);
  }
}
add_action('after_setup_theme', 'tonglu_setup');
