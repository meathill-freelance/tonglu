<?php
/**
 * 文章列表
 *
 * @author Meathill <meathill@gmail.com>
 * @since 1.0.0
 *
 * Date: 2018/6/18
 * Time: 下午2:11
 */

get_header();

$current_category = get_query_var('house_category');
$categories = get_terms([
  'taxonomy' => 'house_category',
  'hide_empty' => false,
]);
$categories = array_map(function ($category) use ($current_category) {
  $thumbnail = json_decode($category->term_thumbnail, true);
  return [
    'name' => $category->name,
    'slug' => $category->slug,
    'url' => $thumbnail['url'],
    'is_active' => $current_category == $category->slug,
  ];
}, $categories);

// 生成列表
if (have_posts()) {
  $blog = [];
  while (have_posts()) {
    the_post();

    $fields = get_post_meta($post->ID);
    $fields = strip_array_keys($fields);
    $blog[] = array_merge([
      'class' => join(' ', get_post_class($class, $post_id)),
      'title' => the_title('', '', FALSE),
      'full_title' => the_title_attribute(['echo' => FALSE]),
      'link' => apply_filters('the_permalink', get_permalink()),
      'date' => apply_filters('the_time', get_the_time('Y-m-d'), 'Y-m-d'),
      'excerpt' => apply_filters('the_excerpt', get_the_excerpt()),
      'thumbnail' => get_the_post_thumbnail_url(),
    ], $fields);
  }
}

// 生成翻页
$max_page = $wp_query->max_num_pages;
$cur_page = (get_query_var('paged')) ? get_query_var('paged') : 1;
$next_page = $cur_page + 1;

$pages = array();
$count = 1;
$query_str = $_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : NULL;
while ($count <= $max_page) {
  //TODO news不应该写死
  $pages[] = array(
    'num' => $count,
    'class' => $cur_page == $count ? 'active' : NULL,
    'href' => '/news/page/'.$count.$query_str,
  );
  $count++;
}
// 整理输出
$result = array(
  'category' => single_cat_title('', false),
  'description' => category_description(),
  'blog' => $blog,
  'has_blog' => true,
  'cur_page' => $cur_page,
  'pages' => $pages,
  'categories' => $categories,
);

$tpl = new Mustache_Engine();
$template = dirname(__FILE__) . '/template/location.html';
$template = file_get_contents($template);
echo $tpl->render($template, $result);

get_footer();
