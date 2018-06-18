<?php
/**
 * 文章页
 * @author Meathill <meathill@gmail.com>
 * @since 1.0.0
 *
 * Date: 2018/6/18
 * Time: 下午2:10
 */
include_once 'vendor/autoload.php';

get_header();

if (have_posts()) {
  while (have_posts()) {
    the_post();

    $tags = get_the_tags();
    $links = array();
    foreach ($tags as $tag) {
      $links[] = '<a href="/tag/'.$tag->slug.'">'.$tag->name.'</a>';
    }
    $content = get_the_content('继续阅读');
    $blog = array(
      'id' => get_the_ID(),
      'is_featured' => is_sticky() && is_home() && ! is_paged(),
      'blog_class' => join(' ', get_post_class($class, $post_id)),
      'full_title' => the_title_attribute(array('echo' => FALSE)),
      'is_search' => is_search(),
      'link' => apply_filters('the_permalink', get_permalink()),
      'date' => apply_filters('the_time', get_the_time('Y-m-d'), 'Y-m-d'),
      'excerpt' => apply_filters('the_excerpt', get_the_excerpt()),
      'content' => apply_filters('the_content', $content),
      'category' => get_the_category_list('<li class="breadcrumb-item">'),
      'tags' => implode(' ', $links),
      'thumbnail' => get_the_post_thumbnail_url(),
    );
  }
}


$tpl = new Mustache_Engine();
$template = dirname(__FILE__) . '/template/single.html';
$template = file_get_contents($template);
echo $tpl->render($template, $blog);

get_footer();
