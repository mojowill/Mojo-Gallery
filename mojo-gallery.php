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
 * @todo Add Options page to set default image and colorbox options
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
			
			add_filter( 'the_content', array( &$this, 'output_gallery' ) );
			
			include ( dirname( __FILE__ ) . '/includes/colorbox/colorbox.php' );
			
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
		 * @todo This might be a load of crap and messing up stuff?
		 */
		function output_gallery($content) {
			global $post;
			
			if ( 'mojo-gallery-album' == get_post_type() && is_singular()  ) :
			
				$gallery = strpos( $content, '[gallery' );
				if ( $gallery === false ) :
				
					$content .=  '[gallery]';
					
					return $content;
					
				else :
					
					return $content;
				
				endif;
			
			endif;
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
				
				$arr = array(	"chk_button1" => "1",
								"chk_button3" => "1",
								"textarea_one" => esc_html( "This type of control allows a large amount of information to be entered all at once. Set the 'rows' and 'cols' attributes to set the width and height." ),
								"txt_one" => "Enter whatever you like here..",
								"drp_select_box" => "four",
								"chk_default_options_db" => "",
								"rdo_group_one" => "one",
								"rdo_group_two" => "two"
				);
				
				update_option('mojoGallery_options', $arr);
			
			endif;

		}
		
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
			//add_menu_page( __('Mojo Gallery Options', 'mojo-gallery'), __('Mojo Gallery Options', 'mojo-gallery'), 'manage_options', 'mojo-options', array( &$this, 'render_form'));
			add_submenu_page( 'edit.php?post_type=mojo-gallery-album', __('Mojo Gallery Options', 'mojo-gallery' ), __('Options', 'mojo-gallery'), 'manage_options', array( $this, 'render_form' )  );
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
				<div class="icon32" id="icon-options-general"><br></div>
				<h2><?php echo _e( 'Ultimate Wordpress CRM Plugin', 'uwpcrm' );?></h2>
				<p><?php echo _e( 'Below are some sample controls that will be amended during plugin development.', 'uwpcrm' );?></p>
		
				<!-- Beginning of the Plugin Options Form -->
				<form method="post" action="options.php">
					<?php settings_fields('mojoGallery_plugin_options'); ?>
					<?php $options = get_option('mojoGallery_options'); ?>
		
					<!-- Table Structure Containing Form Controls -->
					<!-- Each Plugin Option Defined on a New Table Row -->
					<table class="form-table">
		
						<!-- Text Area Control -->
						<tr>
							<th scope="row"><?php echo _e( 'Sample Text Area', 'uwpcrm' );?></th>
							<td>
								<textarea name="uwpcrm_options[textarea_one]" rows="7" cols="50" type='textarea'><?php echo $options['textarea_one']; ?></textarea><br /><span style="color:#666666;margin-left:2px;"><?php echo _e( 'Add a comment here to give extra information to Plugin users', 'uwpcrm' );?></span>
							</td>
						</tr>
		
						<!-- Textbox Control -->
						<tr>
							<th scope="row"><?php echo _e( 'Enter Some Information', 'uwpcrm' );?></th>
							<td>
								<input type="text" size="57" name="uwpcrm_options[txt_one]" value="<?php echo $options['txt_one']; ?>" />
							</td>
						</tr>
		
						<!-- Radio Button Group -->
						<tr valign="top">
							<th scope="row"><?php echo _e( 'Radio Button Group #1', 'uwpcrm' );?></th>
							<td>
								<!-- First radio button -->
								<label><input name="uwpcrm_options[rdo_group_one]" type="radio" value="one" <?php checked('one', $options['rdo_group_one']); ?> /> <?php echo _e( 'Radio Button #1',' uwpcrm' );?></label><br />
		
								<!-- Second radio button -->
								<label><input name="uwpcrm_options[rdo_group_one]" type="radio" value="two" <?php checked('two', $options['rdo_group_one']); ?> /> <?php echo _e( 'Radio Button #2', 'uwpcrm' );?></label><br /><span style="color:#666666;"><?php echo _e( 'General comment to explain more about this Plugin option.', 'uwpcrm' );?></span>
							</td>
						</tr>
		
						<!-- Checkbox Buttons -->
						<tr valign="top">
							<th scope="row"><?php echo _e( 'Group of Checkboxes', 'uwpcrm' );?></th>
							<td>
								<!-- First checkbox button -->
								<label><input name="uwpcrm_options[chk_button1]" type="checkbox" value="1" <?php if (isset($options['chk_button1'])) { checked('1', $options['chk_button1']); } ?> /> <?php echo _e( 'Checkbox #1', 'uwpcrm' );?></label><br />
		
								<!-- Second checkbox button -->
								<label><input name="uwpcrm_options[chk_button2]" type="checkbox" value="1" <?php if (isset($options['chk_button2'])) { checked('1', $options['chk_button2']); } ?> /> <?php echo _e( 'Checkbox #2', 'uwpcrm' );?></label><br />
		
								<!-- Third checkbox button -->
								<label><input name="uwpcrm_options[chk_button3]" type="checkbox" value="1" <?php if (isset($options['chk_button3'])) { checked('1', $options['chk_button3']); } ?> /> <?php echo _e( 'Checkbox #3', 'uwpcrm' );?></label><br />
		
								<!-- Fourth checkbox button -->
								<label><input name="uwpcrm_options[chk_button4]" type="checkbox" value="1" <?php if (isset($options['chk_button4'])) { checked('1', $options['chk_button4']); } ?> /> <?php echo _e( 'Checkbox #4', 'uwpcrm' );?> </label><br />
		
								<!-- Fifth checkbox button -->
								<label><input name="uwpcrm_options[chk_button5]" type="checkbox" value="1" <?php if (isset($options['chk_button5'])) { checked('1', $options['chk_button5']); } ?> /> <?php echo _e( 'Checkbox #5', 'uwpcrm' );?> </label>
							</td>
						</tr>
		
						<!-- Another Radio Button Group -->
						<tr valign="top">
							<th scope="row">Radio Button Group #2</th>
							<td>
								<!-- First radio button -->
								<label><input name="uwpcrm_options[rdo_group_two]" type="radio" value="one" <?php checked('one', $options['rdo_group_two']); ?> /> Radio Button #1</label><br />
		
								<!-- Second radio button -->
								<label><input name="uwpcrm_options[rdo_group_two]" type="radio" value="two" <?php checked('two', $options['rdo_group_two']); ?> /> Radio Button #2</label><br />
		
								<!-- Third radio button -->
								<label><input name="uwpcrm_options[rdo_group_two]" type="radio" value="three" <?php checked('three', $options['rdo_group_two']); ?> /> Radio Button #3</label>
							</td>
						</tr>
		
						<!-- Select Drop-Down Control -->
						<tr>
							<th scope="row">Sample Select Box</th>
							<td>
								<select name='uwpcrm_options[drp_select_box]'>
									<option value='one' <?php selected('one', $options['drp_select_box']); ?>>One</option>
									<option value='two' <?php selected('two', $options['drp_select_box']); ?>>Two</option>
									<option value='three' <?php selected('three', $options['drp_select_box']); ?>>Three</option>
									<option value='four' <?php selected('four', $options['drp_select_box']); ?>>Four</option>
									<option value='five' <?php selected('five', $options['drp_select_box']); ?>>Five</option>
									<option value='six' <?php selected('six', $options['drp_select_box']); ?>>Six</option>
									<option value='seven' <?php selected('seven', $options['drp_select_box']); ?>>Seven</option>
									<option value='eight' <?php selected('eight', $options['drp_select_box']); ?>>Eight</option>
								</select>
								<span style="color:#666666;margin-left:2px;">Add a comment here to explain more about how to use the option above</span>
							</td>
						</tr>
		
						<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
						<tr valign="top" style="border-top:#dddddd 1px solid;">
							<th scope="row">Database Options</th>
							<td>
								<label><input name="uwpcrm_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
								<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
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
		

				
	} //end class
endif; //end class if

/**
 * Start the Plugin
 */
 
$mojoGallery = new mojoGallery();