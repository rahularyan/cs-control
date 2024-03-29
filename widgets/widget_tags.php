<?php
	class cs_tags_widget {

		function cs_widget_form()
		{
			
			return array(
				'style' => 'wide',
				'fields' => array(
					'cs_tags_count' => array(
						'label' => 'Numbers of tags',
						'type' => 'number',
						'tags' => 'name="cs_tags_count" class="form-control"',
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
			require_once QA_INCLUDE_DIR.'qa-db-selects.php';
			$widget_opt = @$themeobject->current_widget['param']['options'];

			if(@$themeobject->current_widget['param']['locations']['show_title'])
				$themeobject->output('<h3 class="widget-title">Tags <a href="'.qa_path_html('tags').'">View All</a></h3>');
				
			$to_show = (int)$widget_opt['cs_tags_count'];
			$populartags = cs_get_cache_popular_tags($to_show);
			
			reset($populartags);
			$themeobject->output('<div class="ra-tags-widget clearfix">');
	
			$blockwordspreg=qa_get_block_words_preg();			
			foreach ($populartags as $tag => $count) {
				if (count(qa_block_words_match_all($tag, $blockwordspreg)))
					continue; // skip censored tags

				$themeobject->output('<a href="'.qa_path_html('tag/'.$tag).'" class="widget-tag">'.qa_html($tag).'<span>'.$count.'</span></a>');
			}
			
			$themeobject->output('</div>');
		}
	
	}
/*
	Omit PHP closing tag to help avoid accidental output
*/