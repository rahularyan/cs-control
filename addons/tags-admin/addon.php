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
		cs_event_hook('doctype', NULL, array($this, 'navigation'));
		cs_event_hook('language', NULL, array($this, 'language'));
		cs_event_hook('enqueue_css', NULL, array($this, 'css'));
		cs_event_hook('enqueue_scripts', NULL, array($this, 'scripts'));	
		cs_event_hook('cs_ajax_save_tags', NULL, array($this, 'save_tags'));	
	}
		
	public function navigation($themeclass){		
		if(qa_get_logged_in_level() >= QA_USER_LEVEL_ADMIN)	{
			//show tags-admin menu if user is admin
			$themeclass->content['navigation']['sub']['tags-admin'] = array(
				'label' => qa_lang('cleanstrap/tags_admin'),
				'url' => qa_path_html('admin/tags-admin'),
				'icon' => 'icon-wrench'
			);
			if($themeclass->request == 'admin/tags-admin') {
				$themeclass->content['navigation']['sub']['tags-admin']['selected'] = true;
				$themeclass->content['navigation']['sub']['tags-admin']['selected'] = true;
				$themeclass->template = 'admin';
			}
		
		}	
	
		return $themeclass->content;
	}
	public function css($css_src){
		
		$css_src['cs_notification'] = CS_CONTROL_URL . '/addons/tags-admin/styles.css';
		return  $css_src;
	}
	
	public function scripts($src){		
		$src['cs_notification'] = CS_CONTROL_URL . '/addons/tags-admin/script.js';

		return  $src;
	}
	
	public function language($lang_array){
		return array(
			'tags_admin' 			=> 'Tags Admin',
			'edit_tags_page' 		=> 'Edit tags and description'
		);
		
	}
	
	public function save_tags(){	
		
		if(qa_get_logged_in_level() >= QA_USER_LEVEL_ADMIN && qa_check_form_security_code('edit-tag', $_REQUEST['code']))	{
			echo cs_update_tags_meta(qa_post_text('tag'), 'description', qa_post_text('description'));
		}
		
		die();
	}

}


// init method
$cs_tags_admin = new CS_Tags_Admin_Addon; 
