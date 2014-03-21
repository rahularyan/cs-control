<?php
	class cs_widget_text {

		function cs_widget_form()
		{
			
			return array(
				'style' => 'wide',
				'fields' => array(
					'title' => array(
						'label' => 'Text',
						'tags' => 'name="title" class="form-control"',
						'type' => 'input',
						'rows' => '5',
						'value' => 'Title of the widget',
					),
					'cs_t_text' => array(
						'label' => 'Text',
						'tags' => 'name="cs_t_text" class="form-control"',
						'type' => 'textarea',
						'rows' => '5',
						'value' => '',
					),
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
				$themeobject->output('<h3 class="widget-title">'.$widget_opt['title'].'</h3>');
				
			$themeobject->output('<div class="ra-text-widget clearfix">');
			$themeobject->output(@utf8_decode(urldecode($widget_opt['cs_t_text'])));
			$themeobject->output('</div>');
		}
	
	}
/*
	Omit PHP closing tag to help avoid accidental output
*/