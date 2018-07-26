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

// 生成列表
if (have_posts()) {
  $blog = [];
  while (have_posts()) {
    the_post();
    $tag_str = '';
    $posttags = get_the_tags();
    if ($posttags) {
      foreach($posttags as $tag) {
        $tag_str = $tag_str.'['.$tag->name . ']';
      }
    }
    $tag_str = trim($tag_str);
    $blog[] = array(
      'class' => join(' ', get_post_class($class, $post_id)),
      'title' => the_title('', '', FALSE),
      'tag' => $tag_str,
      'full_title' => the_title_attribute(array('echo' => FALSE)),
      'link' => apply_filters('the_permalink', get_permalink()),
      'date' => apply_filters('the_time', get_the_time('Y-m-d'), 'Y-m-d'),
      'excerpt' => apply_filters('the_excerpt', get_the_excerpt()),
      'thumbnail' => get_the_post_thumbnail_url(),
    );
  }
}

// 生成翻页
$max_page = $wp_query->max_num_pages;
$base_url = '/category/news/page/';
$pagination = generate_pagination($max_page, $base_url);

// 整理输出
$result = array_merge([
  'thumbnail' => get_the_category_thumbnail(null, null, true),
  'category' => single_cat_title('', false),
  'description' => category_description(),
  'blog' => $blog,
], $pagination);

$tpl = new Mustache_Engine();
$template = dirname(__FILE__) . '/template/category.html';
$template = file_get_contents($template);
echo $tpl->render($template, $result);

get_footer();
