			<?php get_header();?>
			
			<div id="content3">
				
				<?php if (have_posts()) : ?>
            		<?php while (have_posts()) : the_post();
            			
            			//Lets get some info about our parent post
            			$parent_title = get_the_title($post->post_parent);
            			
            			//If we have a parent post modify the link/title to include it
            			if ($post->post_parent ) : ?>
            			
            				<h1><a href="<?php echo get_post_type_archive_link( 'mojo-gallery-album' );?>"><?php $mojoGallery->archive_title();?></a> &raquo; <a href="<?php echo get_permalink($post->post_parent);?>"><?php echo $parent_title;?></a> &raquo; <?php the_title();?></h1>			
						
						<?php else : ?>
					
							<h1><a href="<?php echo get_post_type_archive_link( 'mojo-gallery-album');?>"><?php $mojoGallery->archive_title();?></a> &raquo; <?php the_title();?></h1>						
						<?php endif;
					
						//If we have no content and we have children list them, we need to check here for out own injection of the shortcode!
						if ( ( get_the_content() == '' ) || ( ! $mojoGallery->has_attachments() ) ) :
							
							$args = array(
								'post_type' => 'mojo-gallery-album',
								'child_of' => $post->ID,
								'parent' => $post->ID,
							);
							
							$albums = get_pages($args);
							$counter = 1; //start our counter
							?>
							
							<div id="mojoGallery">
							
							<?php foreach ( $albums as $album ) {
																
								//show left hand column
								if ( $counter != $mojoGallery->column_count() ) : ?>
									<div class="griditemleft">
										<div class="postimage">
											<a href="<?php $mojoGallery->album_permalink();?>"><?php $mojoGallery->default_thumbnails();?></a>
										</div>
										<h2><a href="<?php $mojoGallery->album_permalink();?>"><?php echo $album->post_title;?></a></h2>
									</div>
									
								<?php
								//show the right hand column
								elseif ( $counter == $mojoGallery->column_count() ) : ?>
								
									<div class="griditemright">
										<div class="postimage">
											<a href="<?php $mojoGallery->album_permalink();?>"><?php $mojoGallery->default_thumbnails();?></a>
										</div>
										<h2><a href="<?php $mojoGallery->album_permalink();?>"><?php echo $album->post_title;?></a></h2>								
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
				<?php else : ?>
				
					<p><?php echo _e( 'Sorry, no album found.', 'mojo-gallery' );?></p>
					
                <?php endif; ?>
                			
			</div>
			
			<?php get_footer(); ?>