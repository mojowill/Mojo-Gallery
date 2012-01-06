<?php
/*
Plugin Name: The Mojo Gallery
Plugin URI: http://www.mojowill.com/developer/mojo-gallery-plugin/
Description: A small gallery plugin using the built in media uploader and gallery shortcodes. THIS IS A WORK IN PROGRESS!
Version: 0.1
Author: theMojoWill
Author URI: http://www.mojowill.com
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 * Mojo Gallery
 *
 * A small gallery plugin using the built in media uploader and gallery shortcodes. THIS IS A WORK IN PROGRESS!
 *
 * @package MojoGallery
 * @author Will Wilson <will@mojowill.com>
 * @version 0.1
 * @since 0.1
 * @todo Have archive and single templates created and used
 * @todo Modify Taxonomy permalinks to include CPT
 * @todo Add Options page to set default image
 */

if ( ! class_exists( 'mojoGallery' ) ) :
	
	/**
	 * mojoGallery class.
	 */
	class mojoGallery {
								
		
		/**
		 * __construct function.
		 *
		 * We run our actions in here.
		 * 
		 * @access public
		 * @return void
		 */
		function __construct() {
			
			new mojoGalleryOptions;
			
			add_action( 'init', array( &$this, 'register_cpt_mojo_album' ) );
			add_action( 'init', array( &$this, 'register_taxonomy_album_tag' ) );
			add_action( 'init', array( &$this, 'register_taxonomy_album_category' ) );
			
			add_action( 'wp_print_scripts', array( &$this, 'gplus' ) );

			
			add_filter( 'the_content', array( &$this, 'output_gallery' ) );
			add_filter( 'the_content', array( &$this, 'social_sharing' ) );
									
			/**
			 * Custom Template Filter
			 * 
			 * Commented out for now.
			 */
			//add_filter( 'single_template', array( &$this, 'custom_single_template' ) );
			
			$options = get_option('mojoGallery_options');
			
			/**
			 * Only bring in the colorbox if options say so
			 */
			if (isset($options['colorbox'])) :
				
				if ( $options['colorbox'] == 1 ) :
					include ( dirname( __FILE__ ) . '/includes/colorbox/colorbox.php' );
				
				endif;
			endif;
		}
		
		/**
		 * register_mojo_cpt_albums function.
		 *
		 * Setup our Albums post type
		 * 
		 * @access public
		 * @return void
		 */
		function register_cpt_mojo_album() {

		    $labels = array( 
		        'name' => __( 'Albums', 'mojo-gallery' ),
		        'singular_name' => __( 'Album', 'mojo-gallery' ),
		        'add_new' => __( 'Add Album', 'mojo-gallery' ),
		        'add_new_item' => __( 'Add New Album', 'mojo-gallery' ),
		        'edit_item' => __( 'Edit Album', 'mojo-gallery' ),
		        'new_item' => __( 'New Album', 'mojo-gallery' ),
		        'view_item' => __( 'View Album', 'mojo-gallery' ),
		        'search_items' => __( 'Search Albums', 'mojo-gallery' ),
		        'not_found' => __( 'No albums found', 'mojo-gallery' ),
		        'not_found_in_trash' => __( 'No albums found in Trash', 'mojo-gallery' ),
		        'parent_item_colon' => __( 'Parent Album:', 'mojo-gallery' ),
		        'menu_name' => __( 'Mojo Gallery', 'mojo-gallery' ),
		    );
		
		    $args = array( 
		        'labels' => $labels,
		        'hierarchical' => true,
		        
		        'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		        'taxonomies' => array( 'album_tag', 'album_category' ),
		        'public' => true,
		        'show_ui' => true,
		        'show_in_menu' => true,
		        'menu_position' => 10,
		        
		        'show_in_nav_menus' => true,
		        'publicly_queryable' => true,
		        'exclude_from_search' => false,
		        'has_archive' => true,
		        'query_var' => true,
		        'can_export' => true,
		        'rewrite' => true,
		        'capability_type' => 'post'
		    );
		
		    register_post_type( 'mojo-gallery-album', $args );
		}
		
		/**
		 * register_taxonomy_album_tag function.
		 *
		 * Setup our album tags
		 * 
		 * @access public
		 * @return void
		 */
		function register_taxonomy_album_tag() {

		    $labels = array( 
		        'name' => __( 'Album Tags', 'mojo-gallery' ),
		        'singular_name' => __( 'Album Tag', 'mojo-gallery' ),
		        'search_items' => __( 'Search Album Tags', 'mojo-gallery' ),
		        'popular_items' => __( 'Popular Album Tags', 'mojo-gallery' ),
		        'all_items' => __( 'All Album Tags', 'mojo-gallery' ),
		        'parent_item' => __( 'Parent Album Tag', 'mojo-gallery' ),
		        'parent_item_colon' => __( 'Parent Album Tag:', 'mojo-gallery' ),
		        'edit_item' => __( 'Edit Album Tag', 'mojo-gallery' ),
		        'update_item' => __( 'Update Album Tag', 'mojo-gallery' ),
		        'add_new_item' => __( 'Add New Album Tag', 'mojo-gallery' ),
		        'new_item_name' => __( 'New Album Tag Name', 'mojo-gallery' ),
		        'separate_items_with_commas' => __( 'Separate album tags with commas', 'mojo-gallery' ),
		        'add_or_remove_items' => __( 'Add or remove album tags', 'mojo-gallery' ),
		        'choose_from_most_used' => __( 'Choose from the most used album tags', 'mojo-gallery' ),
		        'menu_name' => __( 'Album Tags', 'mojo-gallery' ),
		    );
		
		    $args = array( 
		        'labels' => $labels,
		        'public' => true,
		        'show_in_nav_menus' => false,
		        'show_ui' => true,
		        'show_tagcloud' => true,
		        'hierarchical' => false,
		
		        'rewrite' => true,
		        'query_var' => true
		    );
		
		    register_taxonomy( 'album_tag', array('mojo-gallery-album'), $args );

		}
		
		/**
		 * register_taxonomy_album_category function.
		 *
		 * Setup our categories
		 * 
		 * @access public
		 * @return void
		 */
		function register_taxonomy_album_category() {

		    $labels = array( 
		        'name' => __( 'Album Categories', 'mojo-gallery' ),
		        'singular_name' => __( 'Album Category', 'mojo-gallery' ),
		        'search_items' => __( 'Search Album Categories', 'mojo-gallery' ),
		        'popular_items' => __( 'Popular Album Categories', 'mojo-gallery' ),
		        'all_items' => __( 'All Album Categories', 'mojo-gallery' ),
		        'parent_item' => __( 'Parent Album Category', 'mojo-gallery' ),
		        'parent_item_colon' => __( 'Parent Album Category:', 'mojo-gallery' ),
		        'edit_item' => __( 'Edit Album Category', 'mojo-gallery' ),
		        'update_item' => __( 'Update Album Category', 'mojo-gallery' ),
		        'add_new_item' => __( 'Add New Album Category', 'mojo-gallery' ),
		        'new_item_name' => __( 'New Album Category Name', 'mojo-gallery' ),
		        'separate_items_with_commas' => __( 'Separate album categories with commas', 'mojo-gallery' ),
		        'add_or_remove_items' => __( 'Add or remove album categories', 'mojo-gallery' ),
		        'choose_from_most_used' => __( 'Choose from the most used album categories', 'mojo-gallery' ),
		        'menu_name' => __( 'Album Categories', 'mojo-gallery' ),
		    );
		
		    $args = array( 
		        'labels' => $labels,
		        'public' => true,
		        'show_in_nav_menus' => true,
		        'show_ui' => true,
		        'show_tagcloud' => false,
		        'hierarchical' => true,
		
		        'rewrite' => true,
		        'query_var' => true
		    );
		
		    register_taxonomy( 'album_category', array('mojo-gallery-album'), $args );

		}
		
		/**
		 * output_gallery function.
		 *
		 * Adds the Gallery Shortcode to the_content on album pages 
		 * ONLY if they haven't already added the shortcode! 
		 * 
		 * @access public
		 * @return void
		 * @todo Allow gallery options from options page (number of columns etc.)
		 */
		function output_gallery($content) {
			global $post;
			
			if ( ( 'mojo-gallery-album' == get_post_type() ) && is_singular()  ) :
			
				
				/**
				 * Add the gallery shortcode if needed
				 */
				$gallery = strpos( $content, '[gallery' );
				if ( $gallery === false ) :
				
					$content .=  '[gallery]';
					
					return $content;
					
				else :
					
					return $content;
				
				endif;
				
			endif;
		}
		
		/**
		 * gplus function.
		 *
		 * Required JS for the Social Stuff on loads on our pages!
		 * 
		 * @access public
		 * @return void
		 */
		function gplus() {
			if ( is_single() && ( 'mojo-gallery-album' == get_post_type() ) ) :
				wp_enqueue_script( 'gplus', 'https://apis.google.com/js/plusone.js', null, null, true );
			endif;
		}

		/**
		 * social_sharing function.
		 *
		 * Adds Social Media Sharing.
		 * 
		 * @access public
		 * @return void
		 */
		function social_sharing($content) {
				$options = get_option('mojoGallery_options');
				if ( is_single() && ( 'mojo-gallery-album' == get_post_type() ) && ($options['social'] == 1) ) : 
					return $content . '
						<div style="social-widget">
							<div style="display:inline;">
								<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
								<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
							</div>
							<div style="display:inline;">
								<g:plusone size="medium"></g:plusone>
								<script type="text/javascript">
									(function() { 
										var po = document.createElement(\'script\');
										po.type = \'text/javascript\';
										po.async = true;
										po.src = \'<a href="https://apis.google.com/js/plusone.js&#039;" rel="nofollow">https://apis.google.com/js/plusone.js&#039;</a>\';
										var s = document.getElementsByTagName(\'script\')[0];
										s.parentNode.insertBefore(po, s);
									})();
								</script>
							</div>
							<div style="display:inline;">
								<iframe src="http://www.facebook.com/plugins/like.php?href='. rawurlencode( get_permalink() ) .'>&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px;" allowTransparency="true"></iframe>
							</div>
						</div>
						<br />
						';
				else :
					return $content;
				endif;
					
		}
		
		/**
		 * custom_single_template function.
		 *
		 * Set our own single template for our albums.
		 * 
		 * @access public
		 * @param mixed $single_template
		 * @return void
		 * @todo is this the best way? Should we just use filters?
		 */
		function custom_single_template($single_template) {
			global $post;
			
			if ( $post->post_type == 'mojo-gallery-album' ) :
				$single_template = dirname( __FILE__ ) . '/single-gallery.php';
			endif;
			
			return $single_template;
		}
				
	} //end class

endif; //end class if

if ( ! class_exists( 'mojoGalleryOptions' ) ) :

	/**
	 * mojoGalleryOptions class.
	 */
	class mojoGalleryOptions {
		
		/**
		 * __construct function.
		 *
		 * Stick actions and filters in here.
		 * 
		 * @access public
		 * @return void
		 */
		function __construct() {
			register_activation_hook( __FILE__, array( &$this, 'add_defaults' ) );
			//register_uninstall_hook( __FILE__, array( &$this, 'delete_plugin_options' ) );
			
			add_action( 'admin_init', array( &$this, 'options_init' ) );
			add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
		}
		
		/**
		 * add_defaults function.
		 * 
		 * @access public
		 * @return void
		 */
		function add_defaults() {
			$tmp = get_option('mojoGallery_options');
		    if ( ( $tmp['chk_default_options_db'] == '1' ) || ( ! is_array( $tmp ) ) ) :
		    
				delete_option('mojoGallery_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
				
				$arr = array(	'colorbox' => '1',
								'social' => '1',
								'chk_button3' => '1',
								'chk_default_options_db' => '',
								'drp_select_box' => 'four',
				);
				
				update_option('mojoGallery_options', $arr);
			
			endif;

		}
		
		/**
		 * delete_plugin_options function.
		 * 
		 * @access public
		 * @static
		 * @return void
		 */
		static function delete_plugin_options() {
			delete_option( 'mojoGallery_options' );
		}
		
		/**
		 * options_init function.
		 * 
		 * @access public
		 * @return void
		 */
		function options_init() {
			register_setting( 'mojoGallery_plugin_options', 'mojoGallery_options', array( &$this, 'validate_options' ) );		
		}
		
		/**
		 * add_options_page function.
		 * 
		 * @access public
		 * @return void
		 */
		function add_options_page() {
			add_submenu_page( 'edit.php?post_type=mojo-gallery-album', __('Mojo Gallery Options', 'mojo-gallery' ), __('Options', 'mojo-gallery'), 'manage_options', 'mojoGallery-options', array( $this, 'render_form' )  );
		}
		
		/**
		 * mojoGallery_render_form function.
		 * 
		 * @access public
		 * @return void
		 */
		function render_form() {
		
		?>
			<div class="wrap">
				
				<!-- Display Plugin Icon, Header, and Description -->
				<div class="icon32" id="icon-upload"><br></div>
				<h2><?php echo _e( 'Mojo Gallery Options', 'mojo-gallery' );?></h2>
		
				<!-- Beginning of the Plugin Options Form -->
				<form method="post" action="options.php">
					<?php settings_fields('mojoGallery_plugin_options'); ?>
					<?php $options = get_option('mojoGallery_options'); ?>
		
					<!-- Table Structure Containing Form Controls -->
					<!-- Each Plugin Option Defined on a New Table Row -->
					<table class="form-table">
		
						<!-- Text Area Control -->
		
						<!-- Checkbox Buttons -->
						<tr valign="top">
							<th scope="row"><?php echo _e( 'Optional Extras', 'mojo-gallery' );?></th>
							<td>
								<!-- First checkbox button -->
								<label><input name="mojoGallery_options[colorbox]" type="checkbox" value="1" <?php if (isset($options['colorbox'])) { checked('1', $options['colorbox']); } ?> /> <?php echo _e( 'Use bundled Colorbox in Albums', 'mojo-gallery' );?></label><br />
		
								<!-- Second checkbox button -->
								<label><input name="mojoGallery_options[social]" type="checkbox" value="1" <?php if (isset($options['social'])) { checked('1', $options['social']); } ?> /> <?php echo _e( 'Display Social Sharing', 'mojo-gallery' );?></label><br />
		
								<!-- Third checkbox button -->
								<label><input name="mojoGallery_options[chk_button3]" type="checkbox" value="1" <?php if (isset($options['chk_button3'])) { checked('1', $options['chk_button3']); } ?> /> <?php echo _e( 'Checkbox #3', 'uwpcrm' );?></label><br />
		
								<!-- Fourth checkbox button -->
								<label><input name="mojoGallery_options[chk_button4]" type="checkbox" value="1" <?php if (isset($options['chk_button4'])) { checked('1', $options['chk_button4']); } ?> /> <?php echo _e( 'Checkbox #4', 'uwpcrm' );?> </label><br />
		
								<!-- Fifth checkbox button -->
								<label><input name="mojoGallery_options[chk_button5]" type="checkbox" value="1" <?php if (isset($options['chk_button5'])) { checked('1', $options['chk_button5']); } ?> /> <?php echo _e( 'Checkbox #5', 'uwpcrm' );?> </label>
							</td>
						</tr>
		
		
						<!-- Select Drop-Down Control -->
						<tr>
							<th scope="row">Sample Select Box</th>
							<td>
								<select name="mojoGallery_options[drp_select_box]">
									<option value="one" <?php selected('one', $options['drp_select_box']); ?>>One</option>
									<option value="two" <?php selected('two', $options['drp_select_box']); ?>>Two</option>
									<option value="three" <?php selected('three', $options['drp_select_box']); ?>>Three</option>
									<option value="four" <?php selected('four', $options['drp_select_box']); ?>>Four</option>
									<option value="five" <?php selected('five', $options['drp_select_box']); ?>>Five</option>
									<option value="six" <?php selected('six', $options['drp_select_box']); ?>>Six</option>
									<option value="seven" <?php selected('seven', $options['drp_select_box']); ?>>Seven</option>
									<option value="eight" <?php selected('eight', $options['drp_select_box']); ?>>Eight</option>
								</select>
								<span style="color:#666666;margin-left:2px;">Add a comment here to explain more about how to use the option above</span>
							</td>
						</tr>
		
						<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
						<tr valign="top" style="border-top:#dddddd 1px solid;">
							<th scope="row"><?php echo _e( 'Database Options', 'mojo-gallery');?></th>
							<td>
								<label><input name="uwpcrm_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> <?php echo _e( 'Restore defaults upon plugin deactivation/reactivation', 'mojo-gallery');?></label>
								<br /><span style="color:red;margin-left:2px;"><?php echo _e( 'Only check this if you want to reset plugin settings upon Plugin reactivation', 'mojo-gallery');?></span>
							</td>
						</tr>
					</table>
					<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>
				</form>
			</div>
			<?php	

		}
		
		/**
		 * validate_options function.
		 * 
		 * @access public
		 * @param mixed $input
		 * @return void
		 */
		function validate_options($input) {
			return $input;
		}
				
	} //end class
endif; //end class if

/**
 * Start the Plugin
 */
 
$mojoGallery = new mojoGallery();