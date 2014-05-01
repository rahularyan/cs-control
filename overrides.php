<?php
/* don't allow this page to be requested directly from browser */	
if (!defined('QA_VERSION')) {
		header('Location: /');
		exit;
}

function qa_page_routing()
{
    $pages = qa_page_routing_base();
    $pages['cs_installation'] = '../qa-plugin/cs-control/installation.php'; // changed to include a new file instead of default page
    return $pages;
}

function qa_set_user_avatar($userid, $imagedata, $oldblobid=null){
	
	//require_once QA_INCLUDE_DIR.'qa-util-image.php';
	require_once CS_CONTROL_DIR.'/inc/class_images.php';
	$thumb = new Image($_FILES['file']['tmp_name']);
	$thumb->resize(200, 200, 'crop', 'c', 'c', 99);
	$imagedata=$thumb->get_image_content();

	if (isset($imagedata)) {
		require_once QA_INCLUDE_DIR.'qa-app-blobs.php';

		$newblobid=qa_create_blob($imagedata, 'jpeg', null, $userid, null, qa_remote_ip_address());
		
		if (isset($newblobid)) {
			qa_db_user_set($userid, 'avatarblobid', $newblobid);
			qa_db_user_set($userid, 'avatarwidth', $width);
			qa_db_user_set($userid, 'avatarheight', $height);
			qa_db_user_set_flag($userid, QA_USER_FLAGS_SHOW_AVATAR, true);
			qa_db_user_set_flag($userid, QA_USER_FLAGS_SHOW_GRAVATAR, false);

			if (isset($oldblobid))
				qa_delete_blob($oldblobid);

			return true;
		}
	}
	
	return false;
}