<?php
/**
 * @overview 通用底部
 * @author Meathill <meathill@gmail.com>
 * @since 1.0.0
 *
 * Date: 2018/6/17
 * Time: 上午12:26
 */

$tpl = new Mustache_Engine([
  'cache' => '/tmp/',
]);
ob_start();
wp_footer();
$wp_footer = ob_get_contents();
ob_end_clean();

$locations = get_nav_menu_locations();
$object = wp_get_nav_menu_object($locations['footer']);
$links = wp_get_nav_menu_items($object->name, [
  'echo' => false,
]);

$home_url = esc_url(home_url('/', is_ssl() ? 'https' : 'http'));
$result = [
  'wp_footer' => $wp_footer,
  'theme_url' => get_theme_root_uri() . '/' . get_template(),
  'links' => $links,
];
$template = dirname(__FILE__) . '/template/footer.html';
$template = file_get_contents($template);
echo $tpl->render($template, $result);
