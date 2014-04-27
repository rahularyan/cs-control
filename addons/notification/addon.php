<?php

/*
	Name:CS Notification
	Type:layer
	Class:cs_notification_layer
	Version:1.0
	Author: Rahul Aryan
	Description:For showing ajax users notification
*/	

/* don't allow this page to be requested directly from browser */	
if (!defined('QA_VERSION')) {
		header('Location: /');
		exit;
}

qa_register_plugin_layer('addons/notification/notification-layer.php', 'CS Notification Layer');
qa_register_plugin_module('page', 'addons/notification/notification-page.php', 'cs_notification_page', 'CS Notification Page');

function cs_set_notification_as_read($id){
	if(qa_is_logged_in())
		qa_db_query_sub(
			'UPDATE ^ra_userevent SET `read` = 1 WHERE id=# AND effecteduserid=#',
			(int)$id, qa_get_logged_in_userid()
		);
}

