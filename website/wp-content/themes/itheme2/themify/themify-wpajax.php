<?php
/**
 * @author Elio Rivero
 * @package themify
 * @since 1.2.2
 * 
 * ----------------------------------------------------------------------
 * 					DO NOT EDIT THIS FILE
 * ----------------------------------------------------------------------
 * 				AJAX functions to:
 *	 				Save Settings
 *	 				Get Settings
 * 					Reset Settings
 * 					Reset Styling
 * 					Import / Export
 * 					Upload Images
 * 					Upload Files
 * 
 *  			http://themify.me
 *  			Copyright (C) 2011 Themify
 *
 ***************************************************************************/

// Initialize actions
add_action('admin_head', 'themify_plupload_admin_head');
add_action('delete_attachment', 'themify_delete_attachment');
$themify_ajax_actions = array(
	'plupload',
	'delete_preset',
	'remove_post_image',
	'save',
	'reset_styling',
	'reset_setting',
	'pull'
);
foreach($themify_ajax_actions as $action){
	add_action('wp_ajax_themify_' . $action, 'themify_' . $action);
}
add_action('added_post_meta', 'themify_after_post_meta', 10, 4);
add_action('updated_postmeta', 'themify_after_post_meta', 10, 4);
add_action('deleted_post_meta', 'themify_deleted_post_meta', 10, 4);

/**
 * Initialize Plupload parameters
 * @since 1.2.2
 * @package themify
 */
function themify_plupload_admin_head() {
// place js config array for plupload
	$plupload_init = array(
	    'runtimes'				=> 'html5,flash,silverlight,html4',
	    'browse_button'			=> 'plupload-browse-button', // adjusted by uploader
	    'container' 			=> 'plupload-upload-ui', // adjusted by uploader
	    'drop_element' 			=> 'drag-drop-area', // adjusted by uploader
	    'file_data_name' 		=> 'async-upload', // adjusted by uploader
	    'multiple_queues' 		=> true,
	    'max_file_size' 		=> wp_max_upload_size() . 'b',
	    'url' 					=> admin_url('admin-ajax.php'),
	    'flash_swf_url' 		=> includes_url('js/plupload/plupload.flash.swf'),
	    'silverlight_xap_url' 	=> includes_url('js/plupload/plupload.silverlight.xap'),
	    'filters' 				=> array( array(
	    	'title' => __('Allowed Files', 'themify'),
	    	'extensions' => 'jpg,gif,png,zip,txt')
		),
	    'multipart' 			=> true,
	    'urlstream_upload' 		=> true,
	    'multi_selection' 		=> false, // added by uploader
	     // additional post data to send to our ajax hook
	    'multipart_params' 		=> array(
	        '_ajax_nonce' 		=> '', // added by uploader
	        'action' 			=> 'themify_plupload', // the ajax action name
	        'imgid' 			=> 0 // added by uploader
	    )
	);
	?>
	<script type="text/javascript">
	    var global_plupload_init=<?php echo json_encode($plupload_init); ?>;
	</script>
	<?php
}

/**
 * AJAX - Plupload execution routines
 * @since 1.2.2
 * @package themify
 */
function themify_plupload() {
    $imgid = $_POST["imgid"];
    check_ajax_referer($imgid . 'themify-plupload');
	/** Check whether this image should be set as a preset. @var String */
	$haspreset = $_POST['haspreset'];
	/** Decide whether to send this image to Media. @var String */
	$add_to_media_library = $_POST['tomedia'];
	/** If post ID is set, uploaded image will be attached to it. @var String */
	$postid = $_POST['topost'];
 
    /** Handle file upload storing file|url|type. @var Array */
    $file = wp_handle_upload($_FILES[$imgid . 'async-upload'], array('test_form' => true, 'action' => 'themify_plupload'));
	//let's see if it's an image, a zip file or something else
	$ext = explode('/', $file['type']);
	
	// Import routines
	if( 'zip' == $ext[1] || 'rar' == $ext[1] || 'plain' == $ext[1] ){
		
		$url = wp_nonce_url('admin.php?page=themify');
		$upload_dir = wp_upload_dir();
		
		if (false === ($creds = request_filesystem_credentials($url) ) ) {
			return true;
		}
		if ( ! WP_Filesystem($creds) ) {
			request_filesystem_credentials($url, '', true);
			return true;
		}
		
		global $wp_filesystem;
		
		if( 'zip' == $ext[1] || 'rar' == $ext[1] ) {
			unzip_file($file['file'], THEME_DIR);
			if( $wp_filesystem->exists( THEME_DIR . '/data_export.txt' ) ){
				$data = $wp_filesystem->get_contents( THEME_DIR . '/data_export.txt' );
				themify_set_data(unserialize($data));
				$wp_filesystem->delete(THEME_DIR . '/data_export.txt');
				$wp_filesystem->delete($file['file']);
			} else {
				_e('Data could not be loaded', 'themify');
			}
		} else {
			if( $wp_filesystem->exists( $file['file'] ) ){
				$data = $wp_filesystem->get_contents( $file['file'] );
				themify_set_data(unserialize($data));
				$wp_filesystem->delete($file['file']);
			} else {
				_e('Data could not be loaded', 'themify');
			}
		}
		
	} else {
		//Image Upload routines
		if( 'tomedia' == $add_to_media_library ){
			
			// Insert into Media Library
			// Set up options array to add this file as an attachment
	        $attachment = array(
	            'post_mime_type' => sanitize_mime_type($file['type']),
	            'post_title' => str_replace('-', ' ', sanitize_file_name(pathinfo($file['file'], PATHINFO_FILENAME))),
	            'post_status' => 'inherit'
	        );
			
			if( $postid ){
				$attach_id = wp_insert_attachment( $attachment, $file['file'], $postid );
			} else {
				$attach_id = wp_insert_attachment( $attachment, $file['file'] );
			}

			// Common attachment procedures
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		    $attach_data = wp_generate_attachment_metadata( $attach_id, $file['file'] );
		    wp_update_attachment_metadata($attach_id, $attach_data);
			
			if( $postid ) {
				
				$full = wp_get_attachment_image_src( $attach_id, 'full' );
				
				if( $_POST['featured'] ){
					//Set the featured image for the post
					set_post_thumbnail($postid, $attach_id);
				}
				update_post_meta($postid, $_POST['fields'], $full[0]);
				update_post_meta($postid, '_'.$_POST['fields'] . '_attach_id', $attach_id);
				
				$thumb = wp_get_attachment_image_src( $attach_id, 'thumbnail' );
				
				//Return URL for the image field in meta box
				$file['thumb'] = $thumb[0];
				
			}
		}
		/**
		 * Presets like backgrounds and such
		 */
		if( 'haspreset' == $haspreset ){
			// For the sake of predictability, we're not adding this to Media.
			$presets = get_option('themify_background_presets');
			$presets[ $file['file'] ] = $file['url'];
			update_option('themify_background_presets', $presets);
			
			/*$presets_attach_id = get_option('themify_background_presets_attach_id');
			$presets_attach_id[ $file['file'] ] = $attach_id;
			update_option('themify_background_presets_attach_id', $presets_attach_id);*/
		}
		
	}
	$file['type'] = $ext[1];
	// send the uploaded file url in response
	echo json_encode($file);
    exit;
}

/**
 * Sync post thumbnail and post_image field
 */
function themify_after_post_meta( $meta_id, $post_id, $meta_key, $meta_value ) {
    if ( '_thumbnail_id' == $meta_key ) {
        $attach_id = get_post_thumbnail_id($post_id);
		$full = wp_get_attachment_image_src( $attach_id, 'full' );
		set_post_thumbnail($post_id, $attach_id);
		update_post_meta($post_id, 'post_image', $full[0]);
    }
}
/**
 * Delete post meta if post thumbnail was deleted
 */
function themify_deleted_post_meta( $deleted_meta_ids, $post_id, $meta_key, $only_delete_these_meta_values ){
    if ( '_thumbnail_id' == $meta_key ) {
    	delete_post_meta($post_id, 'post_image');
    }
}

/**
 * AJAX - Delete preset image
 * @since 1.2.2
 * @package themify
 */
function themify_delete_preset(){
	check_ajax_referer( 'ajax-nonce', 'nonce' );
	
	if( isset($_POST['file']) ){
		$file = $_POST['file'];
		$presets = get_option('themify_background_presets');
		
		if(file_exists(THEME_DIR . '/uploads/bg/' . $file)){
			// It's one of the presets budled with the theme
			unlink(THEME_DIR . '/uploads/bg/' . $file);
			echo 'Deleted ' . THEME_DIR . '/uploads/bg/' . $file;
		} else {
			// It's one of the presets uploaded by user to media
			$presets_attach_id = get_option('themify_background_presets_attach_id');
			//wp_delete_attachment($presets_attach_id[stripslashes($file)], true);
			@ unlink(stripslashes($file));
			unset($presets_attach_id[stripslashes($file)]);
			update_option('themify_background_presets_attach_id', $presets_attach_id);
		}
		unset($presets[ stripslashes($file) ]);
		update_option('themify_background_presets', $presets);
	}
	die();
}

/**
 * When user deletes image from gallery, it will delete the post_image custom field.
 * @since 1.2.2
 * @package themify
 */
function themify_delete_attachment($attach_id){
	$attdata = get_post(get_post_thumbnail_id($post->ID));
	delete_post_meta($attdata->post_parent, 'post_image');
}

/**
 * AJAX - Remove image assigned in Themify custom panel. Clears post_image and _thumbnail_id field.
 * @since 1.1.5
 * @package themify
 */
function themify_remove_post_image(){
	check_ajax_referer( 'themify-custom-panel', 'nonce' );
	$attach_id = isset($_POST['attach_id'])? $_POST['attach_id'] : get_post_thumbnail_id($_POST['postid']);
	if( isset($_POST['postid']) && isset($_POST['customfield'])){
		// Delete image from Media
		wp_delete_attachment($attach_id, true);
		
		// Clear Themify custom field for post image
		update_post_meta($_POST['postid'], $_POST['customfield'], '');
		 
		// Clear hidden custom field
		update_post_meta($_POST['postid'], '_thumbnail_id', array());
	} else {
		_e('Missing vars: post ID and custom field.', 'themify');
	}
	die();
}

/**
 * AJAX - Save user settings
 * @since 1.1.3
 * @package themify
 */
function themify_save(){
	check_ajax_referer( 'ajax-nonce', 'nonce' );
	$data = explode("&", $_POST['data']);
	$temp = array();
	foreach($data as $a){
		$v = explode("=", $a);
		$temp[$v[0]] = urldecode( str_replace("+"," ",preg_replace('/%([0-9a-f]{2})/ie', "chr(hexdec('\\1'))", urlencode($v[1]))) );
	}
	themify_set_data($temp);
	_e('Your settings were saved', 'themify');
	die();
}

/**
 * AJAX - Reset Styling
 * @since 1.1.3
 * @package themify
 */
function themify_reset_styling(){
	check_ajax_referer( 'ajax-nonce', 'nonce' );
	$data = explode("&", $_POST['data']);
	$temp_data = array();
	foreach($data as $a){
		$v = explode("=", $a);
		$temp_data[$v[0]] = str_replace("+"," ",preg_replace('/%([0-9a-f]{2})/ie', "chr(hexdec('\\1'))", $v[1]));
	}
	$temp = array();
	foreach($temp_data as $key => $val){
		if(strpos($key, 'styling') === false){
			$temp[$key] = $val;
		}
	}
	print_r(themify_set_data($temp));
	die();
}

/**
 * AJAX - Reset Settings
 * @since 1.1.3
 * @package themify
 */
function themify_reset_setting(){
	check_ajax_referer( 'ajax-nonce', 'nonce' );
	$data = explode("&", $_POST['data']);
	$temp_data = array();
	foreach($data as $a){
		$v = explode("=", $a);
		$temp_data[$v[0]] = str_replace("+"," ",preg_replace('/%([0-9a-f]{2})/ie', "chr(hexdec('\\1'))", $v[1]));
	}
	$temp = array();
	foreach($temp_data as $key => $val){
		if(strpos($key, 'setting') === false){
			$temp[$key] = $val;
		}
	}
	print_r(themify_set_data($temp));
	die();
}

/**
 * Export Settings to zip file and prompt to download
 * NOTE: This function is not called through AJAX but it is kept here for consistency. 
 * @since 1.1.3
 * @package themify
 */
function themify_export($location, $status) {
	if ( isset($_GET['export']) ) {
		check_admin_referer( 'themify_export_nonce' );
		global $theme;
		$theme_name = $theme->display('Name');
		if(class_exists('ZipArchive')){
			$theme_name_lc = strtolower($theme_name);
			$datafile = 'data_export.txt';
			$handler = fopen($datafile, 'w');
			fwrite($handler,serialize(themify_get_data()));
			fclose($handler);
			$files_to_zip = array(
				'../wp-content/themes/' . $theme_name_lc . '/custom-modules.php',
				'../wp-content/themes/' . $theme_name_lc . '/custom-functions.php',
				'../wp-content/themes/' . $theme_name_lc . '/custom-config.xml',
				$datafile
			);
			//print_r($files_to_zip);
			$file = $theme_name . '_themify_export_' . date('Y_m_d') . '.zip';
			$result = themify_create_zip( $files_to_zip, $file, true );
			if($result){
				if((isset($file))&&(file_exists($file))){
					ob_start();
					header('Pragma: public');
					header('Expires: 0');
					header("Content-type: application/force-download");
					header('Content-Disposition: attachment; filename="' . $file . '"');
					header("Content-Transfer-Encoding: Binary"); 
					header("Content-length: ".filesize($file));
					header('Connection: close');
					ob_clean();
					flush(); 
					readfile($file);
					unlink($datafile);
					unlink($file);
					exit();
				} else {
					return false;
				}
			}
		} else {
			if(ini_get('zlib.output_compression')) {
				ini_set('zlib.output_compression', 'Off');
			}
			ob_start();
			header('Content-Type: application/force-download');
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private',false);
			header('Content-Disposition: attachment; filename="'.$theme_name.'_themify_export_'.date("Y_m_d").'.txt"');
			header('Content-Transfer-Encoding: binary');
			ob_clean();
			flush();
			echo serialize(themify_get_data());
			exit();
		}
	}
	return;
}
add_action('after_setup_theme', 'themify_export', 10, 2);

/**
 * Pull data for inspection
 * @since 1.1.3
 * @package themify
 */
function themify_pull(){
	print_r(themify_get_data());
	die();
}

?>