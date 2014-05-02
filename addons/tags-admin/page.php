<?php
/* don't allow this page to be requested directly from browser */	
if (!defined('QA_VERSION')) {
		header('Location: /');
		exit;
}


class cs_tags_admin_page {
	var $directory;
	var $urltoroot;

	function load_module($directory, $urltoroot) {
		$this->directory=$directory;
		$this->urltoroot=$urltoroot;
	}

	function match_request($request)
	{
		if ($request=='admin/tags-admin')
			return true;

		return false;
	}
	function process_request($request)
	{
		//	Get popular tags
		
		$start=qa_get_start();
		$userid=qa_get_logged_in_userid();
		$populartags=qa_db_read_all_assoc(qa_db_query_sub("SELECT ^words.word, (SELECT CONCAT('{', GROUP_CONCAT( CONCAT( '\"', ^tagmetas.title, '\" : \"', ^tagmetas.content, '\"')), '}') FROM ^tagmetas WHERE ^words.word = ^tagmetas.tag) as content FROM ^words WHERE ^words.tagcount>0 LIMIT # , #", $start, qa_opt_if_loaded('page_size_tags')));


		$tagcount=qa_opt('cache_tagcount');
		$pagesize=qa_opt('page_size_tags');
		
		
		//	Prepare content for theme

		$qa_content=qa_content_prepare();
		
		$qa_content['tags']=array(
			'items' => array(),
			'rows' => ceil($pagesize/qa_opt('columns_tags')),
			'type' => 'tags'
		);

		if (count($populartags)) {
		
			$output=0;
			$qa_content['tags'] = $populartags;

		} else
			$qa_content['title']=qa_lang_html('main/no_tags_found');
		
		$qa_content['page_links']=qa_html_page_links(qa_request(), $start, $pagesize, $tagcount, qa_opt('pages_prev_next'));
		
		if (empty($qa_content['page_links']))
			$qa_content['suggest_next']=qa_html_suggest_ask();
		
		
		$qa_content['navigation']['sub']=qa_admin_sub_navigation();		
		$qa_content['site_title']=qa_lang_html('cleanstrap/edit_tags_page');
		$qa_content['title']=qa_lang_html('cleanstrap/edit_tags_page');
		
		$qa_content['custom']= $this->page_content($qa_content);
		
		return $qa_content;	
	}
	
	function page_content($qa_content){
		ob_start();
		?>
			<div id="ra-tags-admin-page">				
				<?php $this->tags_list($qa_content); ?>
			</div>
		<?php
		$output = ob_get_clean();
		
		return $output;
	}
	function tags_list($qa_content){	
		
		?>
			<ul class="tags-edit-list" data-code="<?php echo qa_get_form_security_code('edit-tag'); ?>">
				<?php 
					
					foreach($qa_content['tags'] as $tag) {
						$output = '';
						$meta = json_decode($tag['content']);
						$output .= '<li><a href="#" data-tag="'.$tag['word'].'" class="edit-tag-item">'.(isset($meta->icon) ? '<img src="'.$meta->icon.'" />' : '').$tag['word'].'</a>';	
						
						if(isset($meta->description))
							$output .= '<p>'.$meta->description.'</p>';
						
						$output .= '</li>';
						$output .= cs_event_hook('tag_admin_tag_item', $tag);
						
						echo $output;						
					} 
				 ?>
			</ul>
			
			<!-- Modal -->
			<div class="modal fade" id="tags-edit-modal" tabindex="-1" role="dialog" aria-labelledby="tag-modal-label" aria-hidden="true">
			  <div class="modal-dialog">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="tag-modal-label">Edit tag <span></span></h4>
				  </div>
				  <div class="modal-body">
					<form >
						<label>Tag Title</label>
						<input name="title" value="" class="form-control" placeholder="Tag Title" disabled>
						
						<label>Tag Description</label>
						<textarea name="description" class="form-control" placeholder="Tags description" rows="4"></textarea>
					</form>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button id="save-tags" type="button" class="btn btn-primary">Save changes</button>
				  </div>
				</div>
			  </div>
			</div>
		<?php
	}
	
	
}

