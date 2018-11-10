<?php
/**
 * 楼盘展示
 *
 * @author Meathill <meathill@gmail.com>
 * @since 1.0.0
 *
 * Date: 2018/6/18
 * Time: 下午10:08
 */
get_header();

if (have_posts()) {
  while (have_posts()) {
    the_post();

    $slides = null;
    $content = get_the_content('继续阅读');
    $content = preg_replace_callback('/\[gallery[^\]]*ids="([\d,)]+)"[^\]]*\]/', function ($match) use (&$slides) {
      $slides = explode(',', $match[1]);
      $slides = array_map(function ($id, $index) {
        return [
          'key' => $index,
          'url' => wp_get_attachment_url($id),
          'is_first' => $index == 0,
        ];
      }, $slides, array_keys($slides));
      return '';
    }, $content);
    $fields = get_post_meta($post->ID);
    $fields = strip_array_keys($fields);
    $fields['tags'] = explode(' ', $fields['tags']);
    $blog = array_merge($fields, array(
      'id' => get_the_ID(),
      'is_featured' => is_sticky() && is_home() && ! is_paged(),
      'full_title' => the_title_attribute(array('echo' => FALSE)),
      'is_search' => is_search(),
      'link' => apply_filters('the_permalink', get_permalink()),
      'date' => apply_filters('the_time', get_the_time('Y-m-d'), 'Y-m-d'),
      'excerpt' => apply_filters('the_excerpt', get_the_excerpt()),
      'content' => apply_filters('the_content', $content),
      'category' => get_the_category_list('</li><li class="breadcrumb-item">'),
      'thumbnail' => get_the_post_thumbnail_url(),
      'slides' => $slides,
      'theme_url' => get_theme_root_uri() . '/' . get_template(),
    ));
  }
}


$tpl = new Mustache_Engine();
$template = dirname(__FILE__) . '/template/house.html';
$template = file_get_contents($template);
echo $tpl->render($template, $blog);

// 插入 swiper 初始化
wp_enqueue_script('wx-js-sdk', '//res.wx.qq.com/open/js/jweixin-1.3.2.js', [], '1.3.2');
wp_enqueue_script('house', get_template_directory_uri() . '/ui-src/js/house.js', [], '1.0.0');

get_footer();
