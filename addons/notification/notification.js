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
				if(response)
					$('.activity-dropdown-list .append').html(response);
				else{
					$('.activity-dropdown-list .loading').hide();
					$('.activity-dropdown-list .no-activity').show();
				}
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
				if(response)
					$('.message-dropdown-list .append').html(response);
				else{
					$('.message-dropdown-list .loading').hide();
					$('.message-dropdown-list .no-activity').show();
				}
			},
		});
	});
	
	$('.mark-activity').click(function(){
		$.ajax({
			type:'GET',
			url : ajax_url,
			data: {
				action: 'mark_all_activity'
			},
			success: function (response) {				
				$('.activity-dropdown-list .append .event-content').addClass('read');
				$('#activitylist > span').remove();
			},
		});
	});	
	$('.mark-messages').click(function(){
		$.ajax({
			type:'GET',
			url : ajax_url,
			data: {
				action: 'mark_all_messages'
			},
			success: function (response) {				
				$('#message-dropdown-list .append .event-content').addClass('read');
				$('#messagelist > span').remove();
			},
		});
	});
	
	cs_user_activity_count();
	
	window.setInterval(function(){
	  cs_user_activity_count();
	}, 50000);

});

function cs_user_activity_count(){
	$.ajax({
		type:'GET',
		url : ajax_url,
		data: {
			action: 'activity_count'
		},
		success: function (response) {
			if(response > 0)
				$('#activitylist').html('<span>'+response+'</span>');				
		},
	});
	$.ajax({
		type:'GET',
		url : ajax_url,
		data: {
			action: 'messages_count'
		},
		success: function (response) {
			if(response > 0)
				$('#messagelist').html('<span>'+response+'</span>');				
		},
	});
}