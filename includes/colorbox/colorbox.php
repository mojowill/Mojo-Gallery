<?php 
/**
 * Colorbox
 * 
 * Gives a nice lightbox style pop over on built in galleries when clicking on thumbnails.
 * Based on jQueryLightBoxForNativeGalleries by Viper007Bond http://profiles.wordpress.org/users/Viper007Bond/
 * 
 * When using the [gallery] shortcode the color box will automatically be added
 * if you want to use this on a single image just add class="colorbox".
 *
 * PHP Version 5 
 * 
 * @package MojoGallery
 * @author Will Wilson <will@mojowill.com>
 * @version 1.0
 * @since 0.1
 * @todo clean up paths
 */
/**
 * mojoColorboxForGalleries class.
 */
class mojoColorboxForGalleries {

	/**
	 * mojoColorboxForGalleries function.
	 * 
	 * @access public
	 * @return void
	 * @package Vtesse
	 */
	function mojoColorboxForGalleries() {
		
		add_action( 'wp_head', array( &$this, 'wp_head' ) );
		add_filter( 'attachment_link', array( &$this, 'attachment_link' ), 10, 2 );

		if ( !is_admin() ) :
			wp_enqueue_script( 'colorbox', plugins_url( '', __FILE__ ) . '/colorbox/jquery.colorbox-min.js', array( 'jquery' ), null, true );
			
			wp_register_style( 'colorbox-theme1', plugins_url( '', __FILE__ ) . '/colorbox/theme1/colorbox.css', array(), '1.3.14', 'screen' );
			wp_register_style( 'colorbox-theme2', plugins_url( '', __FILE__ ) . '/colorbox/theme2/colorbox.css', array(), '1.3.14', 'screen' );
			wp_register_style( 'colorbox-theme3', plugins_url( '', __FILE__ ) . '/colorbox/theme3/colorbox.css', array(), '1.3.14', 'screen' );
			wp_register_style( 'colorbox-theme4', plugins_url( '', __FILE__ ) . '/colorbox/theme4/colorbox.css', array(), '1.3.14', 'screen' );
			wp_register_style( 'colorbox-theme5', plugins_url( '', __FILE__ ) . '/colorbox/theme5/colorbox.css', array(), '1.3.14', 'screen' );

		
			
			$options = get_option( 'mojoGallery_options' ); 
			
			wp_enqueue_style( 'colorbox-' . $options['theme'] );
		
		endif;

		
		}


	/**
	 * wp_head function.
	 * 
	 * @access public
	 * @return void
	 * @since 1.0
	 */
	function wp_head() { ?>
<script type="text/javascript">
// <![CDATA[
	jQuery(document).ready(function($){
		$(".gallery").each(function(index, obj){
			var galleryid = Math.floor(Math.random()*10000);
			$(obj).find("a").colorbox({rel:galleryid, maxWidth:"95%", maxHeight:"95%"});
		});
		$("a.colorbox").colorbox({maxWidth:"95%", maxHeight:"95%"});
	});
// ]]>
</script>
<?php
	}


	/**
	 * attachment_link function.
	 * 
	 * @access public
	 * @param mixed $link
	 * @param mixed $id
	 * @return void
	 * @since 1.0
	 */
	function attachment_link( $link, $id ) {
		
		// The lightbox doesn't function inside feeds obviously, so don't modify anything
		if ( is_feed() || is_admin() )
			return $link;

		/**
		 * post
		 * 
		 * (default value: get_post( $id ))
		 * 
		 * @var mixed
		 * @access public
		 */
		$post = get_post( $id );

		if ( 'image/' == substr( $post->post_mime_type, 0, 6 ) )
			return wp_get_attachment_url( $id );
		else
			return $link;
	}

}
// Start the plugin up
add_action( 'init', 'mojoColorboxForGalleries', 7 );

/**
 * mojoColorboxForGalleries function.
 * 
 * @access public
 * @return void
 * @since 1.0
 */
function mojoColorboxForGalleries() {
	global $mojoColorboxForGalleries;
	/**
	 * mojoColorboxForGalleries
	 * 
	 * (default value: new mojoColorboxForGalleries())
	 * 
	 * @var mixed
	 * @access public
	 * @since 1.0
	 */
	$mojoColorboxForGalleries = new mojoColorboxForGalleries();
}
