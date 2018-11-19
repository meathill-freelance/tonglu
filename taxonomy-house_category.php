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
$categories = null;
if ($current_category != 'onsale') {
	$categories = get_terms([
		'taxonomy' => 'house_category',
		'hide_empty' => false,
		'exclude' => 21,
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

  // 把當前國家放到第一位，剩下的是泰英美
  $order = ['england', 'america', 'thailand', ];
  usort($categories, function ($a, $b) use ($order) {
    // 当前选中的优先级最高
    if ($a['is_active']) {
      return -1;
    }
    if ($b['is_active']) {
      return 1;
    }

    $index_a = array_search($a['slug'], $order);
    $index_b = array_search($b['slug'], $order);
    $index_a = $index_a === false ? 100 : $index_a;
    $index_b = $index_b === false ? 100 : $index_b;
    return $index_a - $index_b;
  });
}

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
$base_url = '/house_category/' . $current_category . '/page/';
$pagination = generate_pagination($max_page, $base_url);

// 整理输出
$result = array_merge([
  'category' => single_cat_title('', false),
  'description' => category_description(),
  'blog' => $blog,
  'has_blog' => true,
  'categories' => $categories,
], $pagination);

$tpl = new Mustache_Engine();
$template = dirname(__FILE__) . '/template/location.html';
$template = file_get_contents($template);
echo $tpl->render($template, $result);

// 插入 swiper 初始化
wp_enqueue_script('script-name', get_template_directory_uri() . '/ui-src/js/location.js', [], '1.0.0');

get_footer();
