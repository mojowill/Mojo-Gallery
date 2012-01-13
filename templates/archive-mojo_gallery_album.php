			<?php get_header(); ?>
			
			<div id="content3">
				<?php 
				
				$counter = 1; //start the counter
				
				global $query_string;
				
				query_posts($query_string . '&ignore_sticky_posts=1&posts_per_page=12&post_parent=0'); ?>
				
				<h1><?php $mojoGallery->archive_title();?></h1>

				<?php if (have_posts()) : ?>
				
					<div id="mojoGallery">
		
	            		<?php while (have_posts() ) : the_post();
		            									
							//show left hand column
							if ( $counter != $mojoGallery->column_count() ) : ?>
								<div class="griditemleft">
									<div class="postimage">
										<a href="<?php the_permalink();?>"><?php $mojoGallery->default_thumbnails();?></a>
									</div>
									<h2><a href="<?php the_permalink();?>"><?php echo get_the_title();?></a></h2>
								</div>
								
							<?php
							//show the right hand column
							elseif ( $counter == $mojoGallery->column_count() ) : ?>
							
								<div class="griditemright">
									<div class="postimage">
										<a href="<?php the_permalink();?>"><?php $mojoGallery->default_thumbnails();?></a>
									</div>
									<h2><a href="<?php the_permalink();?>"><?php echo get_the_title();?></a></h2>								
								</div>
								
								<div class="clear"></div>
							
							<?php $counter = 0;
							
							endif;
	
						$counter++;
						
						endwhile; ?>
					</div>
                
                <?php else : ?>
                
    				<p><?php echo _e( 'Sorry no albums found.', 'mojo-gallery' );?></p>
                
                
              	<?php endif; ?>
                			
			</div>
			
			<?php get_footer(); ?>