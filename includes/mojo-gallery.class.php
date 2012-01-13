<?php
/**
 * Mojo Gallery
 *
 * A small gallery plugin using the built in media uploader and gallery shortcodes. THIS IS A WORK IN PROGRESS!
 *
 * @package MojoGallery
 * @author Will Wilson <will@mojowill.com>
 * @version 0.4.1
 * @since 0.1
 * @todo Modify Taxonomy permalinks to include CPT
 * @todo change use of global $mojo variables to use static property
 */

if ( ! class_exists( 'mojoGallery' ) ) :
	
	/**
	 * mojoGallery class.
	 */
	class mojoGallery {
		
		/**
		 * __construct function.
		 *
		 * We run our actions and filters in here.
		 * 
		 * @access public
		 * @return void
		 * @since 0.1
		 */
		function __construct() {
			global $mojoURI;
			
			new mojoGalleryOptions;
			
			add_action( 'init', array( &$this, 'register_cpt_mojo_album' ) );
			add_action( 'init', array( &$this, 'register_taxonomy_album_tag' ) );
			add_action( 'init', array( &$this, 'register_taxonomy_album_category' ) );
			add_action( 'init', array( &$this, 'gallery_style' ) );
			
			add_action( 'wp_print_scripts', array( &$this, 'gplus' ) );
			add_action( 'init', array( &$this, 'load_languages' ) );
			
			add_filter( 'single_template', array( &$this, 'load_single' ) );
			add_filter( 'archive_template', array( &$this, 'load_archive' ) );
			
			add_filter( 'the_content', array( &$this, 'output_gallery' ) );
						
			/**
			 * Get Options
			 */						
			$options = get_option('mojoGallery_options');
			
			/**
			 * Only bring in the colorbox if options say so
			 */
			if ( isset( $options['colorbox'])) :
				
				if ( $options['colorbox'] == 1 ) :
					include ( $mojoURI . '/includes/colorbox/colorbox.php' );
				
				endif;
			endif;
			
			/**
			 * Call the Social Buttons if needed
			 */
			if ( isset( $options['social'] ) ) :
				if ( $options['social'] == 1 ) :
					add_filter( 'the_content', array( &$this, 'social_sharing' ) );
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
			 global $mojoURL;
			 if ( ! is_admin() ) :
			 	wp_enqueue_style( 'mojo-gallery', $mojoURL . '/gallery.css', null, '1.0', 'screen' );
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
			global $mojoURI;
			load_plugin_textdomain( 'mojo-gallery', false, $mojoURI . '/languages/' );
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
		 * ONLY if they haven't already added the shortcode! Need to be careful of parent listings. 
		 * 
		 * @access public
		 * @return void
		 * @since 0.1
		 * @todo Allow gallery options from options page (number of columns etc.)
		 */
		function output_gallery( $content ) {
			global $post;
						
				/**
				 * Add the gallery shortcode if needed
				 */
				$gallery = strpos( $content, '[gallery' ); //check the content for the gallery shortcode
				
				if ( ($gallery === false ) && ( 'mojo-gallery-album' == get_post_type() ) && is_singular() && $this->has_attachments() ) :
				
					$content .=  '[gallery]';
					return $content;
					
				else :
					return $content;
				
				endif;
			
		}
		
		/**
		 * image_check function.
		 *
		 * A small function to check to see if a post has image attachments.
		 * We will use this in some of our other checks later on.
		 * 
		 * @access public
		 * @return void
		 * @since 0.4
		 */
		function has_attachments() {
			global $post;
			
			$args = array(
				'post_type' => 'attachment',
				'numberposts' => null,
				'post_status' => null,
				'post_parent' => $post->ID
				);
			
			$attachments = get_posts( $args );
			$is_images = false;
			
			/**
			 * check to see if attachments are images.
			 */
			
			foreach ( $attachments as $item ) :
				$mime_types = explode( '/', get_post_mime_type( $item->ID ) );
				if ( in_array( 'image', $mime_types ) ) :
					$is_images = true;
					break;
				endif;
			endforeach;
			
			if ( $is_images ) :
				return true;
			else :
				return false;
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
				if ( is_single() && ( 'mojo-gallery-album' == get_post_type() ) ) : 
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
		 * load_single function.
		 *
		 * First checks to see if the user has their own templates and creates them if not.
		 * 
		 * @access public
		 * @return void
		 * @since 0.4
		 */
		function load_single( $single_template ) {
			global $mojoURI;
			
			$parent_theme = get_template_directory() . '/mojo-gallery/';
			$child_theme = get_stylesheet_directory() . '/mojo-gallery/';
			
			/**
			 * First we check the stylesheet path to check for child themes
			 */
			if ( is_dir( $child_theme) && file_exists( $child_theme . '/single-mojo_gallery_album.php' ) ) :
			
				$single_template = $child_theme . '/single-mojo_gallery_album.php';
			/**
			 * If the stylesheet path isn't valid we check the template path
			 * this is to check if the parent theme has a template even if the child theme doesn't
			 */
			elseif ( is_dir( $parent_theme ) && file_exists( $parent_theme . '/single-mojo_gallery_album.php' ) ) :
			
				$single_template = $parent_theme . '/single-mojo_gallery_album.php';
			
			/**
			 * If we are sure the user doesn't have their own templates we load the plugin default
			 */
			else :
				global $post;
				
				if ( $post->post_type == 'mojo-gallery-album' ) :
					$single_template = $mojoURI . '/templates/single-mojo_gallery_album.php';
				endif;
									
			endif;
			
			return $single_template;

		}
		
		/**
		 * load_archive function.
		 *
		 * First checks to see if the user has their own templates and creates them if not.
		 * 
		 * @access public
		 * @return void
		 * @since 0.4
		 */
		function load_archive( $archive_template ) {
			global $mojoURI;
			
			$parent_theme = get_template_directory() . '/mojo-gallery/';
			$child_theme = get_stylesheet_directory() . '/mojo-gallery/';

			if ( is_post_type_archive( array('mojo-gallery-album') ) ) :

				/**
				 * First we check the stylesheet path to check for child themes
				 */
				if ( is_dir( $child_theme) && file_exists( $child_theme . '/archive-mojo_gallery_album.php' ) ) :
				
					$archive_template = $child_theme . '/archive-mojo_gallery_album.php';
				/**
				 * If the stylesheet path isn't valid we check the template path
				 * this is to check if the parent theme has a template even if the child theme doesn't
				 */
				elseif ( is_dir( $parent_theme ) && file_exists( $parent_theme . '/archive-mojo_gallery_album.php' ) ) :
				
					$archive_template = $parent_theme . '/archive-mojo_gallery_album.php';
				
				/**
				 * If we are sure the user doesn't have their own templates we load the plugin default
				 */
				else :
				
					$archive_template = $mojoURI . '/templates/archive-mojo_gallery_album.php';
												
				endif;
			
			endif;

			return $archive_template;
		}
		
		/**
		 * default_thumbnails function.
		 *
		 * Checks for a post thumb, if none checks for user default, if none uses plugin default.
		 * 
		 * @access public
		 * @return void
		 * @since 0.4
		 */
		function default_thumbnails() {
			global $mojoURL;
			
  			//Set Thumb from current post thumbnail
			if ( has_post_thumbnail() ) :
				$thumb = get_the_post_thumbnail(get_the_ID(), 'gallery-thumbnail');

			//If no post thumbnail get our user set default
			elseif ( ( isset( $options['placeholder'] ) &&  ( $options['placeholder'] !== '' ) ) ) :
				$thumb = $options['placeholder'];
			
			//If no user set default use the plugin default	
			else :
				$thumb = '<img src="'. $mojoURL . '/images/default.jpg" />';
			endif;
			
			echo $thumb;
		}
		
		/**
		 * archive_title function.
		 *
		 * Variable archive title based on user settings
		 * 
		 * @access public
		 * @return void
		 * @since 0.4
		 */
		function archive_title() {
			$options = get_option( 'mojoGallery_options' );
			
			if ( isset( $options['archive_title'] ) && ( $options['archive_title'] != '' ) ) :
				$archive_title = $options['archive_title'];
			else :
				$archive_title = 'Gallery';
			endif;
			
			echo $archive_title;
		}
		
		/**
		 * column_count function.
		 *
		 * Allows user to set number of columns to show in grid view.
		 * 
		 * @access public
		 * @return void
		 * @since 0.4
		 */
		function column_count() {
			$options = get_option( 'mojoGallery_options' );
			
			if ( isset( $options['columns'] ) && ( $options['columns'] != '' ) ) :
				$column_count = $options['columns'];
			else:
				$column_count = 4;
			endif;
			
			return $column_count;
		}
		
		/**
		 * album_permalink function.
		 *
		 * echos our albums permalink for use on the child page list.
		 * 
		 * @access public
		 * @return void
		 * @since 0.4
		 */
		function album_permalink() {
			global $album;
			
			$album_permalink = get_permalink( $album->ID );
			echo $album_permalink;
		}
		
	} //end class

endif; //end class if
