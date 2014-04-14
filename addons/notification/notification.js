$(document).ready(function(){
	$('#activitylist').on('click', function(e){
		e.preventDefault();

		$.ajax({
			type:'GET',
			url : ajax_url,
			data: {
				action: 'activitylist',
			},
			dataType: 'html',
			context: this,
			success: function (response) {
				$('.activity-dropdown-list .append').html(response);
			},
		});
	});
	
	$('#messagelist').on('click', function(e){
		e.preventDefault();

		$.ajax({
			type:'GET',
			url : ajax_url,
			data: {
				action: 'messagelist',
			},
			dataType: 'html',
			context: this,
			success: function (response) {
				$('#message-dropdown-list').html(response);
			},
		});
	});
});