<?php
/*
Plugin Name: Custom Sidebar
Plugin URI: http://040.se
Description: This plugin adds a meta box on every page, that allows you to create a custom sidebar for that page.
Version: 0.1
Author: Martin Nilsson
Author URI: http://040.se
*/


/**
 *
 * Load the function file
 *
 **/
require_once('options/cs-options.php');


/**
 *
 * Add the meta boxes
 *
 * The foreach loop is looping through the custom sidebar option and adds
 * a meta box to each page/post/custom post type that is selected in the option panel.
 *
 **/
add_action('add_meta_boxes', 'custom_sidebar_meta_box');
function custom_sidebar_meta_box() {
  $types = get_option('custom_sidebar_settings');

  if($types) {
    foreach($types as $key => $type) {
      add_meta_box(
        'custom-sidebar',
        'Custom Sidebar',
        'custom_sidebar_markup',
        $type,
        'side',
        'high' 
      );
    }
  }
}


/**
 *
 * This function handles the markup. All the html
 * for the option page goes here
 *
 **/
function custom_sidebar_markup() {
  global $post;
  $custom = get_post_custom($post->ID);
  $field_id = $custom['has_custom_sidebar'][0];
 
  $field_id_value = get_post_meta($post->ID, 'has_custom_sidebar', true);
  if($field_id_value == "yes"){
    $field_id_checked = 'checked="checked"';
  } ?>

  <p style="font-size: 12px; color: #aaa; font-style: italic;"><?php _e('If you want to use a custom sidebar on this page, check the box below. To customize the content of the sidebar, go to Appearance > Widgets.'); ?></p>
  <input type="checkbox" name="has_custom_sidebar" value="yes" <?php echo $field_id_checked; ?> /> <?php _e('Use custom sidebar.'); ?>
<?php
}


/**
 *
 * This function handles the saving process, it also deletes the
 * options thats not filled out from the DB
 *
 **/
add_action('save_post', 'custom_sidebar_save_details');
function custom_sidebar_save_details() {
  global $post;

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post->ID;
  }

  if (isset($_POST['has_custom_sidebar'])) {
    update_post_meta($post->ID, 'has_custom_sidebar', $_POST['has_custom_sidebar']);
  } else {
    delete_post_meta($post->ID, 'has_custom_sidebar');
  }
}


/**
 *
 * This function creates the sidebar for each post, page etc in the widget area.
 * Where the option for a custom_sidebar shows up, is decided
 * from the option panel.
 *
 **/
add_action('widgets_init', 'custom_sidebar_widget_init');
function custom_sidebar_widget_init() {
  $types = get_option('custom_sidebar_settings');
  $pageID = array();
  $landingpages = '';

  if(!empty($types)){
    foreach($types as $key => $type) {
      $landingpages[] = get_posts(array(
        'meta_key'    =>  'has_custom_sidebar',
        'meta_value'  =>  'yes',
        'post_type'   =>  $type
      ));
    }

    foreach($landingpages as $landingpage) {
      foreach($landingpage as $page) {
        if(!in_array($page->ID, $pageID)) {
          $pageID[] = $page->ID;
          register_sidebar(array(
            'name' => 'Custom Sidebar: ' . $page->post_title,
            'id' => 'custom-sidebar-' . $page->ID,
            'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="center">',
            'after_widget'  => '</div></div>',
            'before_title'  => '<h2 class="title">',
            'after_title'   => '</h2>'
          ));
        }
      }
    }
  }
}


/**
 *
 * This function will dynamically handle each custom sidebar
 * to make it easier for you. It takes one parameter:
 *
 * 1. Fallback sidebar (default: null)
 *
 **/
function custom_sidebar($fallback = null) {
  global $wp_query;
  $pID = $wp_query->post->ID;
  $has_custom_sidebar = get_post_meta($pID, 'has_custom_sidebar', true);

  if($has_custom_sidebar == 'yes' && is_singular()) {
    if (is_active_sidebar('custom-sidebar-'.$pID)) {
      dynamic_sidebar('custom-sidebar-'.$pID);
    }
  } else if (is_active_sidebar($fallback)) {
    dynamic_sidebar($fallback);
  }
}