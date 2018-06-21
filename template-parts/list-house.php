<?php
/**
 * 位置列表
 *
 * @author Meathill <meathill@gmail.com>
 * @since 1.0.0
 *
 * Date: 2018/6/18
 * Time: 下午2:11
 */

$categories = get_terms([
  'taxonomy' => 'house_category',
  'hide_empty' => false,
]);
$categories = array_map(function ($category) {
  $thumbnail = json_decode($category->term_thumbnail, true);
  return [
    'name' => $category->name,
    'slug' => $category->slug,
    'url' => $thumbnail['url'],
  ];
}, $categories);

// 整理输出
$result = array(
  'category' => single_cat_title('', false),
  'description' => category_description(),
  'cur_page' => $cur_page,
  'pages' => $pages,
  'categories' => $categories,
);

$tpl = new Mustache_Engine();
$template = dirname(__FILE__) . '/../template/location.html';
$template = file_get_contents($template);
echo $tpl->render($template, $result);
