<?php

	class qa_html_theme_layer extends qa_html_theme_base {
		
		function doctype(){	
			
			//content hook
			cs_event_hook('content', $this->content);
		
			$this->content['css_src']['cs_style'] = CS_THEME_URL.'/'.$this->css_name();
			$this->content['css_admin']['cs_style'] = CS_THEME_URL . '/css/admin.css';
		
			//enqueue script and style in content
			$hooked_script	= cs_event_hook('enqueue_scripts', array());
			$hooked_css 	= cs_event_hook('enqueue_css', $this->content['css_src']);

			if(is_array($hooked_script))
				$this->content['script_src'] =  $hooked_script;
				
			if(is_array($hooked_css))
				$this->content['css_src'] = $hooked_css;
			
			
			// unset old jQuery
			if(($key = array_search('<script src="../../qa-content/jquery-1.7.2.min.js" type="text/javascript"></script>', $this->content['script'])) !== false) {
				unset($this->content['script'][$key]);
			}

			qa_html_theme_base::doctype();

			if(qa_get_logged_in_level() >= QA_USER_LEVEL_ADMIN)	{

				// theme installation & update
				$version = qa_opt('cs_version');
				if( CS_VERSION > $version )
					qa_redirect('cs_installation');

				//show theme option menu if user is admin
				$this->content['navigation']['user']['themeoptions'] = array(
					'label' => qa_lang('cleanstrap/theme_options'),
					'url' => qa_path_html('themeoptions'),
					'icon' => 'icon-wrench'
				);
				$this->content['navigation']['user']['themewidgets'] = array(
					'label' => 'Theme Widgets',
					'url' => qa_path_html('themewidgets'),
					'icon' => 'icon-puzzle',
				);
				$this->content['navigation']['main']['featured'] = array(
					'label' => 'featured',
					'url' => qa_path_html('featured'),
					'icon' => 'icon-star',
				);
				if ($this->request == 'themeoptions') {
					$this->content['navigation']['user']['themeoptions']['selected'] = true;
					$this->content['navigation']['user']['selected']                 = true;
					
					$this->template = 'themeoptions';
				}
				if($this->request == 'themewidgets') {
					$this->content['navigation']['user']['themewidgets']['selected'] = true;
					$this->content['navigation']['user']['selected'] = true;
					$this->template = 'widgets';
				}
			
			}
			if(cs_event_hook('doctype', $this))
				$this->content = cs_event_hook('doctype', $this);

		}
		
		function head_script()
		{
			$this->output('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>');
			qa_html_theme_base::head_script();
			$this->output('<script> ajax_url = "' . CS_CONTROL_URL . '/ajax.php";</script>');
		
		
			$this->output('<script> theme_url = "' . CS_THEME_URL . '";</script>');
			

			if (qa_opt('cs_enable_gzip')) //Gzip
				$this->output('<script type="text/javascript" src="'.CS_THEME_URL.'/js/script_cache.js"></script>');
			else{
				if (isset($this->content['script_src']))
					foreach ($this->content['script_src'] as $script_src)
						$this->output('<script type="text/javascript" src="'.$script_src.'"></script>');
			}
				
	 
			
			
			//register a hook
			cs_event_hook('head_script', $this);
		
			
			
			if($this->cs_is_widget_active('CS Ask Form') && $this->template != 'ask'){
				$this->output('<script type="text/javascript" src="'.get_base_url().'/qa-content/qa-ask.js"></script>');
				
				list($categories, $completetags)=qa_db_select_with_pending(
					qa_db_category_nav_selectspec(qa_get('cat'), true),
					qa_db_popular_tags_selectspec(0, QA_DB_RETRIEVE_COMPLETE_TAGS)
				);
				
				if(qa_using_tags()){
					$completetags = qa_opt('do_complete_tags') ? array_keys($completetags) : array();
					$a_template='<a href="#" class="qa-tag-link" onclick="return qa_tag_click(this);">^</a>';
					$this->output('<script type="text/javascript">
						var qa_tag_template = \''.$a_template.'\',
							qa_tag_onlycomma = \''.(int)qa_opt('tag_separator_comma').'\',
							qa_tags_examples = "",
							qa_tags_complete = \''.qa_html(implode(',', $completetags)).'\',
							qa_tags_max = "'.(int)qa_opt('page_size_ask_tags').'";
					</script>');
				}
				
				
				if (qa_using_categories() && count($categories)) {
					$pathcategories=qa_category_path($categories, qa_get('cat'));
					$startpath='';
					foreach ($pathcategories as $category)
						$startpath.='/'.$category['categoryid'];
					$allownosub = qa_opt('allow_no_sub_category');
				}
				
				

				$this->output('
				<script type="text/javascript">
					var qa_cat_exclude=\'' . qa_opt('allow_no_sub_category') . '\';
					var qa_cat_allownone=1;
					var qa_cat_allownosub=' . (int)qa_opt('allow_no_sub_category') . ';
					var qa_cat_maxdepth=' . QA_CATEGORY_DEPTH . ';
					qa_category_select(\'category\', '.qa_js($startpath).');
				</script>');
			}
		}
		
		
		function head_css()
		{
			//qa_html_theme_base::head_css();
			
			if (qa_opt('cs_enable_gzip')) //Gzip
				$this->output('<link href="'. CS_THEME_URL . '/css/css_cache.css" rel="stylesheet" type="text/css">');
			else
				qa_html_theme_base::head_css();
				
			$this->output('<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
				<meta http-equiv="X-UA-Compatible" content="IE=edge"> ');
			$fav = qa_opt('cs_favicon_url');
			if( $fav )
				$this->output('<link rel="shortcut icon" href="' .  $fav . '" type="image/x-icon">');
			$this->output('

					<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
					<!--[if lte IE 9]>
						<link rel="stylesheet" type="text/css" href="' . CS_THEME_URL . '/css/ie.css"/>
					  <script src="' . CS_THEME_URL . '/js/html5shiv.js"></script>
					  <script src="' . CS_THEME_URL . '/js/respond.min.js"></script>
					<![endif]-->
				');
			
			
		   
			   $this->output('<link rel="stylesheet" type="text/css" href="http://i.icomoon.io/public/temp/e44daa8d54/CleanstrapNew/style.css"/>');
			  
			   $this->output('<link href="http://fonts.googleapis.com/css?family=Varela+Round|Open+Sans" rel="stylesheet" type="text/css">');
				
			
			
			if (qa_opt('cs_custom_style_created')){
				$this->output('<link rel="stylesheet" type="text/css" href="' . CS_THEME_URL . '/css/dynamic.css"/>');
			}else{
				$css = qa_opt('cs_custom_css');
				$this->output('<style>' . $css . '</style>');
			}
			
			/* 
			$googlefonts = json_decode(qa_opt('typo_googlefonts'), true);
			if (isset($googlefonts) && !empty($googlefonts))
				foreach ($googlefonts as $font_name) {
					$font_name = str_replace(" ", "+", $font_name);
					$link      = 'http://fonts.googleapis.com/css?family=' . $font_name;
					$this->output('<link href="' . $link . '" rel="stylesheet" type="text/css">');
				} */
				
			//register a hook
			cs_event_hook('head_css', $this);

			
		}
		
		function form_field($field, $style)
    {
        
        if (@$field['type'] == 'cs_qaads_multi_text') {
            $this->form_prefix($field, $style);
            $this->cs_qaads_form_multi_text($field, $style);
            $this->form_suffix($field, $style);
            
        } else {
            qa_html_theme_base::form_field($field, $style); // call back through to the default function
        }
    }
    
    function cs_qaads_form_multi_text($field, $style)
    {
        $this->output('<div class="ra-multitext"><div class="ra-multitext-append">');
        
        $i = 0;
        
        if ((strlen($field['value']) != 0) && is_array(unserialize($field['value']))) {
            $links = unserialize($field['value']);
            foreach ($links as $k => $ads) {
                
                $this->output('<div class="ra-multitext-list" data-id="' . $field['id'] . '">');
                $this->output('<input name="' . $field['id'] . '[' . $k . '][name]" type="text" value="' . $ads['name'] . '" class="ra-input name" placeholder="' . $field['input_label'] . '" />');
                
                $this->output('<textarea name="' . $field['id'] . '[' . $k . '][code]" class="ra-input code"  placeholder="Your advertisement code.." />' . str_replace('\\', '', base64_decode($ads['code'])) . '</textarea>');
                
                $this->output('<span class="ra-multitext-delete icon-trashcan btn btn-danger btn-xs">Remove</span>');
                $this->output('</div>');
            }
        } else {
            $this->output('<div class="ra-multitext-list" data-id="' . $field['id'] . '">');
            $this->output('<input name="' . $field['id'] . '[0][name]" type="text"  class="ra-input name" placeholder="' . $field['input_label'] . '" />');
            $this->output('<textarea name="' . $field['id'] . '[0][code]" class="ra-input code" placeholder="Your advertisement code.."></textarea>');
            
            $this->output('<span class="ra-multitext-delete icon-trashcan btn btn-danger btn-xs">Remove</span>');
            
            $this->output('</div>');
        }
        
        
        $this->output('</div></div>');
        $this->output('<span class="ra-multitext-add icon-plus btn btn-primary btn-xs" title="Add more">Add more</span>');
    }
    

    
    function q_list_items($q_items)
    {
        if (qa_opt('cs_enable_adv_list')) {
            $advs = json_decode(qa_opt('cs_advs'), true);
            foreach ($advs as $k => $adv) {
                $advertisments[@$adv['adv_location']][] = $adv;
            }
            $i = 0;
            foreach ($q_items as $q_item) {
                $this->q_list_item($q_item);
                if (isset($advertisments[$i])) {
                    foreach ($advertisments[$i] as $k => $adv) {
                        $this->output('<div class="cs-advertisement">');
                        if (isset($adv['adv_adsense']))
                            $this->output($adv['adv_adsense']);
                        else {
                            if (isset($adv['adv_image']))
                                $this->output('<a href="' . $adv['adv_image_link'] . '"><img src="' . $adv['adv_image'] . '" title="' . $adv['adv_image_title'] . '" alt="advert" /></a>');
                            else
                                $this->output('<a href="' . $adv['adv_image_link'] . '">' . $adv['adv_image_title'] . '</a>');
                        }
                        $this->output('</div>');
                    }
                }
                $i++;
            }
        } else
            qa_html_theme_base::q_list_items($q_items);
    }
	
	
	function install_page(){
		$content = $this->content;
		$this->output('<div class="clearfix qa-main container ' . (@$this->content['hidden'] ? ' qa-main-hidden' : '') . '">');
		$this->main_parts($content);
		$this->output('</div>');
		$this->output('<div class="install-footer">Copyright &copy; RahulAryan</div>');
	}
}
