<?php
	class cs_question_activity_widget {

		function cs_widget_form()
		{
			
			return array(
				'style' => 'wide',
				'fields' => array(
					'cs_qa_count' => array(
						'label' => 'Numbers of questions',
						'type' => 'number',
						'tags' => 'name="cs_qa_count" class="form-control"',
						'value' => '10',
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
		function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
		{
			$widget_opt = @$themeobject->current_widget['param']['options'];
			$content = cs_get_cache_question_activity((int)$widget_opt['cs_qa_count']);
			if(@$themeobject->current_widget['param']['locations']['show_title'])
				$themeobject->output('<h3 class="widget-title">'.qa_lang('cleanstrap/recent_activities').' <a href="'.qa_path_html('activity').'">'.qa_lang('cleanstrap/view_all').'</a></h3>');

			$themeobject->output('<div class="ra-question-activity-widget">');

			$q_list = $content;
			
			$themeobject->output('<ul class="activity-list">');
			foreach ($q_list as $list){
				$themeobject->output('<li class="clearfix '.(is_featured($list['raw']['postid']) ? ' featured' : '').'"><span class="fav-star icon-heart'.(@$list['raw']['userfavoriteq'] ? ' active' : '').'"></span><span class="post-status-c">'.cs_post_status($list).'</span><a href="'.$list['url'].'">'.$list['title'].'<span class="time">'.implode(' ', $list['when']).'</span><span class="ans-count total-'.$list['raw']['acount'].'">'.$list['raw']['acount'].'</span></a></li>');
			}
			$themeobject->output('</ul>');
			$themeobject->output('</div>');
		}
	}
/*
	Omit PHP closing tag to help avoid accidental output
*/