<?php
/**
 * 创建"楼盘展示"相关的页面和分类
 *
 * @author Meathill
 * @since 1.0.0
 *
 * Date: 2018/6/18
 * Time: 下午6:33
 */

function add_post_type_house() {
  // 文章类型
  $labels = [
    'name' => '楼盘展示',
    'singular_name' => '楼盘展示',
    'add_new' => '新建楼盘展示',
    'add_new_item' => '新建楼盘展示',
    'edit_item' => '编辑楼盘展示',
    'new_item' => '新建楼盘展示',
    'all_items' => '所有楼盘展示',
    'view_item' => '查看楼盘展示',
    'search_items' => '搜索楼盘展示',
    'not_found' => '没有找到符合条件的楼盘展示',
    'not_found_in_trash' => '回收站里没有找到符合条件的楼盘展示',
    'parent_item_colon' => '',
    'menu_name' => '楼盘展示',
  ];
  $args = [
    'labels' => $labels,
    'description' => '',
    'public' => true,
    'menu_position' => 5,
    'supports' => [
      'title',
      'editor',
      'thumbnail',
      'excerpt',
    ],
    'has_archive' => true,
    'rewrite' => [
      'slug' => 'house/%house%',
    ]
  ];
  register_post_type('house', $args);

  // 文章分类
  $labels = [
    'name' => '楼盘分类',
    'singular_name' => '楼盘分类',
    'search_items' => '搜索楼盘分类',
    'all_items' => '所有楼盘分类',
    'parent_item' => '上级楼盘分类',
    'parent_item_colon' => '上级楼盘分类',
    'edit_item' => '编辑楼盘分类',
    'update_item' => '更新楼盘分类',
    'add_new_item' => '添加新的楼盘分类',
    'new_item_name' => '新楼盘分类',
    'menu_name' => '楼盘分类',
  ];
  $args = [
    'labels' => $labels,
    'hierarchical' => true,
    'rewrite' => [
      'hierarchical' => true,
    ],
  ];
  register_taxonomy('house_category', 'house', $args);
}
add_action('init', 'add_post_type_house');

// 修改链接，以便插入 category
function tonglu_house_post_link( $post_link, $id = 0 ){
  $post = get_post($id);
  if ( is_object( $post ) ){
    if ($post->post_type != 'house') {
      return $post_link;
    }
    $terms = wp_get_object_terms( $post->ID, 'house_category' );
    if( $terms ){
      return str_replace( '%house%' , $terms[0]->slug , $post_link );
    }
  }
  return $post_link;
}
add_filter( 'post_type_link', 'tonglu_house_post_link', 10, 3 );

// 增加重定向规则
function tonglu_house_rewrite_rules($rules) {
  $newRules = [
    'house/([^/]+)/([^/]+)/?$' => 'index.php?house=$matches[2]',
    'house/([^/]+)/?$' => 'index.php?house_category=$matches[1]',
  ];
  return array_merge($newRules, $rules);
}
add_filter('rewrite_rules_array', 'tonglu_house_rewrite_rules');

// 添加 meta box
function tonglu_house_meta_box($post) {
  $nonce = wp_nonce_field('tonglu_house_meta_box', 'tonglu_house_meta_box_nonce', false, false);
  $value = get_post_meta($post->ID);
  $value = strip_array_keys($value);

  $tpl = new Mustache_Engine([
    'cache' => '/tmp',
  ]);
  $form = file_get_contents(dirname(__FILE__) . '/../template/metaBox.html');
  $form = $tpl->render($form, array_merge($value, [
    'nonce' => $nonce,
  ]));
  echo $form;
}

function add_house_meta() {
  add_meta_box(
    'house_meta',
    '楼盘属性',
    'tonglu_house_meta_box',
    'house',
    'normal'
  );
}
add_action('add_meta_boxes', 'add_house_meta');

function house_meta_save_handler($post_id) {
  // 安全检查
  // 检查是否发送了一次性隐藏表单内容（判断是否为第三者模拟提交）
  if (!isset($_POST['tonglu_house_meta_box_nonce'])){
    return;
  }

  // 判断隐藏表单的值与之前是否相同
  if (!wp_verify_nonce( $_POST['tonglu_house_meta_box_nonce'], 'tonglu_house_meta_box')){
    return;
  }

  // 判断该用户是否有权限
  if (!current_user_can('edit_post', $post_id)){
    return;
  }

  $price = sanitize_text_field($_POST['price']);
  $unit = sanitize_text_field($_POST['unit']);
  $rmb = sanitize_text_field($_POST['rmb']);
  $tags = sanitize_text_field($_POST['tags']);
  $recommend = sanitize_text_field($_POST['recommend']);
  $rent = sanitize_text_field($_POST['rent']);
  $growth = sanitize_text_field($_POST['growth']);
  $address = sanitize_text_field($_POST['address']);
  $main_door = sanitize_text_field($_POST['main_door']);
  update_post_meta($post_id, '_house_price', $price);
  update_post_meta($post_id, '_house_unit', $unit);
  update_post_meta($post_id, '_house_rmb', $rmb);
  update_post_meta($post_id, '_house_tags', $tags);
  update_post_meta($post_id, '_house_recommend', $recommend);
  update_post_meta($post_id, '_house_rent', $rent);
  update_post_meta($post_id, '_house_growth', $growth);
  update_post_meta($post_id, '_house_address', $address);
  update_post_meta($post_id, '_house_main_door', $main_door);
}
add_action('save_post', 'house_meta_save_handler');
