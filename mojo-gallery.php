<?php
/*
Plugin Name: The Mojo Gallery
Plugin URI: http://www.mojowill.com/developer/mojo-gallery-plugin/
Description: A small gallery plugin using the built in media uploader and gallery shortcodes. THIS IS A WORK IN PROGRESS!
Version: 0.2
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
			add_action( 'init', array( &$this, 'gallery_style' ) );
			
			add_action( 'wp_print_scripts', array( &$this, 'gplus' ) );
			add_action( 'init', array( &$this, 'load_languages' ) );

			
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
		 * gallery_style function.
		 * 
		 * @access public
		 * @return void
		 * @since 0.1
		 */
		function gallery_style() {
			 
			 if ( ! is_admin() ) :
			 	wp_enqueue_style( 'mojo-gallery', plugins_url( '', __FILE__ ) . '/gallery.css', null, '1.0', 'screen' );
			 endif;

		}
		
		/**
		 * load_languages function.
		 * 
		 * @access public
		 * @return void
		 * @since 0.2
		 */
		function load_languages() {
			load_plugin_textdomain( 'mojo-gallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
		
		/**
		 * register_mojo_cpt_albums function.
		 *
		 * Setup our Albums post type
		 * 
		 * @access public
		 * @return void
		 * @since 0.1
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
		        'menu_name' => 'Mojo Gallery', //we 're not translating this
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
		 * @since 0.1
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
		 * @since 0.1
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
		 * @since 0.1
		 * @todo Allow gallery options from options page (number of columns etc.)
		 */
		function output_gallery( $content ) {
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
		 * @since 0.1
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
		 * @since 0.1
		 */
		function social_sharing( $content ) {
				$options = get_option( 'mojoGallery_options' );
				if ( is_single() && ( 'mojo-gallery-album' == get_post_type() ) && ( $options['social'] == 1 ) ) : 
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
		 * @since 0.1
		 * @todo is this the best way? Should we just use filters?
		 */
		function custom_single_template( $single_template ) {
			global $post;
			
			if ( $post->post_type == 'mojo-gallery-album' ) :
				$single_template = dirname( __FILE__ ) . '/single-gallery.php';
			endif;
			
			return $single_template;
		}
		
		/**
		 * single_output function.
		 *
		 * Controls output of the single pages. Not being called at the moment as can't get it to work!
		 * 
		 * @access public
		 * @return void
		 * @since 0.1
		 */
		function single_output($post) {
			global $post;
	    	if ( is_single() && ( 'mojo-gallery-album' == get_post_type() ) ) :
		    	//Lets get some info about our parent post
				$parent_title = get_the_title($post->post_parent);
				
				//If we have a parent post modify the link/title to include it
				if ($post->post_parent ) : ?>
				
					<h1><a href="<?php echo get_post_type_archive_link( 'mojo-gallery-album' );?>">Gallery</a> &raquo; <a href="<?php echo get_permalink($post->post_parent);?>"><?php echo $parent_title;?></a> &raquo; <?php the_title();?></h1>			
				
				<?php else : ?>
			
					<h1><a href="<?php echo get_post_type_archive_link( 'mojo-gallery-album' );?>">Gallery</a> &raquo; <?php the_title();?></h1>						
				<?php endif;
			
				//If we have no content and we have children list them, need to check for the [gallery] shortcode somehow and need to account for parents with images and children!
				if ( ( get_the_content() == '' ) ) :
					
					$args = array(
						'post_type' => 'gallery',
						'child_of' => $post->ID,
						'parent' => $post->ID,
					);
					
					$albums = get_pages($args);
					
					$counter = 1; //start our counter
					$grids = 4; //images per row should be the same as archive-gallery.php
					?>
					
					<div id="gridcontainer">
					
					<?php foreach ( $albums as $album ) {
		
						$permalink = get_permalink( $album->ID );
						
						if ( $thumb = get_the_post_thumbnail( $album->ID, 'gallery-thumbnail')  == null ) :
							$thumb = '<img src="'. get_template_directory_uri() .'/images/default_thumb.jpg" />';
						else :
							$thumb = get_the_post_thumbnail( $album->ID, 'gallery-thumbnail');
						endif;
						
						
						//show left hand column
						if ( $counter != $grids ) : ?>
							<div class="griditemleft">
								<div class="postimage">
									<a href="<?php echo $permalink;?>"><?php echo $thumb;?></a>
								</div>
								<h2><a href="<?php echo $permalink;?>"><?php echo $album->post_title;?></a></h2>
							</div>
							
						<?php
						//show the right hand column
						elseif ( $counter == $grids ) : ?>
						
							<div class="griditemright">
								<div class="postimage">
									<a href="<?php echo $permalink;?>"><?php echo $thumb;?></a>
								</div>
								<h2><a href="<?php echo $permalink;?>"><?php echo $album->post_title;?></a></h2>								
							</div>
							
							<div class="clear"></div>
						
						<?php
						$counter = 0;
						
						endif;
		
						$counter++;
		
					} ?>
				
					</div>
				<?php
				//Or just show the content
				else :
										
					the_content('');
		
				endif;
				
			endif;
		}
		
				
	} //end class

endif; //end class if

if ( ! class_exists( 'mojoGalleryOptions' ) ) :

	/**
	 * mojoGalleryOptions class.
	 *
	 * @todo Add options for Gallery output.
	 */
	class mojoGalleryOptions {
		
		/**
		 * __construct function.
		 *
		 * Stick actions and filters in here.
		 * 
		 * @access public
		 * @return void
		 * @since 0.1
		 */
		function __construct() {
			register_activation_hook( __FILE__, array( &$this, 'add_defaults' ) );
			register_uninstall_hook( __FILE__, array( &$mojoGalleryOptions, 'delete_plugin_options' ) );
			
			add_action( 'admin_init', array( &$this, 'options_init' ) );
			add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
		}
		
		/**
		 * add_defaults function.
		 * 
		 * @access public
		 * @return void
		 * @since 0.1
		 */
		function add_defaults() {
			$tmp = get_option('mojoGallery_options');
		    if ( ( $tmp['chk_default_options_db'] == '1' ) || ( ! is_array( $tmp ) ) ) :
		    
				delete_option('mojoGallery_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
				
				$arr = array(	'colorbox' => '1',
								'social' => '1',
								'chk_default_options_db' => '',
								'theme' => 'theme1',
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
		 * @since 0.1
		 */
		public static function delete_plugin_options() {
			delete_option( 'mojoGallery_options' );
		}
		
		/**
		 * options_init function.
		 * 
		 * @access public
		 * @return void
		 * @since 0.1
		 */
		function options_init() {
			register_setting( 'mojoGallery_plugin_options', 'mojoGallery_options', array( &$this, 'validate_options' ) );		
		}
		
		/**
		 * add_options_page function.
		 * 
		 * @access public
		 * @return void
		 * @since 0.1
		 */
		function add_options_page() {
			add_submenu_page( 'edit.php?post_type=mojo-gallery-album', __('Mojo Gallery Options', 'mojo-gallery' ), __('Options', 'mojo-gallery'), 'manage_options', 'mojoGallery-options', array( $this, 'render_form' )  );
		}
		
		/**
		 * mojoGallery_render_form function.
		 * 
		 * @access public
		 * @return void
		 * @since 0.1
		 * @todo TIDY UP!
		 */
		function render_form() {
		
		?>
			<div class="wrap">
				
				<!-- Display Plugin Icon, Header, and Description -->
				<div class="icon32" id="icon-upload"><br /></div>
				<h2><?php echo _e( 'Mojo Gallery Options', 'mojo-gallery' );?></h2>
		
				<!-- Beginning of the Plugin Options Form -->
				<form method="post" action="options.php">
					<?php settings_fields('mojoGallery_plugin_options'); ?>
					<?php $options = get_option('mojoGallery_options'); ?>
		
					<!-- Table Structure Containing Form Controls -->
					<!-- Each Plugin Option Defined on a New Table Row -->
					<table class="form-table">
		
		
						<!-- Checkbox Buttons -->
						<tr valign="top">
							<th scope="row"><?php echo _e( 'Optional Extras', 'mojo-gallery' );?></th>
							<td>
								<!-- First checkbox button -->
								<label><input name="mojoGallery_options[colorbox]" type="checkbox" value="1" <?php if (isset($options['colorbox'])) { checked('1', $options['colorbox']); } ?> /> <?php echo _e( 'Use bundled Colorbox in Albums', 'mojo-gallery' );?></label><br />
		
								<!-- Second checkbox button -->
								<label><input name="mojoGallery_options[social]" type="checkbox" value="1" <?php if (isset($options['social'])) { checked('1', $options['social']); } ?> /> <?php echo _e( 'Display Social Sharing', 'mojo-gallery' );?></label><br />
		
							</td>
						</tr>

						<!-- Text Area Control -->
						<tr>
							<th scope="row"><?php echo _e( 'Image Settings', 'mojo-gallery' );?></th>
							<td>
								<label><input name="mojoGallery_options[placeholder]" type="text" value="<?php if (isset($options['placeholder'])) echo $options['placeholder'];?>" /><?php echo _e( 'Default Image Placeholder URL, (150px x 150px by default)', 'mojo-gallery' );?></label>
							</td>
						</tr>
						<!-- Select Drop-Down Control -->
						<tr>
							<th scope="row"><?php echo _e( 'Colorbox Theme', 'mojo-gallery' );?></th>
							<td>
								<select name="mojoGallery_options[theme]">
									<option value="theme1" <?php selected('theme1', $options['theme']); ?>><?php echo _e( 'Theme One', 'mojo-gallery' );?></option>
									<option value="theme2" <?php selected('theme2', $options['theme']); ?>><?php echo _e( 'Theme Two', 'mojo-gallery' );?></option>
									<option value="theme3" <?php selected('theme3', $options['theme']); ?>><?php echo _e( 'Theme Three', 'mojo-gallery' );?></option>
									<option value="theme4" <?php selected('theme4', $options['theme']); ?>><?php echo _e( 'Theme Four', 'mojo-gallery' );?></option>
									<option value="theme5" <?php selected('theme5', $options['theme']); ?>><?php echo _e( 'Theme Five', 'mojo-gallery' );?></option>
								</select>
								<span style="color:#666666;margin-left:2px;"><?php echo _e( 'Select which theme you wish to use for the Colorbox', 'mojo-gallery' );?></span>
							</td>
						</tr>
		
						<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
						<tr valign="top" style="border-top:#dddddd 1px solid;">
							<th scope="row"><?php echo _e( 'Database Options', 'mojo-gallery');?></th>
							<td>
								<label><input name="mojoGallery_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> <?php echo _e( 'Restore defaults upon plugin deactivation/reactivation', 'mojo-gallery');?></label>
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
		 * @since 0.1
		 */
		function validate_options( $input ) {
			$input['placeholder'] = esc_url( $input['placeholder'] ); //Sanitise our placeholder URL
			return $input;
		}
				
	} //end class
endif; //end class if

/**
 * Start the Plugin
 */
 
$mojoGallery = new mojoGallery();