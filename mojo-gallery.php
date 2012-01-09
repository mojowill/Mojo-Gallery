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

if ( ! class_exists( 'mojoGallery' ) ) :
	
	/**
	 * mojoGallery class.
	 */
	class mojoGallery {
		
		
		/**
		 * _root
		 * 
		 * (default value: null)
		 * 
		 * @var mixed
		 * @access protected
		 * @static
		 */
		static protected $_root = null;
		
		/**
		 * root function.
		 * 
		 * @access public
		 * @static
		 * @return void
		 */
		static public function root() {
			
			if ( is_null( self::$_root ) ) :
				self::$_root = dirname( __FILE__ );
			endif;
			
			return self::$_root;
		}
				
		/**
		 * __construct function.
		 *
		 * We run our actions in here.
		 * 
		 * @access public
		 * @return void
		 */
		function __construct() {
			
			add_action( 'init', array( &$this, 'register_cpt_mojo_album' ) );
			add_action( 'init', array( &$this, 'register_taxonomy_album_tag' ) );
			add_action( 'init', array( &$this, 'register_taxonomy_album_category' ) );
			
			add_filter( 'the_content', array( &$this, 'output_gallery' ) );

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
		        'taxonomies' => array( 'album-tag', 'album_category' ),
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
		        'name' => __( 'Album Tags', 'album tag' ),
		        'singular_name' => __( 'Album Tag', 'album tag' ),
		        'search_items' => __( 'Search Album Tags', 'album tag' ),
		        'popular_items' => __( 'Popular Album Tags', 'album tag' ),
		        'all_items' => __( 'All Album Tags', 'album tag' ),
		        'parent_item' => __( 'Parent Album Tag', 'album tag' ),
		        'parent_item_colon' => __( 'Parent Album Tag:', 'album tag' ),
		        'edit_item' => __( 'Edit Album Tag', 'album tag' ),
		        'update_item' => __( 'Update Album Tag', 'album tag' ),
		        'add_new_item' => __( 'Add New Album Tag', 'album tag' ),
		        'new_item_name' => __( 'New Album Tag Name', 'album tag' ),
		        'separate_items_with_commas' => __( 'Separate album tags with commas', 'album tag' ),
		        'add_or_remove_items' => __( 'Add or remove album tags', 'album tag' ),
		        'choose_from_most_used' => __( 'Choose from the most used album tags', 'album tag' ),
		        'menu_name' => __( 'Album Tags', 'album tag' ),
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
		        'name' => __( 'Album Categories', 'album category' ),
		        'singular_name' => __( 'Album Category', 'album category' ),
		        'search_items' => __( 'Search Album Categories', 'album category' ),
		        'popular_items' => __( 'Popular Album Categories', 'album category' ),
		        'all_items' => __( 'All Album Categories', 'album category' ),
		        'parent_item' => __( 'Parent Album Category', 'album category' ),
		        'parent_item_colon' => __( 'Parent Album Category:', 'album category' ),
		        'edit_item' => __( 'Edit Album Category', 'album category' ),
		        'update_item' => __( 'Update Album Category', 'album category' ),
		        'add_new_item' => __( 'Add New Album Category', 'album category' ),
		        'new_item_name' => __( 'New Album Category Name', 'album category' ),
		        'separate_items_with_commas' => __( 'Separate album categories with commas', 'album category' ),
		        'add_or_remove_items' => __( 'Add or remove album categories', 'album category' ),
		        'choose_from_most_used' => __( 'Choose from the most used album categories', 'album category' ),
		        'menu_name' => __( 'Album Categories', 'album category' ),
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
		 */
		function output_gallery() {
			global $post;
			
			if ( 'mojo-gallery-album' == get_post_type() && is_singular()  ) :
			
				if ( true !== strpos( $post->post_content, '[gallery' ) ) :
				
					echo do_shortcode('[gallery]');
				
				endif;
			
			endif;
		}
		
	} //end class

endif; //end class if

/**
 * Start the Plugin
 */
 
$mojoGallery = new mojoGallery();