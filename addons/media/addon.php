<?php

/*
	Name:Media
	Version:1.0
	Author: Rahul Aryan
	Description:For adding media in question and answer
*/	

if (!defined('QA_VERSION')) {
		header('Location: /');
		exit;
}

	cs_event_hook('enqueue_css', NULL, 'cs_enqueue_css_x');
	function cs_enqueue_css_x($css_src){
		
		$css_src['cs_bootstrap'] = Q_THEME_URL . '/css/bootstrap.css';	
		return $css_src;
	}

class CS_Media_Addon{
	function __construct(){
		// hook buttons into head_script
		cs_event_hook('head_script', NULL, array($this, 'head_script'));
		
		// hook buttons into head_css
		cs_event_hook('head_css', NULL, array($this, 'head_css'));
		
		// hook buttons in theme layer
		cs_event_hook('ra_post_buttons', NULL, array($this, 'ra_post_buttons'));
		
		
	}
	
	public function ra_post_buttons($args){
		$themeclass = $args[0];
		$q_view = $args[1];
		
		$postid = $q_view['raw']['postid'];
		
		if (($themeclass->template == 'question') && (qa_get_logged_in_level() >= QA_USER_LEVEL_ADMIN) && (!empty($q_view))){
			$themeclass->output('
				<div class="btn-group featured-image-btn dropup">
					<button type="button" class="icon-image btn btn-default" data-toggle="modal" data-target="#media-modal">
					Media
					</button>
					
					<!-- Modal -->
					<div class="modal fade" id="media-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="myModalLabel">Add media</h4>
						  </div>
						  <div class="modal-body">
						
							<input id="fileupload" type="file" name="files[]" data-url="server/php/" multiple>
							
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary">Save changes</button>
						  </div>
						</div>
					  </div>
					</div>
				</div>
			');
		}
	}
	
	public function head_script($themeclass){		
		$themeclass->output('<script type="text/javascript" src="' . CS_CONTROL_URL . '/addons/media/jquery.fileupload.js"></script>');
		$themeclass->output('<script type="text/javascript" src="' . CS_CONTROL_URL . '/addons/media/jquery.iframe-transport.js"></script>');
		$themeclass->output('<script type="text/javascript" src="' . CS_CONTROL_URL . '/addons/media/script.js"></script>');
	}
	
	public function head_css($themeclass){
		$themeclass->output('<link rel="stylesheet" type="text/css" href="' . CS_CONTROL_URL . '/addons/media/styles.css"/>');
	}


}


// init method
$cs_media_addon = new CS_Media_Addon; 
