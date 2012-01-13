			<?php get_header(); ?>
			
			<div id="content3">
				<?php 
				//Get Options
				$options = get_option( 'mojoGallery_options' );
				
				$counter = 1; //start the counter
				$grids = $options['columns']; //images per row shouldn't go above 4 really
				
				global $query_string;
				
				query_posts($query_string . '&ignore_sticky_posts=1&posts_per_page=12&post_parent=0');
				
				if (have_posts()) : ?>
				
				<div class="fullcol">
				<h1><?php echo _e( 'Gallery', 'mojo-gallery' );?></h1>
				<div id="gridcontainer">

            		<?php while (have_posts() ) : the_post();
            			
            			//Get Thumbnails
						
						
						//show left hand column
						if ( $counter != $grids ) : ?>
							<div class="griditemleft">
								<div class="postimage">
									<a href="<?php the_permalink();?>"><?php echo $mojoGallery->default_thumbnails();
;?></a>
								</div>
								<h2><a href="<?php the_permalink();?>"><?php echo get_the_title();?></a></h2>
							</div>
							
						<?php
						//show the right hand column
						elseif ( $counter == $grids ) : ?>
						
							<div class="griditemright">
								<div class="postimage">
									<a href="<?php the_permalink();?>"><?php echo $mojoGallery->default_thumbnails();?></a>
								</div>
								<h2><a href="<?php the_permalink();?>"><?php echo get_the_title();?></a></h2>								
							</div>
							
							<div class="clear"></div>
						
						<?php
						$counter = 0;
						
						endif;

					$counter++;
					
					endwhile; ?>
					</div>
				</div>	                   
                
                <?php endif; ?>
                			
			</div>
			
			<?php get_footer(); ?>