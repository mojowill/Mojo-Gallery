			<?php get_header(); ?>
			
			<div id="content3">
				
				<?php if (have_posts()) : ?>
				<div class="fullcol">
            		<?php while (have_posts()) : the_post();
            			
            			//Lets get some info about our parent post
            			$parent_title = get_the_title($post->post_parent);
            			
            			//If we have a parent post modify the link/title to include it
            			if ($post->post_parent ) : ?>
            			
            				<h1><a href="<?php echo home_url();?>/gallery">Gallery</a> &raquo; <a href="<?php echo get_permalink($post->post_parent);?>"><?php echo $parent_title;?></a> &raquo; <?php the_title();?></h1>			
						
						<?php else : ?>
					
							<h1><a href="<?php echo home_url();?>/gallery">Gallery</a> &raquo; <?php the_title();?></h1>						
						<?php endif;
					
						//If we have no content and we have children list them
						if ( get_the_content() == '' ) :
							
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
								
								if ( $thumb = get_the_post_thumbnail( $album->ID, 'gallery-thumbnail') == null ) :
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
					
					
					endwhile; ?>
				</div>	                   
                
                <?php endif; ?>
                			
			</div>
			
			<?php get_footer(); ?>