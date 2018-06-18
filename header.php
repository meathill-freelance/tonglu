<?php
/**
 * @overview 通用头部
 * @author Meathill <meathilL@gmail.com>
 * @since 1.0
 *
 * Date: 2018/6/17
 * Time: 上午12:26
 */
global $page, $paged;
$page_num = $page > 2 || $paged > 2 ? ' | ' . sprintf(__('第 %s 页'), max($paged, $page)) : '';

// 提取描述和关键词
$tags = $description = '';
if (is_single()) {
  $description = apply_filters('the_excerpt', get_the_excerpt());
  $post_tags = get_the_terms(0, 'post_tag');
  $tags = ',';
  if ($post_tags) {
    foreach ($post_tags as $tag) {
      $tags .= $tag->name . ',';
    }
  }
  $tags = substr($tags, 0, -1);
}

$home_url = esc_url(home_url('/', is_ssl() ? 'https' : 'http'));
$result = array(
  'title' => wp_title('|', FALSE, 'right') . get_bloginfo('name') . $page_num,
  'description' => $description ? $description : get_bloginfo('description'),
  'keywords' => $tags,
  'pingback' => get_bloginfo('pingback_url'),
  'home_url' => $home_url,
  'theme_url' => get_theme_root_uri() . '/' . get_template(),
  'name' => get_bloginfo('name'),
  'name_title' => esc_attr(get_bloginfo('name', 'display')),
  'nav' => tonglu_bootstrap_nav(),
  'body_class' => join( ' ', get_body_class( ) ),
);

// 为了保证wp_head的输出
$template = dirname(__FILE__) . '/template/header.html';
$template = file_get_contents($template);
$tpl = new Mustache_Engine([
  'cache' => '/tmp/',
]);
$html = $tpl->render($template, $result);
$htmls = explode('<!-- wp_head -->', $html);
echo $htmls[0];
wp_head();
echo $htmls[1];
