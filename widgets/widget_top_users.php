<?php
	class cs_top_users_widget {

		function cs_widget_form()
		{
			
			return array(
				'style' => 'wide',
				'fields' => array(
					'cs_tc_count' => array(
						'label' => 'Numbers of user',
						'type' => 'number',
						'tags' => 'name="cs_tc_count" class="form-control"',
						'value' => '10',
					),
					'cs_tc_avatar' => array(
						'label' => 'Avatar Size',
						'type' => 'number',
						'tags' => 'name="cs_tc_avatar" class="form-control"',
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
		/* top users widget */
		function cs_top_users($limit = 5, $size){
			if (defined('QA_FINAL_WORDPRESS_INTEGRATE_PATH')){
				global $wpdb;
				$users = cs_get_cache('SELECT * FROM '.$wpdb->base_prefix.'users JOIN ^userpoints ON '.$wpdb->base_prefix.'users.ID=^userpoints.userid ORDER BY ^userpoints.points DESC LIMIT #',7200, $limit);
			}else
				$users = cs_get_cache('SELECT * FROM ^users JOIN ^userpoints ON ^users.userid=^userpoints.userid ORDER BY ^userpoints.points DESC LIMIT #',7200, $limit);
			
			$output = '<ul class="top-users-list clearfix">';

			foreach($users as $u){
				if(defined('QA_WORDPRESS_INTEGRATE_PATH')){
					require_once QA_INCLUDE_DIR.'qa-app-posts.php';
					$u['handle'] = qa_post_userid_to_handle($u['userid']);
				}
	
				$output .= '<li class="top-user clearfix">';
				$output .= cs_get_post_avatar( $u, $u['userid'] ,$size, true);
				$output .= '<div class="top-user-data">';
				
				$output .= '<span class="points">'.$u['points'].' '.qa_lang('cleanstrap/points').'</span>';
				$output .= '<a href="'.qa_path_html('user/'.$u['handle']).'" class="name">'.$u['handle'].'</a>';
				$output .= '<p class="counts"><span>'.qa_lang_sub('cleanstrap/x_questions', $u['qposts']).'</span> <span>'.qa_lang_sub('cleanstrap/x_answers', $u['aposts']).'</span><span>'.qa_lang_sub('cleanstrap/x_comments', $u['cposts']).'</span></p>';
				$output .= '</div>';
				$output .= '</li>';
			}
			$output .= '</ul>';
			return $output;
		}		

		function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
		{
			$widget_opt = $themeobject->current_widget['param']['options'];

			if(@$themeobject->current_widget['param']['locations']['show_title'])
				$themeobject->output('<h3 class="widget-title">Top Contributors</h3>');
				
			$themeobject->output('<div class="ra-tags-widget">');
			$themeobject->output($this->cs_top_users((int)@$widget_opt['cs_tc_count'], (int)@$widget_opt['cs_tc_avatar']));
			$themeobject->output('</div>');
		}
	
	}
/*
	Omit PHP closing tag to help avoid accidental output
*/