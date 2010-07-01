<?php
/*
Plugin Name: Blogs Widget
Description:
Author: Andrew Billits (Incsub)
Version: 1.0.1
Author URI:
*/

/* 
Copyright 2007-2009 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//------------------------------------------------------------------------//
//---Config---------------------------------------------------------------//
//------------------------------------------------------------------------//
$blogs_widget_main_blog_only = 'no'; //Either 'yes' or 'no'
//------------------------------------------------------------------------//
//---Hook-----------------------------------------------------------------//
//------------------------------------------------------------------------//

//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//
function widget_blogs_init() {
	global $wpdb, $blogs_widget_main_blog_only;
		
	// Check for the required API functions
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;

	// This saves options and prints the widget's config form.
	function widget_blogs_control() {
		global $wpdb;
		$options = $newoptions = get_option('widget_blogs');
		if ( $_POST['blogs-submit'] ) {
			$newoptions['blogs-title'] = $_POST['blogs-title'];
			$newoptions['blogs-display'] = $_POST['blogs-display'];
			$newoptions['blogs-blog-name-characters'] = $_POST['blogs-blog-name-characters'];
			$newoptions['blogs-order'] = $_POST['blogs-order'];
			$newoptions['blogs-number'] = $_POST['blogs-number'];
			$newoptions['blogs-avatar-size'] = $_POST['blogs-avatar-size'];
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_blogs', $options);
		}
	?>
				<div style="text-align:left">
                
				<label for="blogs-title" style="line-height:35px;display:block;"><?php _e('Title', 'widgets'); ?>:<br />
                <input class="widefat" id="blogs-title" name="blogs-title" value="<?php echo $options['blogs-title']; ?>" type="text" style="width:95%;">
                </select>
                </label>
				<label for="blogs-display" style="line-height:35px;display:block;"><?php _e('Display', 'widgets'); ?>:
                <select name="blogs-display" id="blogs-display" style="width:95%;">
                <option value="avatar_blog_name" <?php if ($options['blogs-display'] == 'avatar_blog_name'){ echo 'selected="selected"'; } ?> ><?php _e('Avatar + Blog Name'); ?></option>
                <option value="avatar" <?php if ($options['blogs-display'] == 'avatar'){ echo 'selected="selected"'; } ?> ><?php _e('Avatar Only'); ?></option>
                <option value="blog_name" <?php if ($options['blogs-display'] == 'blog_name'){ echo 'selected="selected"'; } ?> ><?php _e('Blog Name Only'); ?></option>
                </select>
                </label>
				<label for="blogs-blog-name-characters" style="line-height:35px;display:block;"><?php _e('Blog Name Characters', 'widgets'); ?>:<br />
                <select name="blogs-blog-name-characters" id="blogs-blog-name-characters" style="width:95%;">
                <?php
					if ( empty($options['blogs-blog-name-characters']) ) {
						$options['blogs-blog-name-characters'] = 30;
					}
					$counter = 0;
					for ( $counter = 1; $counter <= 500; $counter += 1) {
						?>
                        <option value="<?php echo $counter; ?>" <?php if ($options['blogs-blog-name-characters'] == $counter){ echo 'selected="selected"'; } ?> ><?php echo $counter; ?></option>
                        <?php
					}
                ?>
                </select>
                </label>
				<label for="blogs-order" style="line-height:35px;display:block;"><?php _e('Order', 'widgets'); ?>:
                <select name="blogs-order" id="blogs-order" style="width:95%;">
                <option value="most_recent" <?php if ($options['blogs-order'] == 'most_recent'){ echo 'selected="selected"'; } ?> ><?php _e('Most Recent'); ?></option>
                <option value="random" <?php if ($options['blogs-order'] == 'random'){ echo 'selected="selected"'; } ?> ><?php _e('Random'); ?></option>
                </select>
                </label>
				<label for="blogs-number" style="line-height:35px;display:block;"><?php _e('Number', 'widgets'); ?>:<br />
                <select name="blogs-number" id="blogs-number" style="width:95%;">
                <?php
					if ( empty($options['blogs-number']) ) {
						$options['blogs-number'] = 10;
					}
					$counter = 0;
					for ( $counter = 1; $counter <= 25; $counter += 1) {
						?>
                        <option value="<?php echo $counter; ?>" <?php if ($options['blogs-number'] == $counter){ echo 'selected="selected"'; } ?> ><?php echo $counter; ?></option>
                        <?php
					}
                ?>
                </select>
                </label>
				<label for="blogs-avatar-size" style="line-height:35px;display:block;"><?php _e('Avatar Size', 'widgets'); ?>:<br />
                <select name="blogs-avatar-size" id="blogs-avatar-size" style="width:95%;">
                <option value="16" <?php if ($options['blogs-avatar-size'] == '16'){ echo 'selected="selected"'; } ?> ><?php _e('16px'); ?></option>
                <option value="32" <?php if ($options['blogs-avatar-size'] == '32'){ echo 'selected="selected"'; } ?> ><?php _e('32px'); ?></option>
                <option value="48" <?php if ($options['blogs-avatar-size'] == '48'){ echo 'selected="selected"'; } ?> ><?php _e('48px'); ?></option>
                <option value="96" <?php if ($options['blogs-avatar-size'] == '96'){ echo 'selected="selected"'; } ?> ><?php _e('96px'); ?></option>
                <option value="128" <?php if ($options['blogs-avatar-size'] == '128'){ echo 'selected="selected"'; } ?> ><?php _e('128px'); ?></option>
                </select>
                </label>
				<input type="hidden" name="blogs-submit" id="blogs-submit" value="1" />
				</div>
	<?php
	}
// This prints the widget
	function widget_blogs($args) {
		global $wpdb, $current_site;
		extract($args);
		$defaults = array('count' => 10, 'blogname' => 'wordpress');
		$options = (array) get_option('widget_blogs');

		foreach ( $defaults as $key => $value )
			if ( !isset($options[$key]) )
				$options[$key] = $defaults[$key];

		?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . __($options['blogs-title']) . $after_title; ?>
            <br />
            <?php

			$newoptions['blogs-display'] = $_POST['blogs-display'];
			$newoptions['blogs-order'] = $_POST['blogs-order'];
			$newoptions['blogs-number'] = $_POST['blogs-number'];
			$newoptions['blogs-avatar-size'] = $_POST['blogs-avatar-size'];
				//=================================================//
				if ( $options['blogs-order'] == 'most_recent' ) {
					$query = "SELECT blog_id FROM " . $wpdb->base_prefix . "blogs WHERE site_id = '" . $wpdb->siteid . "' AND spam != '1' AND archived != '1' AND deleted != '1' ORDER BY registered DESC LIMIT " . $options['blogs-number'];
				} else if ( $options['blogs-order'] == 'random' ) {
					$query = "SELECT blog_id FROM " . $wpdb->base_prefix . "blogs WHERE site_id = '" . $wpdb->siteid . "' AND spam != '1' AND archived != '1' AND deleted != '1' ORDER BY RAND() LIMIT " . $options['blogs-number'];
				}
				$blogs = $wpdb->get_results( $query, ARRAY_A );
				if (count($blogs) > 0){
					if ( $options['blogs-display'] == 'blog_name' || $options['blogs-display'] == 'avatar_blog_name' ) {
						echo '<ul>';
					}
					foreach ($blogs as $blog){
						$blog_details = get_blog_details( $blog['blog_id'] );
						if ( $options['blogs-display'] == 'avatar_blog_name' ) {
							echo '<li>';
							echo '<a href="' . $blog_details->siteurl . '">' . get_blog_avatar( $blog['blog_id'], $options['blogs-avatar-size'], '' ) . '</a>';
							echo ' ';
							echo '<a href="' . $blog_details->siteurl . '">' . substr($blog_details->blogname, 0, $options['blogs-blog-name-characters']) . '</a>';
							echo '</li>';
						} else if ( $options['blogs-display'] == 'avatar' ) {
							echo '<a href="' . $blog_details->siteurl . '">' . get_blog_avatar( $blog['blog_id'], $options['blogs-avatar-size'], '' ) . '</a>';
						} else if ( $options['blogs-display'] == 'blog_name' ) {
							echo '<li>';
							echo '<a href="' . $blog_details->siteurl . '">' . substr($blog_details->blogname, 0, $options['blogs-blog-name-characters']) . '</a>';
							echo '</li>';
						}
					}
					if ( $options['blogs-display'] == 'blog_name' || $options['blogs-display'] == 'avatar_blog_name' ) {
						echo '</ul>';
					}
				}
				//=================================================//
			?>
		<?php echo $after_widget; ?>
<?php
	}
	// Tell Dynamic Sidebar about our new widget and its control
	if ( $blogs_widget_main_blog_only == 'yes' ) {
		if ( $wpdb->blogid == 1 ) {
			register_sidebar_widget(array(__('Blogs'), 'widgets'), 'widget_blogs');
			register_widget_control(array(__('Blogs'), 'widgets'), 'widget_blogs_control');
		}
	} else {
		register_sidebar_widget(array(__('Blogs'), 'widgets'), 'widget_blogs');
		register_widget_control(array(__('Blogs'), 'widgets'), 'widget_blogs_control');
	}
}

add_action('widgets_init', 'widget_blogs_init');

?>