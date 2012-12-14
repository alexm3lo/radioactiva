<?php
/** Check if slider is enabled */
if('' == themify_get('setting-header_slider_enabled') || 'on' == themify_get('setting-header_slider_enabled')) { ?>

	<?php themify_slider_before(); //hook ?>
	<div id="header-slider" class="pagewidth slider">
    	<?php themify_slider_start(); //hook ?>
			
		<ul class="slides clearfix">

    		<?php 
			if(themify_check('setting-header_slider_posts_category')){
				$cat = "&cat=".themify_get('setting-header_slider_posts_category');	
			} else {
				$cat = "";
			}
			if(themify_check('setting-header_slider_posts_slides')){
				$num_posts = "showposts=".themify_get('setting-header_slider_posts_slides')."&";
			} else {
				$num_posts = "showposts=5&";	
			}
			if(themify_check('setting-header_slider_display') && themify_get('setting-header_slider_display') == "images"){ 
        		
				$options = array('one','two','three','four','five','six','seven','eight','nine','ten');
				foreach($options as $option){
					$option = 'setting-header_slider_images_'.$option;
					if(themify_check($option.'_image')){
						echo '<li>';
						
							$title = themify_check($option.'_title') ? themify_get($option.'_title') : '';
							$image = themify_get($option.'_image');
							$alt = $title? $title : $image;
							
							if(themify_check($option.'_link')) {
								$link = themify_get($option.'_link');
								$title_attr = $title? "title='$title'" : '';
								
								echo "<div class='slide-feature-image'><a href='$link' $title_attr>" . themify_get_image("src=$image&setting=header_slider&w=220&h=160&alt=$alt&class=feature-img") . '</a></div>';
								echo $title? '<div class="slide-content-wrap"><h3 class="slide-post-title"><a href="'.$link.'" '.$title_attr.' >'.$title.'</a></h3></div>' : '';
							} else {
								echo '<div class="slide-feature-image">' . themify_get_image("src=".$image."&setting=header_slider&w=220&h=160&alt=".$alt."&class=feature-img") . '</div>';
								echo $title? '<div class="slide-content-wrap"><h3 class="slide-post-title">'.$title.'</h3></div>' : '';
							}

						echo '</li>';
					}
				}
			} else { 

				
				query_posts($num_posts.$cat); 
				
				if( have_posts() ) {
					
					while ( have_posts() ) : the_post();
						?>                

					<?php if ( themify_get('external_link') != ''): ?>
						<?php $link = themify_get("external_link"); ?>
					<?php elseif ( themify_get('lightbox_link') != ''): ?>
						<?php $link = themify_get("lightbox_link").'" class="lightbox" rel="prettyPhoto[slider]'; ?>
					<?php else: ?>
						<?php $link = get_permalink(); ?>
					<?php endif; ?>

                    	<li>
                    		<div class='slide-feature-image'>
                    			<a href="<?php echo $link; ?>" title="<?php the_title_attribute(); ?>">
									<?php $auto_featured_image = themify_check('setting-auto_featured_image')? '': 'field_name=feature_image, post_image, image, wp_thumb&';
									themify_image($auto_featured_image."setting=header_slider&w=220&h=160&class=feature-img&alt=".get_the_title()); ?>
								</a>
							</div>
						<!-- /.slide-feature-image -->

						<div class="slide-content-wrap">

							<?php if(themify_get('setting-header_slider_hide_title') != 'yes'): ?>
								<h3 class="slide-post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
							<?php endif; ?>
							
							<?php if(themify_get('setting-header_slider_default_display') == 'content'): ?>
								<div class="slide-excerpt">
								<?php the_content(); ?>
								</div>
							<?php elseif( ! themify_get('setting-header_slider_default_display') || themify_get('setting-header_slider_default_display') == 'none'): ?>
									<?php //none ?>
							<?php else: ?>
								<div class="slide-excerpt">
								<?php the_excerpt(); ?>
								</div>
							<?php endif; ?>
								
						</div>
						<!-- /.slide-content-wrap -->
                        	
                 		</li>
               			<?php 
					endwhile; 
				}
				
				wp_reset_query();
				
			} 
			?>
		</ul>
        
	  	<?php themify_slider_end(); //hook ?>
	</div>
	<!-- /#slider -->
    <?php themify_slider_after(); //hook ?>
<?php } ?>