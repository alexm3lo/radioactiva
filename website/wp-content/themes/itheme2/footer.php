<?php
/** Themify Default Variables
 @var object */
	global $themify; ?>
	
	<?php themify_layout_after(); //hook ?>
	</div>
	<!--/body --> 

	<div id="footerwrap">
	
    	<?php themify_footer_before(); //hook ?>
		<div id="footer" class="pagewidth clearfix">
        	<?php themify_footer_start(); //hook ?>

			<?php get_template_part( 'includes/footer-widgets'); ?>
		
			<?php if (function_exists('wp_nav_menu')) {
				wp_nav_menu(array('theme_location' => 'footer-nav' , 'fallback_cb' => '' , 'container'  => '' , 'menu_id' => 'footer-nav' , 'menu_class' => 'footer-nav')); 
			} ?>
	
			<div class="footer-text">
				<?php
				if(themify_get('setting-footer_text_left') != "") {
					echo themify_get('setting-footer_text_left');
				}
				else {
					echo '&copy; <a href="'.home_url().'">'.get_bloginfo('name').'</a> '.date('Y') . '<br />';
				} ?>
				<?php
				if(themify_get('setting-footer_text_right') != "") {
					echo themify_get('setting-footer_text_right');
				} else {
					echo apply_filters('themify_footer_text_two', 'Powered by <a href="http://wordpress.org">WordPress</a> &bull; <a href="http://themify.me">Themify WordPress Themes</a>');
				} ?>
			</div>
			<!--/footer-text -->
			<?php themify_footer_end(); //hook ?>
		</div>
		<!--/footer --> 
        <?php themify_footer_after(); //hook ?>
        
    </div>
    <!--/footerwrap -->
</div>
<!--/pagewrap -->

<?php
/**
 *  Stylesheets and Javascript files are enqueued in theme-functions.php
 */
?>

<!-- wp_footer -->
<?php wp_footer(); ?>

<?php themify_body_end(); //hook ?>
</body>
</html>