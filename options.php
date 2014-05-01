<?php
/* don't allow this page to be requested directly from browser */	
if (!defined('QA_VERSION')) {
		header('Location: /');
		exit;
}


class cs_theme_options {
	var $directory;
	var $urltoroot;

	function load_module($directory, $urltoroot) {
		$this->directory=$directory;
		$this->urltoroot=$urltoroot;
	}

	function match_request($request)
	{
		if ($request=='themeoptions')
			return true;

		return false;
	}
	function process_request($request)
	{
	
		$saved = false;
		if (qa_clicked('cs_reset_button')) {
			reset_theme_options();
			$saved = 'Settings saved';
		}
		if (qa_clicked('cs_save_button')) {
			// General
			qa_opt('logo_url', qa_post_text('cs_logo_field'));
			qa_opt('cs_favicon_url', qa_post_text('cs_favicon_field'));
			qa_opt('cs_enable_gzip', (bool) qa_post_text('cs_enable_gzip'));
			qa_opt('cs_featured_image_width', (int) qa_post_text('cs_featured_image_width'));
			qa_opt('cs_featured_image_height', (int) qa_post_text('cs_featured_image_height'));
			qa_opt('cs_featured_thumbnail_width', (int) qa_post_text('cs_featured_thumbnail_width'));
			qa_opt('cs_featured_thumbnail_height', (int) qa_post_text('cs_featured_thumbnail_height'));
			qa_opt('cs_crop_x', qa_post_text('cs_crop_x'));
			qa_opt('cs_crop_y', qa_post_text('cs_crop_y'));
			
			// Layout
			qa_opt('cs_nav_position', qa_post_text('cs_nav_position'));
			qa_opt('cs_nav_fixed', (bool) qa_post_text('cs_nav_fixed'));
			qa_opt('cs_show_icon', (bool) qa_post_text('cs_show_icon'));
			qa_opt('cs_enable_ask_button', (bool) qa_post_text('cs_enable_ask_button'));
			qa_opt('cs_enable_category_nav', (bool) qa_post_text('cs_enable_category_nav'));
			qa_opt('cs_enable_clean_qlist', (bool) qa_post_text('cs_enable_clean_qlist'));
			qa_opt('cs_enable_default_home', (bool) qa_post_text('cs_enable_default_home'));
			qa_opt('cs_enable_except', (bool) qa_post_text('cs_enable_except'));
			qa_opt('cs_except_len', (int) qa_post_text('cs_except_len'));
			qa_opt('cs_enable_avatar_lists', (bool) qa_post_text('cs_enable_avatar_lists'));
			if (qa_opt('cs_enable_avatar_lists'))
				qa_opt('avatar_q_list_size', 35);
			else
				qa_opt('avatar_q_list_size', 0); // set avatar size to zero so Q2A won't load them
			qa_opt('show_view_counts', (bool) qa_post_text('cs_enable_views_lists'));
			qa_opt('cs_show_tags_list', (bool) qa_post_text('cs_show_tags_list'));
			qa_opt('cs_horizontal_voting_btns', (bool) qa_post_text('cs_horizontal_voting_btns'));
			qa_opt('cs_enble_back_to_top', (bool) qa_post_text('cs_enble_back_to_top'));
			qa_opt('cs_back_to_top_location', qa_post_text('cs_back_to_top_location'));
			
			// Styling
			qa_opt('cs_styling_rtl', (bool) qa_post_text('cs_styling_rtl'));
			qa_opt('cs_bg_select', qa_post_text('cs_bg_select'));
			qa_opt('cs_bg_color', qa_post_text('cs_bg_color'));
			qa_opt('cs_text_color', qa_post_text('cs_text_color'));
			qa_opt('cs_border_color', qa_post_text('cs_border_color'));
			qa_opt('cs_q_link_color', qa_post_text('cs_q_link_color'));
			qa_opt('cs_q_link_hover_color', qa_post_text('cs_q_link_hover_color'));
			qa_opt('cs_nav_link_color', qa_post_text('cs_nav_link_color'));
			qa_opt('cs_nav_link_color_hover', qa_post_text('cs_nav_link_color_hover'));
			qa_opt('cs_subnav_link_color', qa_post_text('cs_subnav_link_color'));
			qa_opt('cs_subnav_link_color_hover', qa_post_text('cs_subnav_link_color_hover'));
			qa_opt('cs_link_color', qa_post_text('cs_link_color'));
			qa_opt('cs_link_hover_color', qa_post_text('cs_link_hover_color'));
			qa_opt('cs_highlight_color', qa_post_text('cs_highlight_color'));
			qa_opt('cs_highlight_bg_color', qa_post_text('cs_highlight_bg_color'));
			qa_opt('cs_ask_btn_bg', qa_post_text('cs_ask_btn_bg'));
			require_once(CS_THEME_DIR . '/inc/styles.php'); // Generate customized CSS styling				
			
			// Typography
			$typo_options = $_POST['typo_option'];
			$google_fonts = array();
			foreach ($typo_options as $k => $options) {
				qa_opt('typo_options_family_' . $k, $options['family']);
				qa_opt('typo_options_style_' . $k, $options['style']);
				qa_opt('typo_options_size_' . $k, $options['size']);
				qa_opt('typo_options_linehight_' . $k, $options['linehight']);
				if ((isset($google_webfonts[$options['family']])) && (!(in_array($options['family'], $google_fonts)))){
					$google_fonts[] = $options['family'];
					qa_opt('typo_options_backup_' . $k, $options['backup']);
				}else{
					qa_opt('typo_options_backup_' . $k, '');
				}
			}
			qa_opt('typo_googlefonts', json_encode($google_fonts));
			
			// Social
			$SocialCount  = (int) qa_post_text('social_count'); // number of advertisement items
			$social_links = array();
			$i            = 0;
			while (($SocialCount > 0) and ($i < 100)) { // don't create an infinite loop
				if (null !== qa_post_text('social_link_' . $i)) {
					$social_links[$i]['social_link']  = qa_post_text('social_link_' . $i);
					$social_links[$i]['social_title'] = qa_post_text('social_title_' . $i);
					$social_links[$i]['social_icon']  = qa_post_text('social_icon_' . $i);
					if (($social_links[$i]['social_icon'] == '1') && (null !== qa_post_text('social_image_url_' . $i))) {
						$social_links[$i]['social_icon_file'] = qa_post_text('social_image_url_' . $i);
					}
					$SocialCount--;
				}
				$i++;
			}
			qa_opt('cs_social_list', json_encode($social_links));
			qa_opt('cs_social_enable', (bool) qa_post_text('cs_social_enable'));
			
			// Advertisement
			$AdsCount = (int) qa_post_text('adv_number'); // number of advertisement items
			$ads      = array();
			$i        = 0;
			while (($AdsCount > 0) and ($i < 100)) { // don't create an infinite loop
				if (null !== qa_post_text('adv_adsense_' . $i)) {
					// add adsense ads
					$ads[$i]['adv_adsense']  = qa_post_text('adv_adsense_' . $i);
					$ads[$i]['adv_location'] = qa_post_text('adv_location_' . $i);
					$AdsCount--;
				} elseif ((@getimagesize(@$_FILES['cs_adv_image_' . $i]['tmp_name']) > 0) or (null !== qa_post_text('adv_image_title_' . $i)) or (null !== qa_post_text('adv_image_link_' . $i)) or (null !== qa_post_text('adv_location_' . $i))) {
					// add static ads
					if (null !== qa_post_text('adv_image_url_' . $i)) {
						$ads[$i]['adv_image'] = qa_post_text('adv_image_url_' . $i);
					}
					$ads[$i]['adv_image_title'] = qa_post_text('adv_image_title_' . $i);
					$ads[$i]['adv_image_link']  = qa_post_text('adv_image_link_' . $i);
					$ads[$i]['adv_location']    = qa_post_text('adv_location_' . $i);
					$AdsCount--;
				}
				$i++;
			}
			qa_opt('cs_advs', json_encode($ads));
			qa_opt('cs_enable_adv_list', (bool) qa_post_text('cs_enable_adv_list'));
			qa_opt('cs_ads_below_question_title', base64_encode($_REQUEST['cs_ads_below_question_title']));
			qa_opt('cs_ads_after_question_content', base64_encode($_REQUEST['cs_ads_after_question_content']));
			
			// footer							
			qa_opt('cs_footer_copyright', qa_post_text('cs_footer_copyright'));
			
			
			$saved = true;
			$saved = 'Settings saved';
		}
		$qa_content=qa_content_prepare();

		
		$qa_content['site_title']="Theme Options";
		$qa_content['error']="";
		$qa_content['suggest_next']="";
		
		$qa_content['custom']= $this->opt_form();
		
		return $qa_content;	
	}
	
	function opt_form(){
		$output = '<form class="form-horizontal" enctype="multipart/form-data" method="post">';		
		$output .= '<div class="qa-part-tabs-nav">
		<ul class="ra-option-tabs nav nav-tabs">
			<li>
				<a href="#" data-toggle=".qa-part-form-tc-general">General</a>
			</li>
			<li>
				<a href="#" data-toggle=".qa-part-form-tc-layout">Layouts</a>
			</li>
			<li>
				<a href="#" data-toggle=".qa-part-form-tc-styling">Styling</a>
			</li>
			<li>
				<a href="#" data-toggle=".qa-part-form-tc-typo">Typography</a>
			</li>
			<li>
				<a href="#" data-toggle=".qa-part-form-tc-social">Social</a>
			</li>
			<li>
				<a href="#" data-toggle=".qa-part-form-tc-ads">Advertisements</a>
			</li>
		</ul>
	</div>';
		$output .= $this->opt_general();
		$output .= $this->opt_layout();
		$output .= $this->opt_styling();
		$output .= $this->opt_typography();
		$output .= $this->opt_social();
		$output .= $this->opt_ads();
		
		$output .= '<div class="form-button-sticky-footer">';
			$output .= '<div class="form-button-holder">';
				$output .= '<input type="submit" class="qa-form-tall-button btn-primary" title="" value="Save Changes" name="cs_save_button">';
				$output .= '<input type="submit" class="qa-form-tall-button" title="" value="Reset to Default" name="cs_reset_button">';
			$output .= '</div>';
		$output .= '</div>';
		$output .= '</form>';
		
		return $output;
	}
	
	function opt_general(){
		return $output ='<div class="qa-part-form-tc-general">
		<h3>General Settings</h3>
		<table class="qa-form-tall-table options-table">
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Logo
						<span class="description">Upload your own logo.</span>
					</th>
					<td class="qa-form-tall-data">
						' . (qa_opt('logo_url') ? '<img id="logo-preview" class="logo-preview img-thumbnail" src="' . qa_opt('logo_url') . '">' : '<img id="logo-preview" class="logo-preview img-thumbnail" style="display:none;" src="">') . '
						<div id="logo_uploader">Upload</div>
						<input id="cs_logo_field" type="hidden" name="cs_logo_field" value="' . qa_opt('logo_url') . '">
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Favicon
						<span class="description">favicon image (32px32px).</span>
					</th>
					<td class="qa-form-tall-data">
						' . (qa_opt('cs_favicon_url') ? '<img id="favicon-preview" class="favicon-preview img-thumbnail" src="' . qa_opt('cs_favicon_url') . '">' : '<img id="favicon-preview" class="favicon-preview img-thumbnail" style="display:none;" src="">') . '
						<div id="favicon_uploader">Upload</div>
						<input id="cs_favicon_field" type="hidden" name="cs_favicon_field" value="' . qa_opt('cs_favicon_url') . '">
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Compression
						<span class="description">Cache and compress assets</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
						'.(!qa_opt('cs_enable_gzip') ? '<a href="#" id="cache_assets" class="btn btn-default">Enable Compression</a>' : '<a href="#" id="cache_assets" class="active btn btn-danger">Disable Compression</a>').'
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr><td><h3>Featured Questions</h3></td></tr>
				<tr>
					<th class="qa-form-tall-label">
						Featured Image Width
						<span class="description">Question\'s Featured Image Width</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="input-group font-input">
							<input id="cs_featured_image_width" class="form-control featured-image-width" type="text" name="cs_featured_image_width" value="' . qa_opt('cs_featured_image_width') . '">
							<span class="input-group-addon">px</span>
						</div>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Featured Image Hight
						<span class="description">Question\'s Featured Image Hight</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="input-group font-input">
							<input id="cs_featured_image_height" class="form-control featured-image-height" type="text" name="cs_featured_image_height" value="' . qa_opt('cs_featured_image_height') . '">
							<span class="input-group-addon">px</span>
						</div>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Thumbnail Width
						<span class="description">Question\'s Featured Image Thumbnail Width</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="input-group font-input">
							<input id="cs_featured_thumbnail_width" class="form-control featured-thumb-width" type="text" name="cs_featured_thumbnail_width" value="' . qa_opt('cs_featured_thumbnail_width') . '">
							<span class="input-group-addon">px</span>
						</div>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Thumbnail Hight
						<span class="description">Question\'s Featured Image Hight</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="input-group font-input">
							<input id="cs_featured_thumbnail_height" class="form-control featured-thumb-height" type="text" name="cs_featured_thumbnail_height" value="' . qa_opt('cs_featured_thumbnail_height') . '">
							<span class="input-group-addon">px</span>
						</div>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Image Cropping X
						<span class="description">Crop Featured image from Right/Left</span>
					</th>
					<td class="qa-form-tall-label">
						<select id="cs_crop_x" name="cs_crop_y" >
							<option' . ((qa_opt('cs_crop_x') == 'l') ? ' selected' : '') . ' value="l">left</option>
							<option' . ((qa_opt('cs_crop_x') == 'c') ? ' selected' : '') . ' value="c">Center</option>
							<option' . ((qa_opt('cs_crop_x') == 'r') ? ' selected' : '') . ' value="r">right</option>
						</select>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Image Cropping Y
						<span class="description">Crop Featured image from Top/Bottom</span>
					</th>
					<td class="qa-form-tall-label">
						<select id="cs_crop_y" name="cs_crop_y" >
							<option' . ((qa_opt('cs_crop_y') == 't') ? ' selected' : '') . ' value="t">Top</option>
							<option' . ((qa_opt('cs_crop_y') == 'c') ? ' selected' : '') . ' value="c">Center</option>
							<option' . ((qa_opt('cs_crop_y') == 'b') ? ' selected' : '') . ' value="b">Bottom</option>
						</select>
					</td>
				</tr>
			</tbody>
			<tbody>
			<tr>
				<th class="qa-form-tall-label">
					Text at right side of footer
					<span class="description">you can add links or images by entering html code</span>
				</th>
				<td class="qa-form-tall-label">
					<input id="cs_footer_copyright" class="form-control" type="text" name="cs_footer_copyright" value="' . qa_opt('cs_footer_copyright') . '">
				</td>
			</tr>
		</tbody>
		</table>
	</div>';
	}

	function opt_layout(){
		return '<div class="qa-part-form-tc-layout">
		<h3>Layout Settings</h3>
		<table class="qa-form-tall-table options-table">
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Enable RTL Styling
						<span class="description">for Right to Left Languages</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_styling_rtl') ? ' checked=""' : '') . ' id="cs_styling_rtl" name="cs_styling_rtl">
							<label for="cs_styling_rtl">
							</label>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Navigation Position
						<span class="description">Where to show navigation</span>
					</th>
					<td class="qa-form-tall-label">
						<input class="theme-option-radio" type="radio"' . (qa_opt('cs_nav_position') == 'left' ? ' checked=""' : '') . ' id="cs_nav_position" name="cs_nav_position" value="left">
						   <label for="cs_nav_position">Left</label>
						<input class="theme-option-radio" type="radio"' . (qa_opt('cs_nav_position') == 'top' ? ' checked=""' : '') . ' id="cs_nav_position_top" name="cs_nav_position" value="top">
						   <label for="cs_nav_position_top">Top</label> 
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Fixed Navigation
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_nav_fixed') ? ' checked=""' : '') . ' id="cs_nav_fixed" name="cs_nav_fixed">
								<label for="cs_nav_fixed"></label>
						</div>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Show menu Icon
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_show_icon') ? ' checked=""' : '') . ' id="cs_show_icon" name="cs_show_icon">
								<label for="cs_show_icon"></label>
						</div>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Ask Button
						<span class="description">Enable to show Ask Button in header.</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_enable_ask_button') ? ' checked=""' : '') . ' id="cs_enable_ask_button" name="cs_enable_ask_button">
								<label for="cs_enable_ask_button"></label>
						</div>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Categories Drop down
						<span class="description">Enable to show Categories List in drop down menu in header.</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_enable_category_nav') ? ' checked=""' : '') . ' id="cs_enable_category_nav" name="cs_enable_category_nav">
								<label for="cs_enable_category_nav"></label>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr><td><h3>Home Page</h3></td></tr>
				<tr>
					<th class="qa-form-tall-label">
						Toggle question list in home
						<span class="description">Toggle if you want to show default question list in home page</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_enable_default_home') ? ' checked=""' : '') . ' id="cs_enable_default_home" name="cs_enable_default_home">
								<label for="cs_enable_default_home"></label>
						</div>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Clean Question List
						<span class="description">Enable to switch to default question list.</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_enable_clean_qlist') ? ' checked=""' : '') . ' id="cs_enable_clean_qlist" name="cs_enable_clean_qlist">
								<label for="cs_enable_clean_qlist"></label>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr><td><h3>Question Lists</h3></td></tr>
				<tr>
					<th class="qa-form-tall-label">
						Question Excerpt
						<span class="description">Toggle question description in question lists.</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_enable_except') ? ' checked=""' : '') . ' id="cs_enable_except" name="cs_enable_except">
								<label for="cs_enable_except"></label>
						</div>
					</td>
				</tr>
				<tr id="cs_except_length">
					<th class="qa-form-tall-label">
						Excerpt Length
						<span class="description">Length of questions description in question lists</span>
					</th>
					<td class="qa-form-tall-label">
						<input class="qa-form-wide-number" type="text" value="' . qa_opt('cs_except_len') . '"  id="cs_except_len" name="cs_except_len">
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Avatars in lists
						<span class="description">Toggle avatars in question lists.</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_enable_avatar_lists') ? ' checked=""' : '') . ' id="cs_enable_avatar_lists" name="cs_enable_avatar_lists">
								<label for="cs_enable_avatar_lists"></label>
						</div>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						View Count
						<span class="description">Toggle View Count in question lists.</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('show_view_counts') ? ' checked=""' : '') . ' id="cs_enable_views_lists" name="cs_enable_views_lists">
								<label for="cs_enable_views_lists"></label>
						</div>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Question Tags
						<span class="description">Toggle Tags in question lists.</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_show_tags_list') ? ' checked=""' : '') . ' id="cs_show_tags_list" name="cs_show_tags_list">
								<label for="cs_show_tags_list"></label>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Horizontal Voting Buttons
						<span class="description">Switch between horizontal and vertical voting buttons</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_horizontal_voting_btns') ? ' checked=""' : '') . ' id="cs_horizontal_voting_btns" name="cs_horizontal_voting_btns">
							<label for="cs_horizontal_voting_btns">
							</label>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Back to Top Button
						<span class="description">Enable Back to Top</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
								<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_enble_back_to_top') ? ' checked=""' : '') . ' id="cs_enble_back_to_top" name="cs_enble_back_to_top">
							<label for="cs_enble_back_to_top">
							</label>
						</div>
					</td>
					</tr>
					<tr id="back_to_top_location_container" ' . (qa_opt('cs_enble_back_to_top') ? '' : ' style="display:none;"') . '>
					<th class="qa-form-tall-label">
						Back To Top\'s Position
						<span class="description">Back To Top button\'s Position</span>
					</th>
					<td class="qa-form-tall-label">
						<input class="theme-option-radio" type="radio"' . (qa_opt('cs_back_to_top_location') == 'nav' ? ' checked=""' : '') . ' id="cs_back_to_top_nav" name="cs_back_to_top_location" value="nav">
						   <label for="cs_back_to_top_nav">Under Navigation</label>
						<input class="theme-option-radio" type="radio"' . (qa_opt('cs_back_to_top_location') == 'right' ? ' checked=""' : '') . ' id="cs_back_to_top_right" name="cs_back_to_top_location" value="right">
						   <label for="cs_back_to_top_right">Bottom Right</label> 
					</td>
				</tr>
			</tbody>
		</table>
	</div>';
	}

	function opt_styling(){
			$p_path       = CS_THEME_DIR . '/images/patterns';
            $bg_images    = array();
            $list_options = '';
            $files        = scandir($p_path, 1);
            $list_options .= '<option class="icon-wrench" value="bg_default"' . ((qa_opt('cs_bg_select') == 'bg_default') ? ' selected' : '') . '>Default Background</option>';
            $list_options .= '<option class="icon-wrench" value="bg_color"' . ((qa_opt('cs_bg_select') == 'bg_color') ? ' selected' : '') . '>only use Background Color</option>';
            //@$bg_images[qa_opt('qat_bg_image_index')
            foreach ($files as $file)
                if (!((empty($file)) or ($file == '.') or ($file == '..'))) {
                    $image       = preg_replace("/\\.[^.]*$/", "", $file);
                    $bg_images[] = $image;
                    $list_options .= '<option value="' . $image . '"' . ((qa_opt('cs_bg_select') == $image) ? ' selected' : '') . '>' . $image . '</option>';
                }
		$bg_select               = '<select id="cs_bg_select" name="cs_bg_select" class="qa-form-wide-select">' . $list_options . '</select>';
		return '<div class="qa-part-form-tc-styling">
		<h3>Colors</h3>
		<table class="qa-form-tall-table options-table">
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Body background
					</th>
					<td class="qa-form-tall-label">
						' . $bg_select . '
					</td>
				</tr>
				<tr id="bg-color-container"' . ((qa_opt('cs_bg_select') == 'bg_color') ? '' : ' style="display:none;"') . '>
					<th class="qa-form-tall-label">
						Body Font Color
					</th>
					<td class="qa-form-tall-label">
						<input type="colorpicker" class="form-control" value="' . qa_opt('cs_bg_color') . '" id="cs_bg_color" name="cs_bg_color">
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Text color
					</th>
					<td class="qa-form-tall-label">
						<input type="colorpicker" class="form-control" value="' . qa_opt('cs_text_color') . '" id="cs_text_color" name="cs_text_color">
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Border color
					</th>
					<td class="qa-form-tall-label">
						<input type="colorpicker" class="form-control" value="' . qa_opt('cs_border_color') . '" id="cs_border_color" name="cs_border_color">
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Link color
					</th>
					<td class="qa-form-tall-label">
						Link Color<input type="colorpicker" class="form-control" value="' . qa_opt('cs_link_color') . '" id="cs_link_color" name="cs_link_color">
						Hover Color<input type="colorpicker" class="form-control" value="' . qa_opt('cs_link_hover_color') . '" id="cs_link_hover_color" name="cs_link_hover_color">
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Question Link color
					</th>
					<td class="qa-form-tall-label">
						Link Color<input type="colorpicker" class="form-control" value="' . qa_opt('cs_q_link_color') . '" id="cs_q_link_color" name="cs_q_link_color">
						Hover Color<input type="colorpicker" class="form-control" value="' . qa_opt('cs_q_link_hover_color') . '" id="cs_q_link_hover_color" name="cs_q_link_hover_color">
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Navigation Link color
					</th>
					<td class="qa-form-tall-label">
						Text Color<input type="colorpicker" class="form-control" value="' . qa_opt('cs_nav_link_color') . '" id="cs_nav_link_color" name="cs_nav_link_color">
						Hover Color<input type="colorpicker" class="form-control" value="' . qa_opt('cs_nav_link_color_hover') . '" id="cs_nav_link_color_hover" name="cs_nav_link_color_hover">
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Sub Navigation Link color
					</th>
					<td class="qa-form-tall-label">
						Text Color<input type="colorpicker" class="form-control" value="' . qa_opt('cs_subnav_link_color') . '" id="cs_subnav_link_color" name="cs_subnav_link_color">
						Hover Color<input type="colorpicker" class="form-control" value="' . qa_opt('cs_subnav_link_color_hover') . '" id="cs_subnav_link_color_hover" name="cs_subnav_link_color_hover">
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Highlight Text color
					</th>
					<td class="qa-form-tall-label">
						<input type="colorpicker" class="form-control" value="' . qa_opt('cs_highlight_color') . '" id="cs_highlight_color" name="cs_highlight_color">
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Highlight background color
					</th>
					<td class="qa-form-tall-label">
						<input type="colorpicker" class="form-control" value="' . qa_opt('cs_highlight_bg_color') . '" id="cs_highlight_bg_color" name="cs_highlight_bg_color">
					</td>
				</tr>
			</tbody>
		</table>
		<h3>Background color of questions</h3>
		<table class="qa-form-tall-table options-table">
			
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Ask button background
						<span class="description">ADD DETAIL.</span>
					</th>
					<td class="qa-form-tall-label">
						<input type="colorpicker" class="form-control" value="' . qa_opt('cs_ask_btn_bg') . '" id="cs_ask_btn_bg" name="cs_ask_btn_bg">
					</td>
				</tr>
			</tbody>
		</table>
	</div>';
	}

	function opt_typography(){
		return '<div class="qa-part-form-tc-typo">
		<table class="qa-form-tall-table options-table">
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Body
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="body" name="typo_option[body][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_body')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[body][style]" class="chosen-select font-style" data-font-option-type="body">
						' . $this->get_font_style_options(qa_opt('typo_options_family_body'), qa_opt('typo_options_style_body')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_body') . '" id="typo_size" name="typo_option[body][size]" type="text" class="form-control font-size" data-font-option-type="body">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_body') . '" id="typo_lineheight" name="typo_option[body][linehight]" type="text" class="form-control font-linehight" data-font-option-type="body">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[body][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="body">' . $this->get_normal_font_options(qa_opt('typo_options_backup_body')) . '</select>
						<span class="font-demo">The quick brown fox jumps over the lazy dog.</span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						H1
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="h1" name="typo_option[h1][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_h1')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[h1][style]" class="chosen-select font-style" data-font-option-type="h1">
						' . $this->get_font_style_options(qa_opt('typo_options_family_h1'), qa_opt('typo_options_style_h1')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_h1') . '" id="typo_size" name="typo_option[h1][size]" type="text" class="form-control font-size" data-font-option-type="h1">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_h1') . '" id="typo_lineheight" name="typo_option[h1][linehight]" type="text" class="form-control font-linehight" data-font-option-type="h1">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[h1][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="h1">' . $this->get_normal_font_options(qa_opt('typo_options_backup_h1')) . '</select>
						<span class="font-demo"><h1>The quick brown fox jumps over the lazy dog.</h1></span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						H2
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="h2" name="typo_option[h2][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_h2')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[h2][style]" class="chosen-select font-style" data-font-option-type="h2">
						' . $this->get_font_style_options(qa_opt('typo_options_family_h2'), qa_opt('typo_options_style_h2')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_h2') . '" id="typo_size" name="typo_option[h2][size]" type="text" class="form-control font-size" data-font-option-type="h2">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_h2') . '" id="typo_lineheight" name="typo_option[h2][linehight]" type="text" class="form-control font-linehight" data-font-option-type="h2">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[h2][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="h2">' . $this->get_normal_font_options(qa_opt('typo_options_backup_h2')) . '</select>
						<span class="font-demo"><h2>The quick brown fox jumps over the lazy dog.</h2></span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						H3
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="h3" name="typo_option[h3][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_h3')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[h3][style]" class="chosen-select font-style" data-font-option-type="h3">
						' . $this->get_font_style_options(qa_opt('typo_options_family_h3'), qa_opt('typo_options_style_h3')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_h3') . '" id="typo_size" name="typo_option[h3][size]" type="text" class="form-control font-size" data-font-option-type="h3">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_h3') . '" id="typo_lineheight" name="typo_option[h3][linehight]" type="text" class="form-control font-linehight" data-font-option-type="h3">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[h3][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="h3">' . $this->get_normal_font_options(qa_opt('typo_options_backup_h3')) . '</select>
						<span class="font-demo"><h3>The quick brown fox jumps over the lazy dog.</h3></span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						H4
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="h4" name="typo_option[h4][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_h4')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[h4][style]" class="chosen-select font-style" data-font-option-type="h4">
						' . $this->get_font_style_options(qa_opt('typo_options_family_h4'), qa_opt('typo_options_style_h4')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_h4') . '" id="typo_size" name="typo_option[h4][size]" type="text" class="form-control font-size" data-font-option-type="h4">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_h4') . '" id="typo_lineheight" name="typo_option[h4][linehight]" type="text" class="form-control font-linehight" data-font-option-type="h4">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[h4][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="h4">' . $this->get_normal_font_options(qa_opt('typo_options_backup_h4')) . '</select>
						<span class="font-demo"><h4>The quick brown fox jumps over the lazy dog.</h4></span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						h5
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="h5" name="typo_option[h5][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_h5')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[h5][style]" class="chosen-select font-style" data-font-option-type="h5">
						' . $this->get_font_style_options(qa_opt('typo_options_family_h5'), qa_opt('typo_options_style_h5')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_h5') . '" id="typo_size" name="typo_option[h5][size]" type="text" class="form-control font-size" data-font-option-type="h5">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_h5') . '" id="typo_lineheight" name="typo_option[h5][linehight]" type="text" class="form-control font-linehight" data-font-option-type="h5">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[h5][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="h5">' . $this->get_normal_font_options(qa_opt('typo_options_backup_h5')) . '</select>
						<span class="font-demo"><h5>The quick brown fox jumps over the lazy dog.</h5></span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Paragraphs
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="p" name="typo_option[p][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_p')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[p][style]" class="chosen-select font-style" data-font-option-type="p">
						' . $this->get_font_style_options(qa_opt('typo_options_family_p'), qa_opt('typo_options_style_p')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_p') . '" id="typo_size" name="typo_option[p][size]" type="text" class="form-control font-size" data-font-option-type="p">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_p') . '" id="typo_lineheight" name="typo_option[p][linehight]" type="text" class="form-control font-linehight" data-font-option-type="p">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[p][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="p">' . $this->get_normal_font_options(qa_opt('typo_options_backup_p')) . '</select>
						<span class="font-demo"><p>The quick brown fox jumps over the lazy dog.</p></span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Span
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="span" name="typo_option[span][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_span')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[span][style]" class="chosen-select font-style" data-font-option-type="span">
						' . $this->get_font_style_options(qa_opt('typo_options_family_span'), qa_opt('typo_options_style_span')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_span') . '" id="typo_size" name="typo_option[span][size]" type="text" class="form-control font-size" data-font-option-type="span">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_span') . '" id="typo_lineheight" name="typo_option[span][linehight]" type="text" class="form-control font-linehight" data-font-option-type="span">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[span][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="span">' . $this->get_normal_font_options(qa_opt('typo_options_backup_span')) . '</select>
						<span class="font-demo"><span>The quick brown fox jumps over the lazy dog.</span></span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Quote
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="quote" name="typo_option[quote][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_quote')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[quote][style]" class="chosen-select font-style" data-font-option-type="quote">
						' . $this->get_font_style_options(qa_opt('typo_options_family_quote'), qa_opt('typo_options_style_quote')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_quote') . '" id="typo_size" name="typo_option[quote][size]" type="text" class="form-control font-size" data-font-option-type="quote">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_quote') . '" id="typo_lineheight" name="typo_option[quote][linehight]" type="text" class="form-control font-linehight" data-font-option-type="quote">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[quote][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="quote">' . $this->get_normal_font_options(qa_opt('typo_options_backup_quote')) . '</select>
						<span class="font-demo"><blockquote>The quick brown fox jumps over the lazy dog.</blockquote></span>
					</td>
				</tr>
			</tbody>
				<tr>
					<th class="qa-form-tall-label">
						Question Title
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="qtitle" name="typo_option[qtitle][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_qtitle')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[qtitle][style]" class="chosen-select font-style" data-font-option-type="qtitle">
						' . $this->get_font_style_options(qa_opt('typo_options_family_qtitle'), qa_opt('typo_options_style_qtitle')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_qtitle') . '" id="typo_size" name="typo_option[qtitle][size]" type="text" class="form-control font-size" data-font-option-type="qtitle">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_qtitle') . '" id="typo_lineheight" name="typo_option[qtitle][linehight]" type="text" class="form-control font-linehight" data-font-option-type="qtitle">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[qtitle][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="qtitle">' . $this->get_normal_font_options(qa_opt('typo_options_backup_qtitle')) . '</select>
						<span class="font-demo"><h2 class="question-title">The quick brown fox jumps over the lazy dog.</h2></span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Question Title Link
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="qtitlelink" name="typo_option[qtitlelink][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_qtitlelink')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[qtitlelink][style]" class="chosen-select font-style" data-font-option-type="qtitlelink">
						' . $this->get_font_style_options(qa_opt('typo_options_family_qtitlelink'), qa_opt('typo_options_style_qtitlelink')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_qtitlelink') . '" id="typo_size" name="typo_option[qtitlelink][size]" type="text" class="form-control font-size" data-font-option-type="qtitlelink">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_qtitlelink') . '" id="typo_lineheight" name="typo_option[qtitlelink][linehight]" type="text" class="form-control font-linehight" data-font-option-type="qtitlelink">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[qtitlelink][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="qtitlelink">' . $this->get_normal_font_options(qa_opt('typo_options_backup_qtitlelink')) . '</select>
						<span class="font-demo"><div class="qa-q-item-title" style="font-size: inherit ! important; font-family: inherite ! important; font-style: inherit ! important; line-height: inherit ! important; font-weight: inherit ! important;"><a href="#" style="font-size: inherit ! important;">The quick brown fox jumps over the lazy dog.</a></div></span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Post Content
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="pcontent" name="typo_option[pcontent][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_pcontent')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[pcontent][style]" class="chosen-select font-style" data-font-option-type="pcontent">
						' . $this->get_font_style_options(qa_opt('typo_options_family_pcontent'), qa_opt('typo_options_style_pcontent')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_pcontent') . '" id="typo_size" name="typo_option[pcontent][size]" type="text" class="form-control font-size" data-font-option-type="pcontent">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_pcontent') . '" id="typo_lineheight" name="typo_option[pcontent][linehight]" type="text" class="form-control font-linehight" data-font-option-type="pcontent">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[pcontent][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="pcontent">' . $this->get_normal_font_options(qa_opt('typo_options_backup_pcontent')) . '</select>
						<span class="font-demo"><div class="entry-content">The quick brown fox jumps over the lazy dog.</div></span>
					</td>
				</tr>
				<tr>
					<th class="qa-form-tall-label">
						Navigation Links
					</th>
					<td class="qa-form-tall-label">
						<select data-placeholder="Choose a font" class="chosen-select font-family" data-font-option-type="mainnav" name="typo_option[mainnav][family]" id="typo_family">' . $this->get_font_options(qa_opt('typo_options_family_mainnav')) . '</select>
						<select data-placeholder="font style" id="typo_style" name="typo_option[mainnav][style]" class="chosen-select font-style" data-font-option-type="mainnav">
						' . $this->get_font_style_options(qa_opt('typo_options_family_mainnav'), qa_opt('typo_options_style_mainnav')) . '
						</select>
						<div class="input-group font-input" title="Font Size">
							<span class="input-group-addon">Font Size</span>
							<input value="' . qa_opt('typo_options_size_mainnav') . '" id="typo_size" name="typo_option[mainnav][size]" type="text" class="form-control font-size" data-font-option-type="mainnav">
							<span class="input-group-addon">px</span>
						</div>						
						<div class="input-group font-input" title="Line Height" >
							<span class="input-group-addon">Line Height</span>
							<input value="' . qa_opt('typo_options_linehight_mainnav') . '" id="typo_lineheight" name="typo_option[mainnav][linehight]" type="text" class="form-control font-linehight" data-font-option-type="mainnav">
							<span class="input-group-addon">px</span>
						</div>
						<select data-placeholder="Font Backup" name="typo_option[mainnav][backup]" id="typo_backup" class="chosen-select font-family-backup" data-font-option-type="mainnav">' . $this->get_normal_font_options(qa_opt('typo_options_backup_mainnav')) . '</select>
						<span class="font-demo">
							<div class="left-sidebar">
								<ul class="qa-nav-main-list" style="font-style: inherit; font-weight: inherit;">
									<li class="qa-nav-main-item qa-nav-main-questions">
										<a class="icon-question qa-nav-main-link" href="#" style="font-style: inherit !important;font-size: inherit ! important;font-weight: inherit !important;">Questions</a>
									</li>
								</ul>
							</div>						
						</span>
					</td>
				</tr>

			<tbody>
			</tbody>
		</table>
	</div>';
	}
	
	function opt_social(){
		$i              = 0;
		$social_content = '';
		$social_fields  = json_decode(qa_opt('cs_social_list'), true);
		if (isset($social_fields))
			foreach ($social_fields as $k => $social_field) {
				$list_options = '<option class="icon-wrench" value="1"' . ((@$social_field['social_icon'] == '1') ? ' selected' : '') . '>Upload Social Icon</option>';
				foreach (cs_social_icons() as $icon => $name) {

					$list_options .= '<option class="' . $icon . '" value="' . $icon . '"' . (($icon == @$social_field['social_icon']) ? ' selected' : '') . '>' . $name . '</option>';
				}
				$social_icon_list = '<select id="social_icon_' . $i . '" name="social_icon_' . $i . '" class="qa-form-wide-select  social-select" sociallistid="' . $i . '">' . $list_options . '</select>';
				if (isset($social_field['social_link'])) {
					if ((!empty($social_field['social_icon_file'])) and (@$social_field['social_icon'] == '1'))
						$image = '<img id="social_image_preview_' . $i . '" src="' . $social_field['social_icon_file'] . '" class="social-preview img-thumbnail">';
					else
						$image = '<img id="social_image_preview_' . $i . '" src="" class="social-preview img-thumbnail" style="display:none;">';
					$social_content .= '<tr id="soical_box_' . $i . '">
		<th class="qa-form-tall-label">
			Social Link #' . ($i + 1) . '
			<span class="description">choose Icon and link to your social profile</span>
		</th>
		<td class="qa-form-tall-data">
			<span class="description">Social Profile Link</span>
			<input class="form-control" id="social_link_' . $i . '" name="social_link_' . $i . '" type="text" value="' . $social_field['social_link'] . '">
			<span class="description">Link Title</span>
			<input class="form-control" id="social_title_' . $i . '" name="social_title_' . $i . '" type="text" value="' . $social_field['social_title'] . '">
			<span class="description">Choose Social Icon</span>
			' . $social_icon_list . '
			<div class="social_icon_file_' . $i . '"' . ((@$social_field['social_icon'] == '1') ? '' : ' style="display:none;"') . '>
				<span class="description">upload Social Icon</span>
				' . $image . '
				<div id="social_image_uploader_' . $i . '">Upload Icon</div>
				<input type="hidden" value="' . @$social_field['social_icon_file'] . '" id="social_image_url_' . $i . '" name="social_image_url_' . $i . '">
			</div>
			<button id="social_remove" class="qa-form-tall-button social_remove pull-right btn" type="submit" name="social_remove" socialid="' . $i . '">Remove This Link</button>
		</tr>';
				}
				$i++;
			}
		$social_content .= '<input type="hidden" value="' . $i . '" id="social_count" name="social_count">';
		return '<div class="qa-part-form-tc-social">
		<table class="qa-form-tall-table options-table">
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Social Toolbar
						<span class="description">Enable social links in your site\'s header.</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
							<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_social_enable') ? ' checked=""' : '') . ' id="cs_social_enable" name="cs_social_enable">
							<label for="cs_social_enable"></label>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Add New Social Links
						<span class="description">Add a new social link</span>
					</th>
					<td class="qa-form-tall-label text-center">
						<button type="submit" id="add_social" name="add_social" class="qa-form-tall-button btn">Add Social Links</button>
					</td>
				</tr>
			</tbody>
			<tbody id="social_container">
				' . $social_content . '	
			</tbody>
		</table>
	</div>';
	}
	
	function opt_ads(){
	
	 $advs        = json_decode(qa_opt('cs_advs'), true);
            $i           = 0;
            $adv_content = '';
            if (isset($advs))
                foreach ($advs as $k => $adv) {
                    if (true) { // use list to choose location of advertisement
                        $list_options = '';
                        for ($count = 1; $count <= qa_opt('page_size_qs'); $count++) {
                            $list_options .= '<option value="' . $count . '"' . (($count == @$adv['adv_location']) ? ' selected' : '') . '>' . $count . '</option>';
                        }
                        $adv_location = '<select id="adv_location_' . $i . '" name="adv_location_' . $i . '" class="qa-form-wide-select">' . $list_options . '</select>';
                    } else {
                        $adv_location = '<input id="adv_location_' . $i . '" name="adv_location_' . $i . '" class="form-control" value="" placeholder="Position of advertisements in list" />';
                    }
                    if (isset($adv['adv_adsense'])) {
                        $adv_content .= '<tr id="adv_box_' . $i . '">
			<th class="qa-form-tall-label">
				Advertisment #' . ($i + 1) . '
				<span class="description">Google Adsense Code</span>
			</th>
			<td class="qa-form-tall-data">
				<input class="form-control" id="adv_adsense_' . $i . '" name="adv_adsense_' . $i . '" type="text" value="' . $adv['adv_adsense'] . '">
				<span class="description">Display After this number of questions</span>
				' . $adv_location . '
				<button advid="' . $i . '" id="advremove" name="advremove" class="qa-form-tall-button advremove pull-right btn" type="submit">Remove This Advertisement</button></td>
			</tr>';
                    } else {
                        if (!empty($adv['adv_image']))
                            $image = '<img id="adv_preview_' . $i . '" src="' . $adv['adv_image'] . '" class="adv-preview img-thumbnail">';
                        else
                            $image = '<img id="adv_preview_' . $i . '" src="" class="adv-preview img-thumbnail" style="display:none;">';
                        $adv_content .= '<tr id="adv_box_' . $i . '">
			<th class="qa-form-tall-label">
				Advertisement #' . ($i + 1) . '
				<span class="description">static advertisement</span>
			</th>
			<td class="qa-form-tall-data">
				<div class="clearfix"></div>
				' . $image . '
				<div class="clearfix"></div>
				<div id="adv_image_uploader_' . $i . '">Upload Icon</div>
				<input type="hidden" value="' . @$adv['social_icon_file'] . '" id="social_image_url_' . $i . '" name="social_image_url_' . $i . '">
				
				<span class="description">Image Title</span>
				<input class="form-control" type="text" id="adv_image_title_' . $i . '" name="adv_image_title_' . $i . '" value="' . @$adv['adv_image_title'] . '">
				<span class="description">Target link</span>
				
				<input class="form-control" id="adv_image_link_' . $i . '" name="adv_image_link_' . $i . '" type="text" value="' . @$adv['adv_image_link'] . '">
				<span class="description">Display After this number of questions</span>
				
				' . $adv_location . '
				
				<input type="hidden" value="' . @$adv['adv_image'] . '" id="adv_image_url_' . $i . '" name="adv_image_url_' . $i . '">
				
				<button advid="' . $i . '" id="advremove" name="advremove" class="qa-form-tall-button advremove pull-right btn" type="submit">Remove This Advertisement</button>
			</td>
			</tr>';
                    }
                    $i++;
                }
            $adv_content .= '<input type="hidden" value="' . $i . '" id="adv_number" name="adv_number">';
            $adv_content .= '<input type="hidden" value="' . qa_opt('page_size_qs') . '" id="question_list_count" name="question_list_count">';
		return '<div class="qa-part-form-tc-ads">
		<h3>Advertisment in question list</h3>
		<table class="qa-form-tall-table options-table">
			<tbody>
				<tr>
					<th class="qa-form-tall-label">
						Advertisement in Lists
						<span class="description">Enable Advertisement in question lists</span>
					</th>
					<td class="qa-form-tall-label">
						<div class="on-off-checkbox-container">
							<input type="checkbox" class="on-off-checkbox" value="1"' . (qa_opt('cs_enable_adv_list') ? ' checked=""' : '') . ' id="cs_enable_adv_list" name="cs_enable_adv_list">
							<label for="cs_enable_adv_list"></label>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody id="ads_container" ' . (qa_opt('cs_enable_adv_list') ? '' : ' style="display:none;"') . '>
				<tr>
					<th class="qa-form-tall-label">
						Add Advertisement
						<span class="description">Create advertisement with static or Google Adsense</span>
					</th>
					<td class="qa-form-tall-label text-center">
						<button type="submit" id="add_adv" name="add_adv" class="qa-form-tall-button btn">Add Advertisement</button>
						<button type="submit" id="add_adsense" name="add_adsense" class="qa-form-tall-button btn">Add Google Adsense</button>
					</td>
				</tr>
			' . $adv_content . '
			</tbody>
			
		</table>
		<h3>Advertisement in question page</h3>
		<table class="qa-form-tall-table options-table">
			<tbody><tr>
				<th class="qa-form-tall-label">
					Under question title
					<span class="description">Advertisement below Question Title</span>
				</th>
				<td class="qa-form-tall-label">
					<textarea class="form-control" cols="40" rows="5" name="cs_ads_below_question_title">' . base64_decode(qa_opt('cs_ads_below_question_title')) . '</textarea>
				</td>
			</tr>
			<tr>
				<th class="qa-form-tall-label">
					After question content
					<span class="description">this advertisement will show up between Question & Answer</span>
				</th>
				<td class="qa-form-tall-label">
					<textarea class="form-control" cols="40" rows="5" name="cs_ads_after_question_content">' . base64_decode(qa_opt('cs_ads_after_question_content')) . '</textarea>
				</td>
			</tr>
			</tbody>
		</table>
	</div>';
	}
	function get_font_options($font_name = '')
    {
		$normal_fonts    = array(
			"Arial, Helvetica, sans-serif" => "Arial, Helvetica, sans-serif",
			"'Arial Black', Gadget, sans-serif" => "'Arial Black', Gadget, sans-serif",
			"'Bookman Old Style', serif" => "'Bookman Old Style', serif",
			"'Comic Sans MS', cursive" => "'Comic Sans MS', cursive",
			"Courier, monospace" => "Courier, monospace",
			"Garamond, serif" => "Garamond, serif",
			"Georgia, serif" => "Georgia, serif",
			"Impact, Charcoal, sans-serif" => "Impact, Charcoal, sans-serif",
			"'Lucida Console', Monaco, monospace" => "'Lucida Console', Monaco, monospace",
			"'Lucida Sans Unicode', 'Lucida Grande', sans-serif" => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
			"'MS Sans Serif', Geneva, sans-serif" => "'MS Sans Serif', Geneva, sans-serif",
			"'MS Serif', 'New York', sans-serif" => "'MS Serif', 'New York', sans-serif",
			"'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
			"Tahoma,Geneva, sans-serif" => "Tahoma, Geneva, sans-serif",
			"'Times New Roman', Times,serif" => "'Times New Roman', Times, serif",
			"'Trebuchet MS', Helvetica, sans-serif" => "'Trebuchet MS', Helvetica, sans-serif",
			"Verdana, Geneva, sans-serif" => "Verdana, Geneva, sans-serif"
		);
        $font_options = '<option value=""></option><optgroup label="Normal Fonts">';
        foreach ($normal_fonts as $k => $font) {
            $font_options .= '<option font-data-type="normalfont" value="' . $k . '"' . (($font_name == $k) ? ' selected' : '') . '>' . $k . '</option>';
        }
        //$font_options .= '<optgroup label="Google Fonts">';
		/* if(is_array($google_webfonts))
        foreach ($google_webfonts as $k => $font) {
            $font_options .= '<option font-data-type="googlefont" font-data-detail=\'' . json_encode($google_webfonts[$k]['variants']) . '\' value="' . $k . '"' . (($font_name == $k) ? ' selected' : '') . '>' . $k . '</option>';
        } */
        return $font_options;
    }
	
	function get_normal_font_options($font_name = '')
    {
        /* global $normal_fonts;
        $font_options = '<option value=""></option>';
        foreach ($normal_fonts as $k => $font) {
            $font_options .= '<option font-data-type="normalfont" value="' . $k . '"' . (($font_name == $k) ? ' selected' : '') . '>' . $k . '</option>';
        }
        return $font_options; */
    }
	function get_font_style_options($font_name, $style)
    {
        global $google_webfonts;
        global $normal_fonts;
        $style_options = '<option value=""></option>';
        if (($font_name == '') or (!(isset($google_webfonts[$font_name])))) {
            $style_options .= '
				<option value="400"' . (($style == "400") ? ' selected' : '') . '>Normal 400</option>
				<option value="700"' . (($style == "700") ? ' selected' : '') . '>Bold 700</option>
				<option value="400italic"' . (($style == "400italic") ? ' selected' : '') . '>Normal 400+Italic</option>
				<option value="700italic"' . (($style == "700italic") ? ' selected' : '') . '>Bold 700+Italic</option>';
        } else {
            foreach ($google_webfonts[$font_name]['variants'] as $k => $fontstyle) {
                $style_options .= '<option value="' . $fontstyle["id"] . '"' . (($style == $fontstyle["id"]) ? ' selected' : '') . '>' . $fontstyle["name"] . '</option>';
                //var_dump($style);
            }
        }
        return $style_options;
    }

	
}

