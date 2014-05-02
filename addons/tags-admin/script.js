$(document).ready(function(){

	var cs_active_tag_to_edit,
		cs_tag_edit_code,
		cs_active_elm_on_tags;
	
	$('.edit-tag-item').click(function(e){
		e.preventDefault();
		
		
		cs_active_elm_on_tags = $(this);
		cs_active_tag_to_edit = $(this).data('tag');
		cs_tag_edit_code = $(this).closest('.tags-edit-list').data('code');

		$('#tag-modal-label span').text(cs_active_tag_to_edit);
		$('#tags-edit-modal input[name="title"]').val( cs_active_tag_to_edit);
		$('#tags-edit-modal textarea[name="description"]').val($(this).next('p').text());
		$('#tags-edit-modal').modal('toggle');
		
		
	
	});
	$('#save-tags').click(function(){
		cs_animate_button(this);
		$.ajax({
			type:'POST',
			url:ajax_url,
			data: {
				action: 'save_tags',
				code: cs_tag_edit_code,
				tag: cs_active_tag_to_edit,
				description: $('#tags-edit-modal textarea[name="description"]').val(),
			},
			dataType: 'html',
			context:this,
			success: function (response) {				
				cs_remove_animate_button(this);
				
				if(cs_active_elm_on_tags.next().is('p'))
					cs_active_elm_on_tags.next('p').text(response);
				else
					$('<p>'+response+'</p>').insertAfter(cs_active_elm_on_tags);
			},
		});
	});

});