<?php
	class cs_new_users_widget {

		function cs_widget_form()
		{
			
			return array(
				'style' => 'wide',
				'fields' => array(
					'cs_nu_count' => array(
						'label' => 'Numbers of user',
						'type' => 'number',
						'tags' => 'name="cs_nu_count" class="form-control"',
						'value' => '10',
					),
					'cs_nu_avatar' => array(
						'label' => 'Avatar Size',
						'type' => 'number',
						'tags' => 'name="cs_nu_avatar" class="form-control"',
						'value' => '30',
					)	
				),

			);
		}

		
		function allow_template($template)
		{
			$allow=false;
			
			switch ($template)
			{
				case 'activity':
				case 'qa':
				case 'questions':
				case 'hot':
				case 'ask':
				case 'categories':
				case 'question':
				case 'tag':
				case 'tags':
				case 'unanswered':
				case 'user':
				case 'users':
				case 'search':
				case 'admin':
				case 'custom':
					$allow=true;
					break;
			}
			
			return $allow;
		}

		
		function allow_region($region)
		{
			$allow=false;
			
			switch ($region)
			{
				case 'main':
				case 'side':
				case 'full':
					$allow=true;
					break;
			}
			
			return $allow;
		}
		function cs_new_users($limit, $size){
			$output = '<ul class="users-list clearfix">';
			if (defined('QA_FINAL_WORDPRESS_INTEGRATE_PATH')){
				global $wpdb;
				$users = $wpdb->get_results("SELECT ID from $wpdb->users order by ID DESC LIMIT $limit");
				require_once QA_INCLUDE_DIR.'qa-app-posts.php';
				
				foreach($users as $u){
					$handle = qa_post_userid_to_handle($u->ID);
					$output .= '<li class="user">';
					$output .= '<div class="avatar" data-handle="'.$handle.'" data-id="'. qa_handle_to_userid($handle).'"><img src="'.cs_get_avatar($handle, $size, false).'" /></div>';
					$output .= '</li>';
				}
				
			}else{
				if ( qa_opt('avatar_default_show') && strlen(qa_opt('avatar_default_blobid')) )
					$users = cs_get_cache("SELECT * FROM ^users ORDER BY created DESC LIMIT #",600, $limit); //refresh every 10 minutes
				elseif ( qa_opt('avatar_allow_upload') )
					$users = cs_get_cache("SELECT * FROM ^users WHERE avatarblobid IS NOT NULL ORDER BY created DESC LIMIT #",600, $limit);	
				else
					$users = cs_get_cache("SELECT * FROM ^users ORDER BY created DESC LIMIT #",600, $limit);	
				foreach($users as $u){
					if (isset($u['handle'])){
						$handle = $u['handle'];
						$avatar = cs_get_avatar($handle, $size, false);
						if (isset($u['useid']))	$id = $u['useid']; else $id = qa_handle_to_userid($handle);
						$output .= '<li class="user">';
						if (!empty($avatar))
							$output .= '<a href="' . qa_path_html('user/'. $handle) . '"><div class="avatar" data-handle="'. $handle .'" data-id="'. $id .'"><img src="'.$avatar.'" /></div></a>';
						$output .= '</li>';
					}
				}
			}
			$output .= '</ul>';
			echo $output;
		}

		function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
		{
			$widget_opt = $themeobject->current_widget['param']['options'];

			if(@$themeobject->current_widget['param']['locations']['show_title'])
				$themeobject->output('<h3 class="widget-title">New Users</h3>');
				
			$themeobject->output('<div class="ra-new-users-widget">');
			$themeobject->output($this->cs_new_users((int)@$widget_opt['cs_nu_count'], (int)@$widget_opt['cs_nu_avatar']));
			$themeobject->output('</div>');
		}
	
	}
/*
	Omit PHP closing tag to help avoid accidental output
*/