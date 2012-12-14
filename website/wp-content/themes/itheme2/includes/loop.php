<?php if(!is_single()) { global $more; $more = 0; } //enable more link ?>
<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php themify_post_before(); //hook ?>
<div id="post-<?php the_ID(); ?>" <?php post_class("post clearfix " . $themify->get_categories_as_classes(get_the_ID())); ?>>
	<?php themify_post_start(); //hook ?>
	
	<?php
	if( $post_image = themify_get_image($themify->auto_featured_image . $themify->image_setting . "w=".$themify->width."&h=".$themify->height) ){
		if($themify->hide_image != 'yes'): ?>
			<div class="post-image <?php echo $themify->image_align; ?>">
				<?php if( 'yes' == $themify->unlink_image): ?>
					<?php echo $post_image; ?>
				<?php else: ?>
					<a href="<?php echo $themify->get_featured_image_link(); ?>"><?php echo $post_image; ?></a>
				<?php endif; ?>
			</div>
		<?php endif; //post image
	} ?>

	<div class="post-content">
		<?php if($themify->hide_date != "yes"): ?>
			<p class="post-date">
				<span class="month"><?php the_time('M') ?></span>
				<span class="day"><?php the_time('j') ?></span>
				<span class="year"><?php the_time('Y') ?></span>
			</p>
		<?php endif; ?>
	
		<?php if($themify->hide_title != "yes"): ?>

			<?php if(is_single()): ?>
				<?php if($themify->unlink_title == "yes"): ?>
					<h1 class="post-title"><?php the_title(); ?></h1>
				<?php else: ?>
					<h1 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<?php endif; //unlink post title ?> 
			<?php else: ?>
				<?php if($themify->unlink_title == "yes"): ?>
					<h2 class="post-title"><?php the_title(); ?></h2>
				<?php else: ?>
					<h2 class="post-title"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<?php endif; //unlink post title ?> 
			<?php endif; ?>

		<?php endif; ?>    
		
		<?php if($themify->display_content == 'excerpt'): ?>
			<?php the_excerpt(); ?>
		<?php elseif($themify->display_content == 'none'): ?>
		<?php else: ?>
			<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>
		<?php endif; ?>
	
		<?php if($themify->hide_meta != 'yes'): ?>
			<p class="post-meta"> 
				<span class="post-author"><?php _e( 'By', 'themify' ); ?> <?php the_author_posts_link(); ?>  &bull;</span>
				<span class="post-category"><?php the_category(', ') ?>  &bull;</span>
				<?php  if( !themify_get('setting-comments_posts') && comments_open() ) : ?>
					<span class="post-comment"><?php comments_popup_link('0', '1', '%'); ?></span>
				<?php endif; //post comment ?>
				<?php the_tags(__(' <span class="post-tag">&bull; Tags: ','themify'), ', ', '</span>'); ?>
			</p>
		<?php endif; ?>    

		<?php edit_post_link(__('Edit', 'themify'), '[', ']'); ?>

	</div>
	<!-- /.post-content -->		
	
    <?php themify_post_end(); //hook ?>
</div>
<!--/post -->
<?php themify_post_after(); //hook ?>