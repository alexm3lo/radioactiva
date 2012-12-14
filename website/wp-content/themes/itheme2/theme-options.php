<?php
/**
 * Main Themify class
 * @package themify
 */
class Themify {
	/** Default sidebar layout
	 * @var string */
	public $layout;
	/** Default posts layout
	 * @var string */
	public $post_layout;
	
	public $hide_title;
	public $hide_meta;
	public $hide_date;
	public $hide_image;
	
	public $unlink_title;
	public $unlink_image;
	
	public $display_content = '';
	public $auto_featured_image;
	
	public $width = '';
	public $height = '';
	
	public $avatar_size = 96;
	public $page_navigation;
	public $posts_per_page;
	
	public $image_align = '';
	public $image_setting = '';
	
	public $query_category = '';
	public $paged = '';
	
	/////////////////////////////////////////////
	// Set Default Image Sizes 					
	/////////////////////////////////////////////
	
	// Default Index Layout
	static $content_width = 918;
	static $sidebar1_content_width = 600;
	
	// Default Single Post Layout
	static $single_content_width = 978;
	static $single_sidebar1_content_width = 670;
	
	// Default Single Image Size
	static $single_image_width = 908;
	static $single_image_height = 400;
	
	// Grid4
	static $grid4_width = 194;
	static $grid4_height = 140;
	
	// Grid3
	static $grid3_width = 272;
	static $grid3_height = 180;
	
	// Grid2
	static $grid2_width = 428;
	static $grid2_height = 250;
	
	// List Large
	static $list_large_image_width = 600;
	static $list_large_image_height = 390;
	 
	// List Thumb
	static $list_thumb_image_width = 200;
	static $list_thumb_image_height = 200;
	
	// List Grid2 Thumb
	static $grid2_thumb_width = 100;
	static $grid2_thumb_height = 90;
	
	// List Post
	static $list_post_width = 908;
	static $list_post_height = 400;
	
	function __construct() {
		
		///////////////////////////////////////////
		//Global options setup
		///////////////////////////////////////////
		$this->layout = themify_get('setting-default_layout');
		if($this->layout == '' ) $this->layout = 'sidebar1'; 
		
		$this->post_layout = themify_get('setting-default_post_layout');
		if($this->post_layout == '') $this->post_layout = 'list-post'; 
		
		$this->page_title = themify_get('setting-hide_page_title');
		$this->hide_title = themify_get('setting-default_post_title');
		$this->unlink_title = themify_get('setting-default_unlink_post_title');
		
		$this->hide_image = themify_get('setting-default_post_image');
		$this->unlink_image = themify_get('setting-default_unlink_post_image');
		$this->auto_featured_image = !themify_check('setting-auto_featured_image')? 'field_name=post_image, image, wp_thumb&' : '';
		
		$this->hide_meta = themify_get('setting-default_post_meta');
		$this->hide_date = themify_get('setting-default_post_date');
		
		$this->display_content = themify_get('setting-default_layout_display');
		$this->avatar_size = apply_filters('themify_author_box_avatar_size', 96);
		
		add_action('template_redirect', array(&$this, 'template_redirect'));
	}

	function template_redirect() {
		
		$post_image_width = themify_get('image_width');
		$post_image_height = themify_get('image_height');
		
		if( is_page() ) {
			$this->post_layout = (themify_get('layout') != "default" && themify_check('layout')) ?
									themify_get('layout') : themify_get('setting-default_post_layout');
			// set default post layout
			if($this->post_layout == '')
				$this->post_layout = 'list-post';
		}
		
		///////////////////////////////////////////
		// Setting image width, height
		///////////////////////////////////////////
		if($this->post_layout == 'grid4'):
		
			$this->width = self::$grid4_width;
			$this->height = self::$grid4_height;
		
		elseif($this->post_layout == 'grid3'):
		
			$this->width = self::$grid3_width;
			$this->height = self::$grid3_height;
		
		elseif($this->post_layout == 'grid2'):
		
			$this->width = self::$grid2_width;
			$this->height = self::$grid2_height;
			
		elseif($this->post_layout == 'list-large-image'):
		
			$this->width = self::$list_large_image_width;
			$this->height = self::$list_large_image_height;
		
		elseif($this->post_layout == 'list-thumb-image'):
		
			$this->width = self::$list_thumb_image_width;
			$this->height = self::$list_thumb_image_height;
		
		elseif($this->post_layout == 'grid2-thumb'):
		
			$this->width = self::$grid2_thumb_width;
			$this->height = self::$grid2_thumb_height;
			
		elseif($this->post_layout == 'list-post'):
		
			$this->width = self::$list_post_width;
			$this->height = self::$list_post_height;
		
		else:
					
			$this->width = self::$list_post_width;
			$this->height = self::$list_post_height;
			
		endif;
		
		if( is_page() ) {
			if(get_query_var('paged')):
				$this->paged = get_query_var('paged');
			elseif(get_query_var('page')):
				$this->paged = get_query_var('page');
			else:
				$this->paged = 1;
			endif;
			$this->query_category = themify_get('query_category');
			
			$this->layout = (themify_get('page_layout') != 'default' && themify_check('page_layout')) ? themify_get('page_layout') : themify_get('setting-default_page_layout');
			if($this->layout == '')
				$this->layout = 'sidebar1'; 
			
			$this->post_layout = (themify_get('layout') != 'default' && themify_check('layout')) ? themify_get('layout') : themify_get('setting-default_post_layout');
			if($this->post_layout == '')
				$this->post_layout = 'list-post';
			
			$this->page_title = (themify_get('hide_page_title') != 'default' && themify_check('hide_page_title')) ? themify_get('hide_page_title') : themify_get('setting-hide_page_title'); 
			$this->hide_title = themify_get('hide_title'); 
			$this->unlink_title = themify_get('unlink_title'); 
			$this->hide_image = themify_get('hide_image'); 
		    $this->unlink_image = themify_get('unlink_image'); 
			$this->hide_meta = themify_get('hide_meta'); 
			$this->hide_date = themify_get('hide_date'); 
			$this->display_content = themify_get('display_content');
			$this->post_image_width = themify_get('image_width'); 
			$this->post_image_height = themify_get('image_height'); 
			$this->page_navigation = themify_get('hide_navigation'); 
			$this->posts_per_page = themify_get('posts_per_page');
			
			if( '' != $post_image_height && '' != $post_image_width) {
				$this->width = $post_image_width;
				$this->height = $post_image_height;
			}
			
		}

		if( is_single() ) {
			$this->hide_title = (themify_get('hide_post_title') != 'default' && themify_check('hide_post_title')) ? themify_get('hide_post_title') : themify_get('setting-default_page_post_title');
			$this->unlink_title = (themify_get('unlink_post_title') != 'default' && themify_check('unlink_post_title')) ? themify_get('unlink_post_title') : themify_get('setting-default_page_unlink_post_title');
			$this->hide_date = (themify_get('hide_post_date') != 'default' && themify_check('hide_post_date')) ? themify_get('hide_post_date') : themify_get('setting-default_page_post_date');
			$this->hide_meta = (themify_get('hide_post_meta') != 'default' && themify_check('hide_post_meta')) ? themify_get('hide_post_meta') : themify_get('setting-default_page_post_meta');
			$this->hide_image = (themify_get('hide_post_image') != 'default' && themify_check('hide_post_image')) ? themify_get('hide_post_image') : themify_get('setting-default_page_post_image');
			$this->unlink_image = (themify_get('unlink_post_image') != 'default' && themify_check('unlink_post_image')) ? themify_get('unlink_post_image') : themify_get('setting-default_page_unlink_post_image');
			
			$this->layout = (themify_get('layout') == 'sidebar-none'
							|| themify_get('layout') == 'sidebar1'
							|| themify_get('layout') == 'sidebar1 sidebar-left'
							|| themify_get('layout') == 'sidebar2') ?
								themify_get('layout') : themify_get('setting-default_page_post_layout');
			 // set default layout
			 if($this->layout == '')
			 	$this->layout = 'sidebar1';
			
			$this->display_content = '';
			
			$this->post_image_width = themify_get('image_width');
			$this->post_image_height = themify_get('image_height');
			
			// Set Default Image Sizes for Single
			self::$content_width = self::$single_content_width;
			self::$sidebar1_content_width = self::$single_sidebar1_content_width;
			
			if( '' == $post_image_height && '' == $post_image_width){
				$this->width  = self::$single_image_width;
				$this->height = self::$single_image_height;
			} else {
				$this->width  = $post_image_width;
				$this->height = $post_image_height;
			}
		}
		
		if($this->layout == 'sidebar1' || $this->layout == 'sidebar1 sidebar-left') {
			$ratio = $this->width / self::$content_width;
			$aspect = $this->height / $this->width;
			$this->width = round($ratio * self::$sidebar1_content_width);
			if($this->height != '' && $this->height != 0)
				$this->height = round($this->width * $aspect);
		}
		
		if(is_single() && $this->hide_image != 'yes') {
			$this->image_align = themify_get('setting-image_post_single_align');
			$this->image_setting = 'setting=image_post_single&';
		} elseif($this->query_category != '' && $this->hide_image != 'yes') {
			$this->image_align = '';
			$this->image_setting = '';
		} else {
			$this->image_align = themify_get('setting-image_post_align');
			$this->image_setting = 'setting=image_post&';
		}
		
	}
	
	/**
	 * Returns post category IDs concatenated in a string
	 * @param number Post ID
	 * @return string Category IDs
	 */
	public function get_categories_as_classes($post_id){
		$categories = wp_get_post_categories($post_id);
		$class = '';
		foreach($categories as $cat)
			$class .= ' cat-'.$cat;
		return $class;
	}
	 
	/**
	 * Returns escaped URL for featured image link
	 * @return string 
	 */
	public function get_featured_image_link(){
		if ( themify_get('external_link') != ''):
			$link = esc_url(themify_get('external_link'));
		elseif ( themify_get('lightbox_link') != ''):
			$link = esc_url(themify_get('lightbox_link')) . '" class="lightbox" rel="prettyPhoto[post]';
		else:
			$link = get_permalink();
		endif;
		return $link;
	}
	 
	 /**
	  * Returns category description
	  * @return string
	  */
	 function get_category_description(){
	 	$category_description = category_description();
		if ( !empty( $category_description ) ){
			return '<div class="category-description">' . $category_description . '</div>';
		}
	 }
	 
	 /**
	  * Returns logo image
	  * @return string
	  */
	 function logo_image(){
		$html = '<a href="'.home_url().'" title="'.get_bloginfo('name').'">';
			$html .= themify_get_image("src=" . themify_get('setting-site_logo_image_value') . "&w=" . themify_get('setting-site_logo_width') . "&h=" . themify_get('setting-site_logo_height') . "&alt=" . urlencode(get_bloginfo('name')));
		$html .= '</a>';
		return apply_filters('themify_logo_image', $html);
	 }
}

/**
 * Initializes Themify class
 */
function themify_global_options(){
	/**
	 * Themify initialization class
	 */
	global $themify;
	$themify = new Themify();
}
add_action( 'init','themify_global_options' );

?>