<?php
if ( ! class_exists( 'mojoGalleryOptions' ) ) :

	/**
	 * mojoGalleryOptions class.
	 *
	 * @todo Add options for Gallery output.
	 * @version 0.4
	 * @since 0.1
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
								'columns' => '4',
								'archive_title' => 'Gallery',
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
							<th scope="row"><?php echo _e( 'Archive Page Title', 'mojo-gallery' );?></th>
							<td>
								<label><input name="mojoGallery_options[archive_title]" type="text" value="<?php if (isset($options['archive_title'])) echo $options['archive_title'];?>"/><span style="color:#666666;margin-left:2px;"><?php echo _e( 'The page title for your archive/category listing page', 'mojo-gallery' );?></span></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo _e( 'Default Placeholder URL', 'mojo-gallery' );?></th>
							<td>
								<label><input name="mojoGallery_options[placeholder]" type="text" value="<?php if (isset($options['placeholder'])) echo $options['placeholder'];?>" /><span style="color:#666666;margin-left:2px;"><?php echo _e( 'Default Image Placeholder URL, (150px x 150px by default)', 'mojo-gallery' );?></span></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo _e( 'Number of columns to show', 'mojo-gallery' );?></th>
							<td>
								<label><input name="mojoGallery_options[columns]" type="text" size="5" value="<?php if ( isset( $options['columns'] ) ) echo $options['columns'];?>"/><span style="color:#666666;margin-left:2px;"><?php echo _e( 'Number of columns to show in grid view', 'mojo-gallery' );?></span></label>
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
			$input['placeholder'] = esc_url( $input['placeholder'] );
			$input['columns'] = wp_filter_nohtml_kses( $input['columns']);
			$input['archive_title'] = wp_filter_nohtml_kses( $input['archive_title']);
			return $input;
		}
				
	} //end class
endif; //end class if
