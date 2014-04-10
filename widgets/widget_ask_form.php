<?php


	class cs_ask_form_widget {
		
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
			
			$sdn =' style="display:none"';
			if (isset($qa_content['categoryids']))
				$params=array('cat' => end($qa_content['categoryids']));
			else
				$params=null;
			
			$tags = qa_get_tags_field_value('tags');
			if (empty($tags))
				$tags = array();
			$categoryid = qa_get_category_field_value('category');
			list($categories, $completetags)=qa_db_select_with_pending(
				qa_db_category_nav_selectspec($categoryid, true),
				qa_db_popular_tags_selectspec(0, QA_DB_RETRIEVE_COMPLETE_TAGS)
			);

			if (qa_using_categories() && count($categories)) {
				$pathcategories=qa_category_path($categories, $categoryid);
				$startpath='';
				foreach ($pathcategories as $category)
					$startpath.='/'.$category['categoryid'];
				$allownosub = qa_opt('allow_no_sub_category');
			}

			$separatorcomma=qa_opt('tag_separator_comma');
			
			$tags_field = qa_html(implode($separatorcomma ? ', ' : ' ', $tags));
			

			$category_options = '';
			foreach ($categories as $category)
				$category_options .= '<option value="' . $category['categoryid']. '">' . $category['title']. '</option>';
			$category_fields = '
				<select id="category_1" class="form-control" onchange="qa_category_select(\'category\');" name="category_1">
					<option value="" selected=""></option>
					' . $category_options . '
				</select>
				<div id="category_note"></div>';
			
			$themeobject->output('<div class="cs-ask-widget-form">');
			if(@$themeobject->current_widget['param']['locations']['show_title'])
				$themeobject->output('<h3 class="widget-title">'.qa_lang('cleanstrap/ask_question').'</h3>');
			
			$themeobject->output(
				'<form action="'.qa_path_html('ask', $params).'" method="post">',
					'<input type="text" name="title" class="form-control cs-ask-title" placeholder="'.qa_lang('cleanstrap/title_placeholder').'">');
			
			if (qa_using_categories() && count($categories))
				$themeobject->output($category_fields);
			
			$themeobject->output(
					'<textarea  name="content" class="form-control cs-ask-content" placeholder="'.qa_lang('cleanstrap/content_placeholder').'" rows="6"></textarea>');
			
			if(qa_using_tags())
			$themeobject->output(
					'<div class="tags-input"><input id="tags" class="form-control" type="text" value="" onmouseup="qa_tag_hints();" onkeyup="qa_tag_hints();" autocomplete="off" name="tags" placeholder="'.qa_lang('cleanstrap/tags_placeholder').'" />',
					'<span id="tag_examples_title"'.(count(@$exampletags) ? '' : $sdn).'>'.qa_lang_html('question/example_tags').'</span>',
					'<span id="tag_complete_title"'.$sdn.'>'.qa_lang_html('question/matching_tags').'</span><span id="tag_hints">',
					'<span id="tag_hints"></span></div>');
					
					
			if (!qa_is_logged_in()){
				$themeobject->output('<input type="text" class="form-control" placeholder="'.qa_lang_html('cleanstrap/your_name').'" name="name">');
				
				$themeobject->output('<div class="form-group"><label class="checkbox-inline"><input id="notify" type="checkbox" checked="" value="1" name="notify" /> Notify me</label></div>');
			
				$themeobject->output('<input type="text" id="email" class="form-control" placeholder="'.qa_lang_html('cleanstrap/your_email').'" name="email">');
			}	
			$themeobject->output('<button class="btn" type="submit">Submit question</button>',
					'<input type="hidden" value="'.qa_get_form_security_code('ask').'" name="code">',
					'<input type="hidden" value="'.qa_html(qa_opt('editor_for_qs')).'" name="editor">',
					'<input type="hidden" value="1" name="doask">',
				'</form>'
			);
			
			$themeobject->output('</div>');
		}
	
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/