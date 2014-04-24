<?php
	class cs_followers_widget {

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
		
		
		function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
		{
			$widget_opt = $themeobject->current_widget['param']['options'];

			if(@$themeobject->current_widget['param']['locations']['show_title'])
				$themeobject->output('<h3 class="widget-title">Followers</h3>');
			
	
			$themeobject->output('<div class="ra-followers-widget">');
			$handle = qa_request_parts(1);
			$themeobject->output(cs_followers_list($handle[0]));
			$themeobject->output('</div>');
		}
	
	}
/*
	Omit PHP closing tag to help avoid accidental output
*/