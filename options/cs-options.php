<?php

/**
 *
 * Add the plugin option page to admin menu
 *
 **/
add_action('admin_menu', 'custom_sidebar_admin_page');
function custom_sidebar_admin_page() {
  add_theme_page('Custom Sidebars Settings', 'Custom Sidebars', 'administrator', 'custom_sidebar_settings', 'custom_sidebar_admin_page_markup');
}


/**
 *
 * Register the settings
 *
 * This will save the option in the wp_options table as 'custom_sidebar_settings'
 * The third parameter is a function that will validate your input values
 *
 **/
add_action('admin_init', 'custom_sidebar_register_settings');
function custom_sidebar_register_settings() {
  register_setting('custom_sidebar_settings', 'custom_sidebar_settings', 'custom_sidebar_settings_validate');
}


/**
 *
 * Register stylesheet
 *
 **/
add_action('admin_init', 'custom_sidebar_stylesheet');
function custom_sidebar_stylesheet() {
  wp_enqueue_style( 'custom_stylesheet', plugin_dir_url( __FILE__ ) . 'cs-style.css', false, 'screen');
}


/**
 *
 * Validate inputs
 *
 * This function validates your input values.
 *
 * $args will contain the values posted in your settings form, you can validate
 * them as no spaces allowed, no special chars allowed or validate emails etc.
 *
 **/
function custom_sidebar_settings_validate($args) {
	// Enter validation here

  // Make sure you return the args
  return $args;
}


/**
 *
 * Admin notices
 *
 * Display the validation errors and update messages
 *
 **/
if(is_admin() && $_GET['page'] == 'custom_sidebar_settings') {
	add_action('admin_notices', 'custom_sidebar_admin_notices');
	function custom_sidebar_admin_notices() {
	  settings_errors();
	}
}


/**
 *
 * custom_sidebar_admin_page_markup
 *
 * This function handles the markup for your plugin settings page
 *
 **/
function custom_sidebar_admin_page_markup() { ?>
  <div class="wrap">

	  <?php screen_icon('themes'); ?>
		<h2>Custom Sidebars</h2>
		<p>This is a option page for the plugin: <strong>Custom Sidebar</strong>.</p>

	  <form action="options.php" method="post"><?php
	    settings_fields( 'custom_sidebar_settings' );
	    do_settings_sections( __FILE__ );

	    //get the older values, wont work the first time
	    $options = get_option( 'custom_sidebar_settings' ); ?>

	    <div class="cs-box">
	    	<div class="sidebar-name"><h3>Where do you want to display the options?</h3></div>
	    	<p>Here you can customize how the plugin should handle things and where to display the custom sidebar option checkboxes.</p>

	    
		    <div class="postbox">
					<div class="sidebar-name"><h3>Select your post types</h3></div>
					<div class="inside">
						<input name="custom_sidebar_settings[custom_sidebar_show_on_pages]" type="checkbox" name="pages" value="page" <?php echo (isset($options['custom_sidebar_show_on_pages']) && $options['custom_sidebar_show_on_pages'] != '') ? 'checked="checked"' : ''; ?>/> Pages<br>
						<input name="custom_sidebar_settings[custom_sidebar_show_on_posts]" type="checkbox" name="posts" value="post" <?php echo (isset($options['custom_sidebar_show_on_posts']) && $options['custom_sidebar_show_on_posts'] != '') ? 'checked="checked"' : ''; ?>/> Posts<br>


						<?php
							// Loop through every custom post type and add a input field
							$args = array(
							  'public'   => true,
							  '_builtin' => false
							);

							$types_obj = get_post_types($args, 'objects');
							$types = get_post_types($args);
							$html  = array();

							foreach($types as $type) {
								$name = $types_obj[$type]->labels->singular_name;

								$html[$type] = '<input name="custom_sidebar_settings[custom_sidebar_show_on_'.$type.']" type="checkbox" name="'.$type.'" value="'.$type.'" ';
								$html[$type] .= (isset($options['custom_sidebar_show_on_'.$type.'']) && $options['custom_sidebar_show_on_'.$type.''] != '') ? 'checked="checked"' : '';
								$html[$type] .= '/> '.$name.'<br>';
							}

							foreach($html as $input) {
								echo $input;
							}
						?>
					</div>
				</div>
			</div>

	    <input class="button button-primary button-large" type="submit" value="Save Changes" />
	  </form>
	</div>
<?php } ?>