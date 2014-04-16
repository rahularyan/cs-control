<?php

/*
	Name:CS Notification
	Type:layer
	Class:cs_notification_layer
	Version:1.0
	Author: Rahul Aryan
	Description:For showing ajax users notification
*/	


class qa_html_theme_layer extends qa_html_theme_base {
	function doctype(){
		qa_html_theme_base::doctype();
		$cs_notification_id = qa_get('ra_notification');
		
		if(isset($cs_notification_id))
			cs_set_notification_as_read($cs_notification_id);
	}
	function cs_notification_btn(){
		//if (true){ // check options
			$userid = qa_get_logged_in_userid();
			if (isset( $userid )){
				$handle = qa_get_logged_in_handle();
				$this->output('
					<div class="user-actions pull-right">
						<div class="activity-bar">
							<div class="button dropdown">
								<a href="' . qa_path_html('user/' . $handle . '/activity') . '" class=" icon-bell dropdown-toggle activitylist" data-toggle="dropdown" id="activitylist"></a>
								<div class="dropdown-menu activity-dropdown-list pull-right" id="activity-dropdown-list">
									<div class="bar">
										<span>'.qa_lang_html('cleanstrap/notifications').'</span>
										<a class="mark-activity" href="#" data-id="'.qa_get_logged_in_userid().'">'.qa_lang('cleanstrap/mark_all_as_read').'</a>
									</div>
									<div class="append">
										<div class="ajax-list"></div>
										<span class="loading"></span>
										<div class="no-activity icon-signal">'.qa_lang('cleanstrap/no-activity').'</div>
									</div>
									
									<a class="event-footer" href="'.qa_path_html('user/'.$handle.'/notification', null, qa_opt('site_url')).'">'.qa_lang('cleanstrap/see_all').'</a>
									
								</div>
							</div>
						</div>
						
						<div class="message-bar">
							<div class="button dropdown">
								<a href="' . qa_path_html('user/' . $handle . '/message') . '" class=" icon-mail dropdown-toggle messagelist" data-toggle="dropdown" id="messagelist"></a>
								<div class="dropdown-menu message-dropdown-list pull-right" id="message-dropdown-list">
									<div class="bar">
										<span>'.qa_lang_html('cleanstrap/messages').'</span>
										<a class="mark-messages" href="#">'.qa_lang('cleanstrap/mark_all_as_read').'</a>
									</div>
									<div class="append">
										<div class="ajax-list"></div>
										<span class="loading"></span>
										<div class="no-activity icon-signal">'.qa_lang('cleanstrap/no-activity').'</div>
									</div>
									
									<a class="event-footer" href="'.qa_path_html('user/'.$handle.'/wall', null, qa_opt('site_url')).'">'.qa_lang('cleanstrap/see_all').'</a>
									
								</div>
							</div>
						</div>
					</div>
				');
			}
		//}
	}
	
	function head_script(){
		qa_html_theme_base::head_script();
		$this->output('<script type="text/javascript" src="' . CS_CONTROL_URL . '/addons/notification/notification.js"></script>');
	}
	
	function head_css(){
		qa_html_theme_base::head_css();
		$this->output('<link rel="stylesheet" type="text/css" href="' . CS_CONTROL_URL . '/addons/notification/styles.css"/>');
	}
	
}

function cs_set_notification_as_read($id){
	if(qa_is_logged_in())
		qa_db_query_sub(
			'UPDATE ^ra_userevent SET `read` = 1 WHERE id=# AND effecteduserid=#',
			(int)$id, qa_get_logged_in_userid()
		);
}

