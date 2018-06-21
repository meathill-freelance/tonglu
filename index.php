<?php
/**
 * @overview 通用页面
 * @author Meathill <meathill@gmail.com>
 * @since 1.0
 *
 * Date: 2018/6/17
 * Time: 上午12:25
 */

$is_house_list = get_query_var('house_category') === '0';
if ($is_house_list) {
  add_filter('body_class', function ($classes) {
    $classes[] = 'tax-house_category';
    return $classes;
  });
}

get_header();

if ($is_house_list) {
  get_template_part('template-parts/list', 'house');
} else {
  echo '<h1>Welcome to 通路有家</h1>';
}

get_footer();

