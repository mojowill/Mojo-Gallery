<?php
/*
Plugin Name: The Mojo Gallery
Plugin URI: http://www.mojowill.com/
Description: A small gallery plugin using the built in media uploader and gallery shortcodes
Version: 1.0
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
		        'name' => _x( 'Albums', 'mojo-gallery' ),
		        'singular_name' => _x( 'Album', 'mojo-gallery' ),
		        'add_new' => _x( 'Add New', 'mojo-gallery' ),
		        'add_new_item' => _x( 'Add New Album', 'mojo-gallery' ),
		        'edit_item' => _x( 'Edit Album', 'mojo-gallery' ),
		        'new_item' => _x( 'New Album', 'mojo-gallery' ),
		        'view_item' => _x( 'View Album', 'mojo-gallery' ),
		        'search_items' => _x( 'Search Albums', 'mojo-gallery' ),
		        'not_found' => _x( 'No albums found', 'mojo-gallery' ),
		        'not_found_in_trash' => _x( 'No albums found in Trash', 'mojo-gallery' ),
		        'parent_item_colon' => _x( 'Parent Album:', 'mojo-gallery' ),
		        'menu_name' => _x( 'Mojo Gallery', 'mojo-gallery' ),
		    );
		
		    $args = array( 
		        'labels' => $labels,
		        'hierarchical' => true,
		        
		        'supports' => array( 'title', 'editor', 'thumbnail', 'post-formats' ),
		        'taxonomies' => array( 'custom-tax' ),
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
		
	} //end class

endif; //end class if

/**
 * Start the Plugin
 */
 
$mojoGallery = new mojoGallery();