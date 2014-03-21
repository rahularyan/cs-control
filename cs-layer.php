<?php

	class qa_html_theme_layer extends qa_html_theme_base {
		
		function doctype(){	
			qa_html_theme_base::doctype();
			
			if(qa_get_logged_in_level() >= QA_USER_LEVEL_ADMIN)	{
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
		}
		
		function head_script()
		{
			qa_html_theme_base::head_script();
			if ($this->request == 'themeoptions') {
				$this->output('<script type="text/javascript" src="' . Q_THEME_URL . '/js/admin.js"></script>');
				$this->output('<script type="text/javascript" src="' . Q_THEME_URL . '/js/spectrum.js"></script>'); // color picker
				$this->output('<script type="text/javascript" src="' . Q_THEME_URL . '/js/chosen.jquery.min.js"></script>'); // Select list
				$this->output('<script type="text/javascript" src="' . Q_THEME_URL . '/js/jquery.uploadfile.min.js"></script>'); // File uploader
			}
		}
		
		
		function head_css()
		{
			qa_html_theme_base::head_css();
			if ($this->request == 'themeoptions') {
				$this->output('<link rel="stylesheet" type="text/css" href="' . Q_THEME_URL . '/css/admin.css"/>');
				$this->output('<link rel="stylesheet" type="text/css" href="' . Q_THEME_URL . '/css/spectrum.css"/>'); // color picker
			}
			
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
		
	}
