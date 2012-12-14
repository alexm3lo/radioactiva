<?php get_header(); ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php if(is_front_page() && !is_paged()){ get_template_part( 'includes/header-slider'); } ?>
			
<!-- layout-container -->
<div id="layout" class="clearfix">

	<?php themify_content_before(); //hook ?>
	<!-- content -->
	<div id="content" class="clearfix">
    	<?php themify_content_start(); //hook ?>
		
		<?php 
		/////////////////////////////////////////////
		// Author Page	 							
		/////////////////////////////////////////////
		?>
		<?php if(is_author()) : ?>
			<?php
			$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
			$author_url = $curauth->user_url;
			?>
			<div class="author-bio clearfix">
				<p class="author-avatar"><?php echo get_avatar( $curauth->user_email, $size = '48' ); ?></p>
				<h2 class="author-name"><?php _e('About','themify'); ?> <?php echo $curauth->first_name; ?> <?php echo $curauth->last_name; ?></h2>
				<?php if($author_url != ''): ?><p class="author-url"><a href="<?php echo $author_url; ?>"><?php echo $author_url; ?></a></p><?php endif; //author url ?>
				<div class="author-description">
					<?php echo $curauth->user_description; ?>
				</div>
				<!-- /.author-description -->
			</div>
			<!-- /.author bio -->
			
			<h2 class="author-posts-by"><?php _e('Posts by','themify'); ?> <?php echo $curauth->first_name; ?> <?php echo $curauth->last_name; ?>:</h2>
		<?php endif; ?>

		<?php 
		/////////////////////////////////////////////
		// Search Title	 							
		/////////////////////////////////////////////
		?>
		<?php if(is_search()): ?>
			<h1 class="page-title"><?php _e('Search Results for:','themify'); ?> <em><?php echo get_search_query(); ?></em></h1>
		<?php endif; ?>

		<?php 
		/////////////////////////////////////////////
		// Category Title	 							
		/////////////////////////////////////////////
		?>
		<?php if(is_category() || is_tag() || is_tax() ): ?>
			<h1 class="page-title"><?php single_cat_title(); ?></h1>
			<?php echo $themify->get_category_description(); ?>
		<?php endif; ?>

		<?php 
		/////////////////////////////////////////////
		// Default query categories	 							
		/////////////////////////////////////////////
		?>
		<?php $query_cats = themify_get('setting-default_query_cats'); ?>
		<?php if(($query_cats !="") && !is_search()): ?>
			<?php query_posts(apply_filters('themify_query_posts_args', $query_string . '&cat='.$query_cats)); ?>
		<?php endif; ?>

		<?php 
		/////////////////////////////////////////////
		// Loop	 							
		/////////////////////////////////////////////
		?>
		<?php if (have_posts()) : ?>
		
			<!-- loops-wrapper -->
			<div class="loops-wrapper">

				<?php while (have_posts()) : the_post(); ?>
		
					<?php if(is_search()): ?>
						<?php get_template_part( 'includes/loop','search'); ?>
					<?php else: ?>
						<?php get_template_part( 'includes/loop','index'); ?>
					<?php endif; ?>
		
				<?php endwhile; ?>
							
			</div>
			<!-- /loops-wrapper -->

			<?php get_template_part( 'includes/pagination'); ?>
		
		<?php 
		/////////////////////////////////////////////
		// Error - No Page Found	 							
		/////////////////////////////////////////////
		?>

		<?php else : ?>
	
			<p><?php _e( 'Sorry, nothing found.', 'themify' ); ?></p>
	
		<?php endif; ?>			

	</div>
  <!--/content -->
  <div id="fondo">
  </div> 
	
	<?php 
	/////////////////////////////////////////////
	// Sidebar							
	/////////////////////////////////////////////
	if ($themify->layout != "sidebar-none"): get_sidebar(); endif; ?>

	<?php themify_content_end(); //hook ?>
</div>
<!-- layout-container -->
<?php themify_content_after() //hook; ?>

<?php get_footer(); ?>
