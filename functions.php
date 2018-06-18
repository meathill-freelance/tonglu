<?php
/**
 * 通用函数，包括各种模板初始化
 *
 * @author Meathill <meathill@gmail.com>
 * @since 1.0.0
 *
 * Date: 2018/6/16
 * Time: 下午10:52
 */

require_once 'vendor/autoload.php';

/**************************************
 *
 * add action
 *
 * ***********************************/
if (!function_exists('tonglu_setup')) {
  /**
   * 模板设置
   */
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

    wp_enqueue_style('admin_css_bootstrap', '//cdn.bootcss.com/bootstrap/4.1.1/css/bootstrap.min.css', false, '4.1.1');
  }
}
add_action('after_setup_theme', 'tonglu_setup');

require 'postType/house.php';

/**************************************
 *
 * add action
 *
 * ***********************************/
/**
 * 判断导航元素是否处于 `active` 状态
 *
 * @param $menu_item
 * @return bool
 */
function is_menu_item_active($menu_item) {
  global $wp_query;
  $queried_object_id = (int) $wp_query->queried_object_id;
  $queried_object = $wp_query->get_queried_object();
  return $menu_item->object_id == $queried_object_id &&
    (
      ( ! empty( $home_page_id ) && 'post_type' == $menu_item->type && $wp_query->is_home && $home_page_id == $menu_item->object_id ) ||
      ( 'post_type' == $menu_item->type && $wp_query->is_singular ) ||
      ( 'taxonomy' == $menu_item->type && ( $wp_query->is_category || $wp_query->is_tag || $wp_query->is_tax ) && $queried_object->taxonomy == $menu_item->object )
    );
}

/**
 * 生成本站需要的基于 Bootstrap 的导航
 *
 * @return string
 */
function tonglu_bootstrap_nav() {
  $menu_name = 'primary'; // specify custom menu slug
  $locations = get_nav_menu_locations();
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
    $active = is_menu_item_active($menu_item) ? ' active' : '';
    $dropdown = count($menu_item->children) > 0 ? ' dropdown' : '';
    $menu_list .= '<li class="nav-item' . $dropdown . $active . '">
<a class="nav-link' . ($dropdown ? ' dropdown-toggle' : '') . '" href="'. $url .'" role="button" ' . ($dropdown ? 'data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"' : '') . '>
<span class="cn">'. $title .'</span>
<span class="en">' . $menu_item->attr_title . '</span>
</a>';
    if ($dropdown) {
      $menu_list .= '<div class="dropdown-menu" aria-labelledby="' . $menu_item->attr_title . '"><div class="container">';
      foreach ($menu_item->children as $item) {
        $menu_list .= '<a class="dropdown-item' . $active . '" href="' . $item->url . '">' . $item-> title .'</a>';
      }
      $menu_list .= '</div></div>';
    }
    $menu_list .= '</li>' . "\n";
  }
  $menu_list .= '</ul>' ."\n";
  return $menu_list;
}

/**
 * 过滤数组座标，把前面的 `_house_` 删掉
 *
 * @param $value
 * @return array
 */
function strip_array_keys($value) {
  $value = array_filter($value, function ($key) {
    return preg_match('/^_house_/', $key);
  }, ARRAY_FILTER_USE_KEY);
  $keys = array_map(function ($key) {
    return substr($key, 7);
  }, array_keys($value));
  return array_combine($keys, array_map(function ($item) {
    return is_array($item) ? $item[0] : $item;
  }, $value));
}
