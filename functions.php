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
    add_theme_support('category-thumbnails');

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

if (!function_exists('tonglu_bootstrap_nav')) {
  function tonglu_bootstrap_nav() {
    global $wp_query;
    $queried_object_id = (int) $wp_query->queried_object_id;
    $queried_object = $wp_query->get_queried_object();
    $menu_name = 'primary'; // specify custom menu slug
    if (($locations = get_nav_menu_locations()) && isset($locations[$menu_name])) {
      $menu = wp_get_nav_menu_object($locations[$menu_name]);
      $menu_items = wp_get_nav_menu_items($menu->term_id);
      $menu_items = array_map(function ($item) {
        $item->children = [];
        return $item;
      }, (array) $menu_items);
      $menu_items = array_combine(array_column($menu_items, 'ID'), $menu_items);

      $menu_list = '<ul class="navbar-nav">';

      foreach ($menu_items as $menu_item) {
        if ($menu_item->menu_item_parent) {
          $menu_items[$menu_item->menu_item_parent]->children[] = $menu_item;
        }
      }
      foreach ($menu_items as $menu_item) {
        if ($menu_item->menu_item_parent) {
          continue;
        }
        $title = $menu_item->title;
        $url = $menu_item->url;
        $is_active = $dropdown = '';
        if ($menu_item->object_id == $queried_object_id &&
          (
            ( ! empty( $home_page_id ) && 'post_type' == $menu_item->type && $wp_query->is_home && $home_page_id == $menu_item->object_id ) ||
            ( 'post_type' == $menu_item->type && $wp_query->is_singular ) ||
            ( 'taxonomy' == $menu_item->type && ( $wp_query->is_category || $wp_query->is_tag || $wp_query->is_tax ) && $queried_object->taxonomy == $menu_item->object )
          )
        ) {
          $is_active = ' active';
        }
        if (count($menu_item->children) > 0) {
          $dropdown = ' dropdown';
        }
        $menu_list .= '<li class="nav-item' . $dropdown . $is_active . '">
<a class="nav-link' . ($dropdown ? ' dropdown-toggle' : '') . '" href="'. $url .'" role="button" ' . ($dropdown ? 'data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"' : '') . '>
  <span class="cn">'. $title .'</span>
  <span class="en">' . $menu_item->attr_title . '</span>
</a>';
        if ($dropdown) {
          $menu_list .= '<div class="dropdown-menu" aria-labelledby="' . $menu_item->attr_title . '"><div class="container">';
          foreach ($menu_item->children as $item) {
            $menu_list .= '<a class="dropdown-item" href="' . $item->url . '">' . $item-> title .'</a>';
          }
          $menu_list .= '</div></div>';
        }
        $menu_list .= '</li>' . "\n";
      }
      $menu_list .= '</ul>' ."\n";
    } else {
      // $menu_list = '<!-- no list defined -->';
    }
    return $menu_list;
  }
}
