			<?php get_header();?>
			
			<div id="content3">
				
				<?php if (have_posts()) : ?>
				<div class="fullcol">
            		<?php while (have_posts()) : the_post();
            			
            			//Lets get some info about our parent post
            			$parent_title = get_the_title($post->post_parent);
            			
            			//Get options
            			$options = get_option( 'mojoGallery_options' ); 
            			//If we have a parent post modify the link/title to include it
            			if ($post->post_parent ) : ?>
            			
            				<h1><a href="<?php echo get_post_type_archive_link( 'mojo-gallery-album' );?>"><?php echo _e( 'Gallery', 'mojo-gallery' );?></a> &raquo; <a href="<?php echo get_permalink($post->post_parent);?>"><?php echo $parent_title;?></a> &raquo; <?php the_title();?></h1>			
						
						<?php else : ?>
					
							<h1><a href="<?php echo get_post_type_archive_link( 'mojo-gallery-album');?>"><?php echo _e( 'Gallery', 'mojo-gallery');?></a> &raquo; <?php the_title();?></h1>						
						<?php endif;
					
						//If we have no content and we have children list them, we need to check here for out own injection of the shortcode!
						if ( ( get_the_content() == '' ) && ( ! $mojoGallery->has_attachements() ) ) :
							
							$args = array(
								'post_type' => 'gallery',
								'child_of' => $post->ID,
								'parent' => $post->ID,
							);
							
							$albums = get_pages($args);
							
							$counter = 1; //start our counter
							$grids = $options['columns']; //images per row should be the same as archive-gallery.php
							?>
							
							<div id="gridcontainer">
							
							<?php foreach ( $albums as $album ) {

								$permalink = get_permalink( $album->ID );
																
								//show left hand column
								if ( $counter != $grids ) : ?>
									<div class="griditemleft">
										<div class="postimage">
											<a href="<?php echo $permalink;?>"><?php echo $mojoGallery->default_thumbnails();?></a>
										</div>
										<h2><a href="<?php echo $permalink;?>"><?php echo $album->post_title;?></a></h2>
									</div>
									
								<?php
								//show the right hand column
								elseif ( $counter == $grids ) : ?>
								
									<div class="griditemright">
										<div class="postimage">
											<a href="<?php echo $permalink;?>"><?php echo $mojoGallery->default_thumbnails();?></a>
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
					
					
					endwhile; ?>
				</div>	                   
                
                <?php endif; ?>
                			
			</div>
			
			<?php get_footer(); ?>