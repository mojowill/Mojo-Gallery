<?php
/*
Plugin Name: The Mojo Gallery
Plugin URI: http://www.mojowill.com/developer/mojo-gallery-plugin/
Description: A small gallery plugin using the built in media uploader and gallery shortcodes. THIS IS A WORK IN PROGRESS!
Version: 0.4.1
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
 * Define Global Variables
 */
$mojoURI = dirname( __FILE__ );
$mojoURL = plugins_url( '', __FILE__ );

/**
 * Bring in the classes
 */
 
require_once( dirname( __FILE__ ) . '/includes/mojo-gallery.class.php' );
require_once( dirname( __FILE__ ) . '/includes/mojo-gallery-options.class.php' ); 

/**
 * Start the Plugin
 */
 
$mojoGallery = new mojoGallery();