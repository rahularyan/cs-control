<?php

/*
	Plugin Name: CS Control
	Plugin URI: http://rahularyan.com/cleanstrap
	Plugin Description: This is the helper plugin for cleanstrap theme developed by rahularyan.com
	Plugin Version: 1.0
	Plugin Date: 2014-21-03
	Plugin Author: Rahularyan.com
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.6
	Plugin Update Check URI: 
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

define('CS_CONTROL_DIR', dirname( __FILE__ ));
define('CS_CONTROL_ADDON_DIR', CS_CONTROL_DIR.'/addons');
define('CS_CONTROL_URL', get_base_url().'/qa-plugin/cs-control');
define('CS_THEME_URL', get_base_url().'/qa-theme/cleanstrap');
define('CS_THEME_DIR', QA_THEME_DIR . '/cleanstrap');
define('CS_VERSION', 2);


include_once(CS_CONTROL_DIR. '/action_hooks.php');
include_once(CS_THEME_DIR. '/action_hooks.php');

// register plugin language
qa_register_plugin_phrases('language/cs-lang-*.php', 'cleanstrap');


qa_register_plugin_overrides('overrides.php');

qa_register_plugin_module('event', 'inc/init.php', 'cs_init', 'CS Init');
qa_register_plugin_module('event', 'inc/cs-user-events.php', 'cs_user_event_logger', 'CS User Event Logger');

qa_register_plugin_module('widget', 'widgets/widget_ticker.php', 'cs_ticker_widget', 'CS Ticker');
qa_register_plugin_module('widget', 'widgets/widget_activity.php', 'cs_activity_widget', 'CS Site Activity');
qa_register_plugin_module('widget', 'widgets/widget_ask.php', 'cs_ask_widget', 'CS Ajax Ask');
qa_register_plugin_module('widget', 'widgets/widget_ask_form.php', 'cs_ask_form_widget', 'CS Ask Form');
qa_register_plugin_module('widget', 'widgets/widget_categories.php', 'widget_categories', 'CS Categories');
qa_register_plugin_module('widget', 'widgets/widget_tags.php', 'cs_tags_widget', 'CS Tags');
qa_register_plugin_module('widget', 'widgets/widget_text.php', 'cs_widget_text', 'CS Text Widget');
qa_register_plugin_module('widget', 'widgets/widget_current_category.php', 'cs_current_category_widget', 'CS Current Cat');
qa_register_plugin_module('widget', 'widgets/widget_user_posts.php', 'cs_user_posts_widget', 'CS User Posts');
qa_register_plugin_module('widget', 'widgets/widget_featured_questions.php', 'cs_featured_questions_widget', 'CS Featured Questions');
qa_register_plugin_module('widget', 'widgets/widget_question_activity.php', 'cs_question_activity_widget', 'CS Question Activity');
qa_register_plugin_module('widget', 'widgets/widget_related_questions.php', 'cs_related_questions', 'CS Related Questions');
qa_register_plugin_module('widget', 'widgets/widget_new_users.php', 'cs_new_users_widget', 'CS New Users');
qa_register_plugin_module('widget', 'widgets/widget_site_status.php', 'cs_site_status_widget', 'CS Site Status');
qa_register_plugin_module('widget', 'widgets/widget_top_users.php', 'cs_top_users_widget', 'CS Top Contributors');
qa_register_plugin_module('widget', 'widgets/widget_posts.php', 'cs_widget_posts', 'CS Posts');
qa_register_plugin_module('widget', 'widgets/widget_user_activity.php', 'cs_user_activity_widget', 'CS User Activity');
qa_register_plugin_module('widget', 'widgets/widget_scroller.php', 'cs_widget_scroller', 'CS Scroller');

qa_register_plugin_module('page', 'options.php', 'cs_theme_options', 'Theme Options');
qa_register_plugin_module('page', 'widgets.php', 'cs_theme_widgets', 'Theme Widgets');
qa_register_plugin_module('page', 'install.php', 'cs_theme_install_page', 'Theme Install Page');


qa_register_plugin_layer('cs-layer.php', 'CS Control Layer');


//load all addons
cs_load_addons();

function get_base_url()
{
	/* First we need to get the protocol the website is using */
	$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https://' : 'http://';

	/* returns /myproject/index.php */
	if(QA_URL_FORMAT_NEAT == 0 || strpos($_SERVER['PHP_SELF'],'/index.php/') !== false):
		$path = strstr($_SERVER['PHP_SELF'], '/index', true);
		$directory = $path;
	else:
		$path = $_SERVER['PHP_SELF'];
		$path_parts = pathinfo($path);
		$directory = $path_parts['dirname'];
		$directory = ($directory == "/") ? "" : $directory;
	endif;       
		
		$directory = ($directory == "\\") ? "" : $directory;
		
	/* Returns localhost OR mysite.com */
	$host = $_SERVER['HTTP_HOST'];

	return $protocol . $host . $directory;
}	


function cs_read_addons(){
	$addons = array();
	//load files from addons folder
	$files=glob(CS_CONTROL_DIR.'/addons/*/addon.php');
	//print_r($files);
	foreach ($files as $file){
		$data = cs_get_addon_data($file);
		$data['folder'] = basename(dirname($file));
		$data['file'] = basename($file);
		$addons[] = $data;
	}
	return $addons;
}
function cs_read_addons_ajax(){
	$addons = array();
	//load files from addons folder
	$files=glob(CS_CONTROL_DIR.'/addons/*/ajax.php');
	//print_r($files);
	foreach ($files as $file){
		$data['folder'] = basename(dirname($file));
		$data['file'] = basename($file);
		$addons[] = $data;
	}
	return $addons;
}

function cs_load_addons(){
	$addons = cs_read_addons();
	if(!empty($addons))
		foreach($addons as $addon){
			include_once CS_CONTROL_DIR.'/addons/'.$addon['folder'].'/'.$addon['file'];
		}
}
function cs_load_addons_ajax(){
	$addons = cs_read_addons_ajax();
	if(!empty($addons))
		foreach($addons as $addon){			
			require_once CS_CONTROL_DIR.'/addons/'.$addon['folder'].'/'.$addon['file'];			
		}
}


function cs_get_addon_data( $plugin_file) {
	$plugin_data = cs_get_file_data( $plugin_file);

	return $plugin_data;
}

function cs_get_file_data( $file) {
	// We don't need to write to the file, so just open for reading.
	$fp = fopen( $file, 'r' );

	// Pull only the first 8kiB of the file in.
	$file_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose( $fp );

	$metadata=cs_addon_metadata($file_data, array(
		'name' => 'Name',
		'type' => 'Type',
		'class' => 'Class',
		'description' => 'Description',
		'version' => 'Version',
		'author' => 'Author',
		'author_uri' => 'Author URI'
	));

	return $metadata;
}

function cs_addon_metadata($contents, $fields){
	$metadata=array();

	foreach ($fields as $key => $field)
		if (preg_match('/'.str_replace(' ', '[ \t]*', preg_quote($field, '/')).':[ \t]*([^\n\f]*)[\n\f]/i', $contents, $matches))
			$metadata[$key]=trim($matches[1]);
	
	return $metadata;
}

function get_all_widgets()
{		
	$widgets = qa_db_read_all_assoc(qa_db_query_sub('SELECT * FROM ^ra_widgets ORDER BY widget_order'));
	foreach($widgets as $k => $w){
		$param = @preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $w['param']);
		$param = unserialize($param);
		$widgets[$k]['param'] = $param;
	}
	return $widgets;

}

function get_widgets_by_position($position)
{		
	$widgets = qa_db_read_all_assoc(qa_db_query_sub('SELECT * FROM ^ra_widgets WHERE position = $ ORDER BY widget_order', $position));
	foreach($widgets as $k => $w){
		$param = unserialize($w['param']);
		$widgets[$k]['param'] = $param;
	}
	return $widgets;

}
function widget_opt($name, $position=false, $order = false, $param = false, $id= false)
{		
	if($position && $param){
		return widget_opt_update($name, $position, $order, $param, $id);		
	}else{
		qa_db_read_one_value(qa_db_query_sub('SELECT * FROM ^ra_widgets WHERE name = $',$name ), true);		
	}
}


function widget_opt_update($name, $position, $order, $param, $id = false){

	if($id){
		qa_db_query_sub(
			'UPDATE ^ra_widgets SET position = $, widget_order = #, param = $ WHERE id=#',
			$position, $order, $param, $id
		);
		return $id;
	}else{
		qa_db_query_sub(
			'INSERT ^ra_widgets (name, position, widget_order, param) VALUES ($, $, #, $)',
			$name, $position, $order, $param
		);
		return qa_db_last_insert_id();
	}
}
function widget_opt_delete($id ){
	qa_db_query_sub('DELETE FROM ^ra_widgets WHERE id=#', $id);
}

function cs_user_data($handle){
	$userid = qa_handle_to_userid($handle);
	$identifier=QA_FINAL_EXTERNAL_USERS ? $userid : $handle;
	$user = array();
	if(defined('QA_WORDPRESS_INTEGRATE_PATH')){
		$u_rank = cs_get_cache_select_selectspec(qa_db_user_rank_selectspec($userid,true));
		$u_points = cs_get_cache_select_selectspec(qa_db_user_points_selectspec($userid,true));
		
		$userinfo = array();
		$user_info = get_userdata( $userid );
		$userinfo['userid'] = $userid;
		$userinfo['handle'] = $handle;
		$userinfo['email'] = $user_info->user_email;
		
		$user[0] = $userinfo;
		$user[1]['rank'] = $u_rank;
		$user[2] = $u_points;
		$user = ($user[0]+ $user[1]+ $user[2]);
	}else{
		$user[0] = cs_get_cache_select_selectspec( qa_db_user_account_selectspec($userid, true) );
		$user[1]['rank'] = cs_get_cache_select_selectspec( qa_db_user_rank_selectspec($handle) );
		$user[2] = cs_get_cache_select_selectspec( qa_db_user_points_selectspec($identifier) );
		$user = ($user[0]+ $user[1]+ $user[2]);
	}
	return $user;
}	

function cs_get_avatar($handle, $size = 40, $html =true){
	$userid = qa_handle_to_userid($handle);
	if(defined('QA_WORDPRESS_INTEGRATE_PATH')){
		$img_html = get_avatar( qa_get_user_email($userid), $size);
	}else if(QA_FINAL_EXTERNAL_USERS){
		$img_html = qa_get_external_avatar_html($userid, $size, false);
	}else{
		if (!isset($handle)){
			if (qa_opt('avatar_allow_gravatar'))
				$img_html = qa_get_gravatar_html(qa_get_user_email($userid), $size);
			else if ( qa_opt('avatar_allow_upload') && qa_opt('avatar_default_show') && strlen(qa_opt('avatar_default_blobid')) )
				$img_html = qa_get_avatar_blob_html(qa_opt('avatar_default_blobid'), qa_opt('avatar_default_width'), qa_opt('avatar_default_height'), $size);
			else
				$img_html = '';
		}else{
			$f = cs_user_data($handle);
			if(empty($f['avatarblobid'])){
				if (qa_opt('avatar_allow_gravatar'))
					$img_html = qa_get_gravatar_html(qa_get_user_email($userid), $size);
				else if ( qa_opt('avatar_allow_upload') && qa_opt('avatar_default_show') && strlen(qa_opt('avatar_default_blobid')) )
					$img_html = qa_get_avatar_blob_html(qa_opt('avatar_default_blobid'), qa_opt('avatar_default_width'), qa_opt('avatar_default_height'), $size);
				else
					$img_html = '';
			} else
				$img_html = qa_get_user_avatar_html($f['flags'], $f['email'], $handle, $f['avatarblobid'], $size, $size, $size, true);
		}
	}
	if (empty($img_html))
		return;
		
	preg_match( '@src="([^"]+)"@' , $img_html , $match );
	if($html)
		return '<a href="'.qa_path_html('user/'.$handle).'">'.(!defined('QA_WORDPRESS_INTEGRATE_PATH') ?  '<img src="'.$match[1].'" />':$img_html).'</a>';		
	elseif(isset($match[1]))
		return $match[1];
}
function cs_get_post_avatar($post, $userid ,$size = 40, $html=false){
	if(defined('QA_WORDPRESS_INTEGRATE_PATH')){
		$avatar = get_avatar( qa_get_user_email($userid), $size);
	}if (QA_FINAL_EXTERNAL_USERS)
		$avatar = qa_get_external_avatar_html($post['userid'], $size, false);
	else
		$avatar = qa_get_user_avatar_html($post['flags'], $post['email'], $post['handle'],
			$post['avatarblobid'], $post['avatarwidth'], $post['avatarheight'], $size);
	if($html)
		return '<div class="avatar" data-id="'.$userid.'" data-handle="'.$post['handle'].'">'.$avatar.'</div>';
	
	return $avatar;
}

function cs_post_type($id){
	$result = qa_db_read_one_value(qa_db_query_sub('SELECT type FROM ^posts WHERE postid=#', $id ),true);
	return $result;
}

function cs_post_status($item, $description = false){
	$notice = '';
	if (@$item['answer_selected'] || @$item['raw']['selchildid']){	
		$notice =   '<span class="post-status selected">'.qa_lang_html('cleanstrap/solved').'</span>' ;
		if($description)
			$notice .=   qa_lang_html('cleanstrap/marked_as_solved') ;
	}elseif(@$item['raw']['closedbyid']){
		$type = cs_post_type(@$item['raw']['closedbyid']);
		if($type == 'Q'){
			$notice =   '<span class="post-status duplicate">'.qa_lang_html('cleanstrap/duplicate').'</span>' ;
			if($description)
				$notice .=   qa_lang_html('cleanstrap/marked_as_duplicate') ;			
		}else{
			$notice =   '<span class="post-status closed">'.qa_lang_html('cleanstrap/closed').'</span>' ;
			if($description)
				$notice .=   qa_lang_html('cleanstrap/marked_as_closed') ;
		}
	}else{
		$notice =   '<span class="post-status open">'.qa_lang_html('cleanstrap/open').'</span>' ;
			if($description)
				$notice .=   qa_lang_html('cleanstrap/marked_as_open') ;		
	}
	return $notice;
}
function cs_get_post_status($item, $description = false){
	// this will return question status whether question is open, closed, duplicate or solved
	
	if (@$item['answer_selected'] || @$item['raw']['selchildid']){	
		$status =   'solved' ;
	}elseif(@$item['raw']['closedbyid']){
		$type = cs_post_type(@$item['raw']['closedbyid']);
		if($type == 'Q')
			$status =   'duplicate' ;	
		else
			$status =   'closed' ;	
	}else{
		$status =   'open' ;	
	}
	return $status;
}
function cs_get_excerpt($id){
	$result = qa_db_read_one_value(qa_db_query_sub('SELECT content FROM ^posts WHERE postid=#', $id ),true);
	return strip_tags($result);
}
function cs_truncate($string, $limit, $pad="...") {
	if(strlen($string) <= $limit) 
		return $string; 
	else{ 
		//preg_match('/^.{1,'.$limit.'}\b/s', $string, $match);
		//return $match[0].$pad;
		$text = $string.' ';
		$text = substr($text,0,$limit);
		$text = substr($text,0,strrpos($text,' '));
		return $text.$pad;
	} 
}
		
function cs_user_profile($handle, $field =NULL){
	$userid = qa_handle_to_userid($handle);
	if(defined('QA_WORDPRESS_INTEGRATE_PATH')){
		return get_user_meta( $userid );
	}else{
		$query = cs_get_cache_select_selectspec(qa_db_user_profile_selectspec($userid, true));
		
		if(!$field) return $query;
		if (isset($query[$field]))
			return $query[$field];
	}
	
	return false;
}	

function cs_user_badge($handle) {
	if(qa_opt('badge_active')){
	$userids = qa_handles_to_userids(array($handle));
	$userid = $userids[$handle];

	
	// displays small badge widget, suitable for meta
	
	$result = qa_db_read_all_values(
		qa_db_query_sub(
			'SELECT badge_slug FROM ^userbadges WHERE user_id=#',
			$userid
		)
	);

	if(count($result) == 0) return;
	
	$badges = qa_get_badge_list();
	foreach($result as $slug) {
		$bcount[$badges[$slug]['type']] = isset($bcount[$badges[$slug]['type']])?$bcount[$badges[$slug]['type']]+1:1; 
	}
	$output='<ul class="user-badge clearfix">';
	for($x = 2; $x >= 0; $x--) {
		if(!isset($bcount[$x])) continue;
		$count = $bcount[$x];
		if($count == 0) continue;

		$type = qa_get_badge_type($x);
		$types = $type['slug'];
		$typed = $type['name'];

		$output.='<li class="badge-medal '.$types.'"><i class="icon-badge" title="'.$count.' '.$typed.'"></i><span class="badge-pointer badge-'.$types.'-count" title="'.$count.' '.$typed.'"> '.$count.'</span></li>';
	}
	$output = substr($output,0,-1);  // lazy remove space
	$output.='</ul>';
	return($output);
	}
}
function cs_name($handle){
	if(defined('QA_WORDPRESS_INTEGRATE_PATH')){
		$userdata = cs_user_profile($handle, 'name');
		$name = $userdata['nickname'][0];
	}else
		$name = cs_user_profile($handle, 'name');
	return strlen($name) ? $name : $handle;
}



function cs_post_link($id){
	$type = mysql_result(qa_db_query_sub('SELECT type FROM ^posts WHERE postid = "'.$id.'"'), 0);
	
	if($type == 'A')
		$id = mysql_result(qa_db_query_sub('SELECT parentid FROM ^posts WHERE postid = "'.$id.'"'),0);
	
	$post = qa_db_query_sub('SELECT title FROM ^posts WHERE postid = "'.$id.'"');
	return qa_q_path_html($id, mysql_result($post,0));
}	

function cs_tag_list($limit = 20){
	$populartags=qa_db_single_select(qa_db_popular_tags_selectspec(0, $limit));
			
	$i= 1;
	foreach ($populartags as $tag => $count) {							
		echo '<li><a class="icon-tag" href="'.qa_path_html('tag/'.$tag).'">'.qa_html($tag).'<span>'.filter_var($count, FILTER_SANITIZE_NUMBER_INT).'</span></a></li>';
	}
}

function cs_url_grabber($str) {
	preg_match_all(
	  '#<a\s
		(?:(?= [^>]* href="   (?P<href>  [^"]*) ")|)
		(?:(?= [^>]* title="  (?P<title> [^"]*) ")|)
		(?:(?= [^>]* target=" (?P<target>[^"]*) ")|)
		[^>]*>
		(?P<text>[^<]*)
		</a>
	  #xi',
	  $str,
	  $matches,
	  PREG_SET_ORDER
	);
	

	foreach($matches as $match) {
	 return '<a href="'.$match['href'].'" title="'.$match['title'].'">'.$match['text'].'</a>';
	}	
}

function cs_register_widget_position($widget_array){
	if(is_array($widget_array)){
		qa_opt('cs_widgets_positions', serialize($widget_array));
	}
	return;
}



function cs_get_template_array(){
	return array(
		'qa' 			=> 'QA',
		'home' 			=> 'Home',
		'ask' 			=> 'Ask',
		'question' 		=> 'Question',
		'questions' 	=> 'Questions',
		'activity' 		=> 'Activity',
		'unanswered' 	=> 'Unanswered',
		'hot' 			=> 'Hot',
		'tags' 			=> 'Tags',
		'tag' 			=> 'Tag',
		'categories' 	=> 'Categories',
		'users' 		=> 'Users',
		'user' 			=> 'User',
		'account' 		=> 'Account',
		'favorite' 		=> 'Favorite',
		'user-wall' 	=> 'User Wall',
		'user-activity' => 'User Activity',
		'user-questions' => 'User Questions',
		'user-answers' 	=> 'User Answers',
		'custom' 		=> 'Custom',
		'login' 		=> 'Login',
		'feedback' 		=> 'Feedback',
		'updates' 		=> 'Updates',
		'search' 		=> 'Search',
		'admin' 		=> 'Admin'
	);
}

function cs_social_icons(){
	return array(
		'icon-facebook' 	=> 'Facebook',
		'icon-twitter' 		=> 'Twitter',
		'icon-googleplus' 	=> 'Google',
		'icon-pinterest' 	=> 'Pinterest',
		'icon-linkedin' 	=> 'Linkedin',
		'icon-github' 		=> 'Github',
		'icon-stumbleupon' 	=> 'Stumbleupon',
	);
}



function reset_theme_options(){
	qa_opt('cs_custom_style','');
	// General
	qa_opt('logo_url', Q_THEME_URL . '/images/logo.png');
	qa_opt('cs_favicon_url', '');
	qa_opt('cs_featured_image_width', 800);
	qa_opt('cs_featured_image_height', 300);
	qa_opt('cs_featured_thumbnail_width', 278);
	qa_opt('cs_featured_thumbnail_height', 120);
	qa_opt('cs_crop_x', 'c');
	qa_opt('cs_crop_y', 'c');
	
	
	
	
	// Layout
	qa_opt('cs_theme_layout', 'boxed');
	qa_opt('cs_nav_fixed', true);	
	qa_opt('cs_show_icon', true);	
	qa_opt('cs_enable_ask_button', true);	
	qa_opt('cs_enable_category_nav', true);	
	qa_opt('cs_enable_clean_qlist', true);	
	qa_opt('cs_enable_default_home', true);	
	qa_opt('cs_enable_except', false);
	qa_opt('cs_except_len', 240);
	if ((int)qa_opt('avatar_q_list_size')>0){
		qa_opt('avatar_q_list_size',35);
		qa_opt('cs_enable_avatar_lists', true);
	}else
		qa_opt('cs_enable_avatar_lists', false);
	qa_opt('show_view_counts', false);
	qa_opt('cs_show_tags_list', true);
	qa_opt('cs_horizontal_voting_btns', false);
	qa_opt('cs_enble_back_to_top', true);
	qa_opt('cs_back_to_top_location', 'nav');
	// Styling
	qa_opt('cs_styling_duplicate_question', false);
	qa_opt('cs_styling_solved_question', false);
	qa_opt('cs_styling_closed_question', false);
	qa_opt('cs_styling_open_question', false);
	qa_opt('cs_bg_select', false);
	qa_opt('cs_bg_color', '#F4F4F4');
	qa_opt('cs_text_color', '');
	qa_opt('cs_border_color', '#EEEEEE');
	qa_opt('cs_q_link_color', '');
	qa_opt('cs_q_link_hover_color', '');
	qa_opt('cs_nav_link_color', '');
	qa_opt('cs_nav_link_color_hover', '');
	qa_opt('cs_subnav_link_color', '');
	qa_opt('cs_subnav_link_color_hover', '');
	qa_opt('cs_link_color', '');
	qa_opt('cs_link_hover_color', '');
	qa_opt('cs_highlight_color', '');
	qa_opt('cs_highlight_bg_color', '');
	qa_opt('cs_ask_btn_bg', '');
	qa_opt('cs_custom_css', '');
	
	// Typography
	$typo = array('h1','h2','h3','h4','h5','p','span','quote','qtitle','qtitlelink','pcontent','mainnav');
	foreach($typo as $k ){
		qa_opt('typo_options_family_' . $k , '');
		qa_opt('typo_options_style_' . $k , '');
		qa_opt('typo_options_size_' . $k , '');
		qa_opt('typo_options_linehight_' . $k , '');
		qa_opt('typo_options_backup_' . $k , '');
	}
	
	// Social
	qa_opt('cs_social_list','');
	qa_opt('cs_social_enable', false);
	
	// Advertisement
	qa_opt('cs_advs','');
	qa_opt('cs_enable_adv_list', false);
	qa_opt('cs_ads_below_question_title', '');
	qa_opt('cs_ads_after_question_content','');

	// footer							
	qa_opt('cs_footer_copyright', 'Copyright © 2014');
}

function is_featured($postid){
	require_once QA_INCLUDE_DIR.'qa-db-metas.php';
	return (bool)qa_db_postmeta_get($postid, 'featured_question');
}
function get_featured_thumb($postid){
	require_once QA_INCLUDE_DIR.'qa-db-metas.php';
	$img =  qa_db_postmeta_get($postid, 'featured_image');

	if (!empty($img)){
		$thumb_img = preg_replace('/(\.[^.]+)$/', sprintf('%s$1', '_s'), $img);
		return '<img class="featured-image" src="'.Q_THEME_URL . '/uploads/' . $thumb_img .'" />';
	}
	return false;
}
function get_featured_image($postid){
	require_once QA_INCLUDE_DIR.'qa-db-metas.php';
	$img =  qa_db_postmeta_get($postid, 'featured_image');

	if (!empty($img))
		return '<img class="image-preview" id="image-preview" src="'.Q_THEME_URL . '/uploads/' . $img.'" />';
		
	return false;
}
function cs_cat_path($categorybackpath){
	return qa_path_html(implode('/', array_reverse(explode('/', $categorybackpath))));
}

/**
 * multi_array_key_exists function.
 *
 * @param mixed $needle The key you want to check for
 * @param mixed $haystack The array you want to search
 * @return bool
 */
function multi_array_key_exists( $needle, $haystack ) {
	if(isset($haystack) && is_array($haystack))
    foreach ( $haystack as $key => $value ) :

        if ( $needle == $key )
            return true;
       
        if ( is_array( $value ) ) :
             if ( multi_array_key_exists( $needle, $value ) == true )
                return true;
             else
                 continue;
        endif;
       
    endforeach;
   
    return false;
}
function make_array_utf8( $arr ) {
    foreach ( $arr as $key => $value )
        if ( is_array( $value ) ) 
            $arr[$key] = make_array_utf8( $value );
        else
			$arr[$key] = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($value));
	return $arr;
}

function cs_get_site_cache(){
	global $cache;
	$cache = json_decode( qa_db_cache_get('cs_cache', 0),true );
}

function cs_get_cache_popular_tags($to_show){
	global $cache;
	$age = 3600; // 1 hour

	if (isset($cache['tags'])){
		if ( ((int)$cache['tags']['age'] + $age) > time()) {
			$populartags = $cache['tags'];
			unset($populartags['age']);
			return $populartags;
		}
	}
	$populartags=qa_db_single_select(qa_db_popular_tags_selectspec(0, (!empty($to_show) ? $to_show : 20)));
	$cache['tags'] =  $populartags;
	$cache['tags']['age'] = time();
	$cache['changed'] = true;	
	return $populartags;
}
function cs_get_cache_question_activity($qcount){
	global $cache;
	$age = 60; // one minute

	if (isset($cache['qactivity'])){
		if ( ((int)$cache['qactivity']['age'] + $age) > time()) {
			$content = $cache['qactivity'];
			unset($content['age']);
			return $content;
		}
	}
	
	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	require_once QA_INCLUDE_DIR.'qa-app-format.php';
	require_once QA_INCLUDE_DIR.'qa-app-q-list.php';
	
	$categoryslugs='';
	$userid=qa_get_logged_in_userid();


//	Get lists of recent activity in all its forms, plus category information
	
	list($questions1, $questions2, $questions3, $questions4)=qa_db_select_with_pending(
		qa_db_qs_selectspec($userid, 'created', 0, $categoryslugs, null, false, false, $qcount),
		qa_db_recent_a_qs_selectspec($userid, 0, $categoryslugs),
		qa_db_recent_c_qs_selectspec($userid, 0, $categoryslugs),
		qa_db_recent_edit_qs_selectspec($userid, 0, $categoryslugs)
	);
	
//	Prepare and return content for theme
	$content =  qa_q_list_page_content(
		qa_any_sort_and_dedupe(array_merge($questions1, $questions2, $questions3, $questions4)), // questions
		$qcount, // questions per page
		0, // start offset
		null, // total count (null to hide page links)
		null, // title if some questions
		null, // title if no questions
		null, // categories for navigation
		null, // selected category id
		true, // show question counts in category navigation
		'activity/', // prefix for links in category navigation
		null, // prefix for RSS feed paths (null to hide)
		null, // suggest what to do next
		null, // page link params
		null // category nav params
	);
	$result = $content['q_list']['qs'];
	$cache['qactivity'] =  $result;
	$cache['qactivity']['age'] = time();
	$cache['changed'] = true;	
	return $result;
}
function cs_get_cache_select_selectspec($selectspec){
	global $cache;
	$age = 10;
	
	$hash = md5(json_encode($selectspec));
	if (isset($cache[$hash])){
		if ( ((int)$cache[$hash]['age'] + $age) > time()) {
			$result = $cache[$hash]['result'];
			return $result;
		}
	}
	$result = qa_db_select_with_pending($selectspec);
	$cache[$hash]['result'] =  $result;
	$cache[$hash]['age'] = time();
	$cache['changed'] = true;
	return $result ;	
}
function cs_get_cache($query,$age = 10){
	global $cache;

	$funcargs=func_get_args();
	unset($funcargs[1]);
	$query =  qa_db_apply_sub($query, array_slice($funcargs, 1));
	$hash = md5($query);
	if (isset($cache[$hash])){
		if ( ((int)$cache[$hash]['age'] + $age) > time()) {
			$result = $cache[$hash]['result'];
			return $result;
		}
	}
	$result = qa_db_read_all_assoc( qa_db_query_raw($query) );
	$cache[$hash]['result'] =  $result;
	$cache[$hash]['age'] = time();
	$cache['changed'] = true;
	return $result ;	
}
function cs_set_site_cache(){
	global $cache;
	if (@$cache['changed']){
		unset($cache['changed']);
		$cache = make_array_utf8($cache);
		qa_db_cache_set('cs_cache', 0, json_encode($cache) );
	}
}

function cs_ajax_user_popover(){
	
	$handle_id= qa_post_text('handle');
	$handle= qa_post_text('handle');
	require_once QA_INCLUDE_DIR.'qa-db-users.php';
	if(isset($handle)){
		$userid = qa_handle_to_userid($handle);
		//$badges = ra_user_badge($handle);
		
		if(defined('QA_WORDPRESS_INTEGRATE_PATH')){
			$userid = qa_handle_to_userid($handle);
			$cover = get_user_meta( $userid, 'cover' );
			$cover = $cover[0];
		}else{
			$data = cs_user_data($handle);
		}

		?>
		<div id="<?php echo $userid;?>_popover" class="user-popover">
			<div class="counts clearfix">
				<div class="points">
					<?php echo '<span>'.$data['points'] .'</span>Points'; ?>
				</div>
				<div class="qcount">
					<?php echo '<span>'.$data['qposts'] .'</span>Questions'; ?>
				</div>
				<div class="acount">
					<?php echo '<span>'.$data['aposts'] .'</span>Answers'; ?>
				</div>
				<div class="ccount">
					<?php echo '<span>'.$data['cposts'] .'</span>Comments'; ?>
				</div>
			</div>
			<div class="bottom">	
				<div class="avatar pull-left"><?php echo cs_get_avatar($handle, 30); ?></div>
				<span class="name"><?php echo cs_name($handle); ?></span>				
				<span class="level"><?php echo qa_user_level_string($data['level']); ?></span>				
			</div>
		</div>	
		<?php
	}
	die();
}


function cs_ago($time)
{
   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
   $lengths = array("60","60","24","7","4.35","12","10");

   $now = time();

       $difference     = $now - $time;
       $tense         = "ago";

   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
       $difference /= $lengths[$j];
   }

   $difference = round($difference);

   if($difference != 1) {
       $periods[$j].= "s";
   }

   return "$difference $periods[$j] 'ago' ";
}

function stripslashes2($string) {
	str_replace('\\', '', $string);
    return $string;
}

function cs_followers_list($handle, $limit = 14, $order_by = 'rand'){
	$userid = qa_handle_to_userid($handle);
	
	if( $order_by == 'rand')
		$order_by = 'ORDER BY RAND()';
	
	$followers = qa_db_read_all_values(qa_db_query_sub('SELECT ^users.handle FROM ^userfavorites, ^users  WHERE (^userfavorites.userid = ^users.userid and ^userfavorites.entityid = #) and ^userfavorites.entitytype = "U" ORDER BY RAND() LIMIT #', $userid,  (int)$limit));	

	
	if(count($followers)){
		$output = '<div class="user-followers-inner">';
		$output .= '<ul class="user-followers clearfix">';
		foreach($followers as $user){
			$id = qa_handle_to_userid($user);
			$output .= '<li><div class="avatar" data-handle="'.$user.'" data-id="'.$id.'"><a href="'.qa_path_html('user/'.$user).'"><img src="'.cs_get_avatar($user, 59, false).'" /></a></div></li>';
		}
		$count = cs_user_followers_count($userid);
		
		if($count > 100)
			$count = '99+';
		else
			$count = ($count);
			
		$output .= '<li class="total-followers"><a href="'.qa_path_html('followers').'"><span>'.$count.'</span>'.qa_lang_html('cleanstrap/followers').'</a></li>';
		$output .= '</ul>';
		$output .= '</div>';
		return $output;
	}
	return;
}
function cs_user_followers_count($userid){
	$count =  qa_db_read_one_value(qa_db_query_sub('SELECT count(userid) FROM ^userfavorites  WHERE  entityid = # and entitytype = "U"', $userid), true);
	return $count;
}

function handle_url($handle){
	return qa_path_html('user/'.$handle);
}

function cs_event_hook($event, $value = NULL, $callback = NULL){
    static $events;

    // Adding or removing a callback?
    if($callback !== NULL)
    {
        if($callback)
        {
            $events[$event][] = $callback;
        }
        else
        {
            unset($events[$event]);
        }
    }
    elseif(isset($events[$event])) // Fire a callback
    {
        foreach($events[$event] as $function)
        {
            $value = call_user_func($function, $value);
        }
        return $value;
    }
}