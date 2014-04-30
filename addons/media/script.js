

$(document).ready(function(){
    $('#fileupload').fileupload({
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo(document.body);
            });
        }
    });
	
	$('#q_meta_remove_featured_image').click(function(){
		$.ajax({
			data: {
				cs_ajax: true,
				cs_ajax_html: true,
				args: $(this).data('args'),
				action: 'delete_featured_image',
			},
			context:this,
			success: function (response) {
				//$(this).closest('.question-image-container').find('.featured-image').remove();
				//$('.image-preview').hide();
				//$('#q_meta_remove_featured_image').hide();
				//location.reload();
			},
		});	
	});
});