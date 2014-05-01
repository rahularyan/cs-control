<?php

/*
	Name:Tags Admin
	Version:1.0
	Author: Rahul Aryan
	Description:For adding media in question and answer
*/	

if (!defined('QA_VERSION')) {
		header('Location: /');
		exit;
}
qa_register_plugin_module('page', 'addons/tags-admin/page.php', 'cs_tags_admin_page', 'CS Tags Admin Page');


class CS_Tags_Admin_Addon{
	function __construct(){
		// hook buttons into head_script
		//cs_event_hook('head_script', NULL, array($this, 'head_script'));
		
		// hook buttons into head_css
		//cs_event_hook('head_css', NULL, array($this, 'head_css'));
	
		
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
$cs_tags_admin = new CS_Tags_Admin_Addon; 
