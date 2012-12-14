jQuery(document).ready(function($) {
	
	if($('.plupload-upload-uic').length > 0) {
		$('.plupload-upload-uic').each(function() {
			var $this = $(this);
			var id1 = $this.attr("id");
			var imgId = id1.replace("plupload-upload-ui", "");
			var haspreset = false;
			var tomedia = false;
			var topost = false;
			
			pconfig = JSON.parse(JSON.stringify(global_plupload_init));
			pconfig["browse_button"] = imgId + pconfig["browse_button"];
			pconfig["container"] = imgId + pconfig["container"];
			pconfig["drop_element"] = imgId + pconfig["drop_element"];
			pconfig["file_data_name"] = imgId + pconfig["file_data_name"];
			pconfig["multipart_params"]["imgid"] = imgId;
			pconfig["multipart_params"]["_ajax_nonce"] = $this.find(".ajaxnonceplu").attr("id").replace("ajaxnonceplu", "");
			
			if($this.hasClass('add-preset')) {
				haspreset = true;
				pconfig["multipart_params"]['haspreset'] = 'haspreset'; 
			}
			if($this.hasClass('add-to-media')){
				tomedia = true;
				pconfig["multipart_params"]['tomedia'] = 'tomedia';
			}
			if($this.data('postid')) {
				topost = true;
				pconfig["multipart_params"]['topost'] = $this.data('postid');
			}
			if($this.data('fields')) {
				pconfig["multipart_params"]['fields'] = $this.data('fields');
			}
			if($this.data('featured')) {
				pconfig["multipart_params"]['featured'] = $this.data('featured');
			}
			
			var uploader = new plupload.Uploader(pconfig);
			
			uploader.bind('Init', function(up) { });
			uploader.init();
			
			uploader.bind('FilesAdded', function(up, files) {
				if($this.data('confirm')) {
					var reply = confirm($this.data('confirm'));
					if(!reply) return;
				}
				up.refresh();
				up.start();
				$(".alert").addClass("busy").fadeIn(800);
			});
			
			uploader.bind('UploadProgress', function(up, file) {
				$('#' + file.id + " .fileprogress").width(file.percent + "%");
				$('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
			});
			
			uploader.bind('Error', function(up, error){
				
				$('.prompt-box .show-login').hide();
				$('.prompt-box .show-error').show();
				
				if( -600 == error.code ){
					errorMessage = themify_lang.filesize_error;
					errorMessageFix = themify_lang.filesize_error_fix;
				}
				
				if($('.prompt-box .show-error').length > 0){
					$('.prompt-box .show-error').html('<p class="prompt-error">' + errorMessage + '</p>');
					if(errorMessageFix)
						$('.prompt-box .show-error').append('<p>' + errorMessageFix + '</p>');
				}
				$(".overlay, .prompt-box").fadeIn(500);
				
				return;
			});
			
			uploader.bind('FileUploaded', function(up, file, response) {
				
				var json = jQuery.parseJSON(response['response']);
				
				if('200' == response['status'] && !json.error) {
					status = 'done';
				} else {
					status = 'error';
				}
				
				$(".alert").removeClass("busy").addClass(status).delay(800).fadeOut(800, function() {
					$(this).removeClass(status);
				});
				
				if(json.error){
					$('.prompt-box .show-login').hide();
					$('.prompt-box .show-error').show();
					
					if($('.prompt-box .show-error').length > 0){
						$('.prompt-box .show-error').html('<p class="prompt-error">' + json.error + '</p>');
						$('.prompt-box .show-error').append('<p>' + themify_lang.enable_zip_upload + '</p>');
					}
					$(".overlay, .prompt-box").fadeIn(500);
					return;
				}
				
				$('#' + file.id).fadeOut();
				
				var response_file = json.file,
				response_url = json.url,
				response_type = json.type;
				
				if('zip' == response_type || 'rar' == response_type || 'plain' == response_type)
					window.location = location.href.replace(location.hash, '');
				else
					$('#' + imgId).val(response_url);
				
				if(topost){
					var thumb_url = json.thumb;
					var post_image_preview = $('<a href="' + response_url + '" target="_blank"><img src="' + thumb_url + '" width="40" /></a>')
					.fadeIn(1000)
					.css('display', 'inline-block');
					
					if($('#' + imgId + 'plupload-upload-ui').closest('.themify_field').children('.themify_upload_preview').find('a').length > 0){
						$('#' + imgId + 'plupload-upload-ui').closest('.themify_field').children('.themify_upload_preview').find('a').remove();
					}
					$('#' + imgId + 'plupload-upload-ui').closest('.themify_field').children('.themify_upload_preview').fadeIn().append(post_image_preview);
					$('.themify_featimg_remove').removeClass('hide');
				}
				
				if(haspreset){
					$('#' + imgId).closest('fieldset').children('.preset').find('img').removeClass('selected');
					
					var title = response_url.replace(/^.*[\\\/]/, '');
					//<span title="' + title + '"></span>
					var new_preset = $('<a href="#" title="' + title + '"><img src="' + response_url + '" alt="' + title + '" class="backgroundThumb selected" /></a>')
					.css('display', 'inline-block');
					$('#' + imgId).closest('fieldset').children('.preset').append(new_preset);
				}
			});
		});
	}
});
	