			<?php get_header(); ?>
			
			<div id="content3">
				<?php $counter = 1; //start the counter
				$grids = 4; //images per row shouldn't go above 4 really
				
				global $query_string;
				
				query_posts($query_string . '&caller_get_posts=1&posts_per_page=12&post_parent=0');
				
				if (have_posts()) : ?>
				
				<div class="fullcol">
				<h1>Gallery</h1>
				<div id="gridcontainer">

            		<?php while (have_posts() ) : the_post();
            			
            			//Set Thumb
						if ( has_post_thumbnail() ) :
							$thumb = get_the_post_thumbnail(get_the_ID(), 'gallery-thumbnail');
						else :
							$thumb = '<img src="'. get_template_directory_uri() .'/images/default_thumb.jpg" />';
						endif;
						
						//show left hand column
						if ( $counter != $grids ) : ?>
							<div class="griditemleft">
								<div class="postimage">
									<a href="<?php the_permalink();?>"><?php echo $thumb;?></a>
								</div>
								<h2><a href="<?php the_permalink();?>"><?php echo get_the_title();?></a></h2>
							</div>
							
						<?php
						//show the right hand column
						elseif ( $counter == $grids ) : ?>
						
							<div class="griditemright">
								<div class="postimage">
									<a href="<?php the_permalink();?>"><?php echo $thumb;?></a>
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