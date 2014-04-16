<?php

function cs_ajax_activitylist(){
	
	$offset = (int)qa_get('offset');
	$offset = isset($offset) ? ($offset*15) : 0;
	
	// get points for each activity
	require_once QA_INCLUDE_DIR.'qa-db-points.php';
	require_once QA_INCLUDE_DIR.'qa-db-users.php';
	$optionnames=qa_db_points_option_names();
	$options=qa_get_options($optionnames);
	$multi = (int)$options['points_multiple'];
	$upvote = '';
	$downvote = '';
	if(@$options['points_per_q_voted_up']) {
		$upvote = '_up';
		$downvote = '_down';
	}
	$event_point['in_q_vote_up'] = (int)$options['points_per_q_voted'.$upvote]*$multi;
	$event_point['in_q_vote_down'] = (int)$options['points_per_q_voted'.$downvote]*$multi*(-1);
	$event_point['in_q_unvote_up'] = (int)$options['points_per_q_voted'.$upvote]*$multi*(-1);
	$event_point['in_q_unvote_down'] = (int)$options['points_per_q_voted'.$downvote]*$multi;
	$event_point['a_vote_up'] = (int)$options['points_per_a_voted'.$upvote]*$multi;
	$event_point['in_a_vote_down'] = (int)$options['points_per_a_voted'.$downvote]*$multi*(-1);
	$event_point['in_a_unvote_up'] = (int)$options['points_per_a_voted'.$upvote]*$multi*(-1);
	$event_point['in_a_unvote_down'] = (int)$options['points_per_a_voted'.$downvote]*$multi;
	$event_point['in_a_select'] = (int)$options['points_a_selected']*$multi;
	$event_point['in_a_unselect'] = (int)$options['points_a_selected']*$multi*(-1);
	$event_point['q_post'] = (int)$options['points_post_q']*$multi;
	$event_point['a_post'] = (int)$options['points_post_a']*$multi;
	$event_point['a_select'] = (int)$options['points_select_a']*$multi;
	$event_point['q_vote_up'] = (int)$options['points_vote_up_q']*$multi;
	$event_point['q_vote_down'] = (int)$options['points_vote_down_q']*$multi;
	$event_point['a_vote_up'] = (int)$options['points_vote_up_a']*$multi;
	$event_point['a_vote_down'] = (int)$options['points_vote_down_a']*$multi;
	
	// Get Events
	$userid = qa_get_logged_in_userid();
	$eventslist = qa_db_read_all_assoc(
		qa_db_query_sub( 
			'SELECT id, UNIX_TIMESTAMP(datetime) AS datetime, userid, postid, effecteduserid, event, params, `read` FROM ^ra_userevent WHERE effecteduserid=# AND event NOT IN ("u_wall_post", "u_message") ORDER BY datetime DESC LIMIT 15 OFFSET #',
			$userid, $offset 
		)
	);
	
	if(count($eventslist) > 0){
		$event = array();
		$output='';
		$i=0;
		//
		$userids = array();
		foreach ($eventslist as $event){
			$userids[$event['userid']]=$event['userid'];
			$userids[$event['effecteduserid']]=$event['effecteduserid'];
		}
		if (QA_FINAL_EXTERNAL_USERS)
			$handles=qa_get_public_from_userids($userids);
		else 
			$handles = qa_db_user_get_userid_handles($userids);
		
		// get event's: time, type, parameters
		// get post id of questions
		
		foreach ($eventslist as $event){
			$title='';
			$link='';
			$vote_status = '';
			$handle = $handles[$event['userid']];
			
			$datetime = $event['datetime'];
			$event['date'] = qa_html(qa_time_to_string(qa_opt('db_time')-$datetime));
			$event['params'] = json_decode($event['params'],true);
			$id = ' data-id="'.$event['id'].'"';
			$read = $event['read'] ? ' read' : ' unread';
			
			$url_param = array('ra_notification' => $event['id']);
			$user_link = qa_path_html('user/'.$handle, $url_param, qa_opt('site_url'));
			
			switch($event['event']){
				case 'related': // related question to an answer
					$url = qa_path_html(qa_q_request($event['postid'], $event['params']['title']), $url_param, qa_opt('site_url'),null,null);
								
					echo '<div class="event-content clearfix'.$read.''.$read.'"'.$id.'>
							<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/asked_question_related_to_your').'</span>
										<strong class="where">'.qa_lang_html('cleanstrap/answer').'</strong>
									</div>
									<div class="footer">
										<span class="event-icon icon-link"></span>
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
											
					break;
				case 'a_post': // user's question had been answered
					$anchor = qa_anchor('A', $event['postid']);
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null,$anchor);
					
					$title = cs_truncate($event['params']['qtitle'], 60);
					
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/answered_your').'</span>
										<strong class="where">'.qa_lang_html('cleanstrap/question').'</strong>
									</div>
									<div class="footer">
										<span class="event-icon icon-answer"></span>
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';

					break;
				case 'c_post': // user's question had been commented
					$anchor = qa_anchor('C', $event['postid']);
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null,$anchor);
					
					if($event['params']['parenttype'] == 'Q')
						$type =	qa_lang_html('cleanstrap/question');
					elseif($event['params']['parenttype'] == 'A')
						$type =	qa_lang_html('cleanstrap/answer');
					else
						$type =	qa_lang_html('cleanstrap/comment');
						
					if(isset($event['params']['parent_uid']) && $event['params']['parent_uid'] != $userid){
						$what =	qa_lang_html('cleanstrap/followup_comment');
						$type =	qa_lang_html('cleanstrap/comment');
					}else
						$what = qa_lang_html('cleanstrap/replied_to_your');
					
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.$what.'</span>
										<strong class="where">'.$type.'</strong>
									</div>
									<div class="footer">
										<span class="event-icon icon-replay"></span>
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';

					break;
				case 'q_reshow': 
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null,null);
					
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-eye-open" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<span>'.qa_lang_html('cleanstrap/your').'</span>
										<strong>'.qa_lang_html('cleanstrap/question').'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/is_visible').'</span>
									</div>
									<div class="footer">
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';

					break;
				case 'a_reshow': // user's question had been answered
					$anchor = qa_anchor('A', $event['postid']);
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null,$anchor);
					
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-eye-open" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<span>'.qa_lang_html('cleanstrap/your').'</span>
										<strong>'.qa_lang_html('cleanstrap/answer').'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/is_visible').'</span>
									</div>
									<div class="footer">
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';

					break;
				case 'c_reshow': // user's question had been answered
					$anchor = qa_anchor('C', $event['postid']);
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null,$anchor);
					
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-eye-open" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<span>'.qa_lang_html('cleanstrap/your').'</span>
										<strong>'.qa_lang_html('cleanstrap/comment').'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/is_visible').'</span>
									</div>
									<div class="footer">
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					
					break;
				case 'a_select':
					$anchor = qa_anchor('A', $event['postid']);
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null,$anchor);
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/selected_as_best').'</span>
									</div>
									<div class="footer">
										<span class="event-icon icon-medal"></span>
										<span class="points">'.qa_lang_sub('cleanstrap/you_have_earned_x_points', $event_point['a_post']).'</span>
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
		
					break;
				case 'q_vote_up': 
					
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null);
					
					$title = cs_truncate($event['params']['qtitle'], 60);
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/upvoted_on_your').'</span>
										<strong class="where">'.qa_lang_html('cleanstrap/question').'</strong>
									</div>
									<div class="footer">
										<span class="event-icon icon-thumbs-up2"></span>
										<span class="points">'.qa_lang_sub('cleanstrap/you_have_earned_x_points', $event_point['a_vote_up']).'</span>
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					
					break;
				case 'a_vote_up': 
					$anchor = qa_anchor('A', $event['postid']);
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null,$anchor);
				
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/upvoted_on_your').'</span>
										<strong class="where">'.qa_lang_html('cleanstrap/answer').'</strong>
									</div>
									<div class="footer">
										<span class="event-icon icon-thumbs-up2"></span>
										<span class="points">'.qa_lang_sub('cleanstrap/you_have_earned_x_points', $event_point['a_vote_up']).'</span>
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					
					break;
				case 'q_approve':
					
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null);
					
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-checkmark3" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/approved_your').'</span>
										<strong class="where">'.qa_lang_html('cleanstrap/question').'</strong>
									</div>
									<div class="footer">
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
				
					break;
				case 'a_approve':
					$anchor = qa_anchor('A', $event['postid']);
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null,$anchor);
					
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-checkmark3" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/approved_your').'</span>
										<strong class="where">'.qa_lang_html('cleanstrap/answer').'</strong>
									</div>
									<div class="footer">
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					
					break;
				case 'u_favorite': 
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
							<div class="event-right">
								<a href="'.$user_link.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/added_you_to').'</span>
										<strong class="where">'.qa_lang_html('cleanstrap/favourite').'</strong>
									</div>
									<div class="footer">
										<span class="event-icon icon-heart"></span>									
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					break;
				
				case 'q_favorite': 
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
							<div class="event-right">
								<a href="'.$user_link.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/added_your_question_to').'</span>
										<strong class="where">'.qa_lang_html('cleanstrap/favourite').'</strong>
									</div>
									<div class="footer">
										<span class="event-icon icon-heart"></span>									
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					break;
				case 'q_vote_down': 
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null);
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-thumbs-down2" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<span class="what">'.qa_lang_html('cleanstrap/you_have_received_down_vote').'</span>
										<strong class="where">'.qa_lang_html('cleanstrap/question').'</strong>
									</div>
									<div class="footer">
										<span class="points">'.qa_lang_sub('cleanstrap/you_have_lost_x_points', $event_point['q_vote_down']).'</span>
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					break;
				case 'c_approve':
					$anchor = qa_anchor('C', $event['postid']);
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null,$anchor);
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-checkmark3" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/approved_your').'</span>
										<strong class="where">'.qa_lang_html('cleanstrap/comment').'</strong>
									</div>
									<div class="footer">									
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					break;
				case 'q_reject':
		
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null);
		
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-cross" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/your_question_is_rejected').'</span>
									</div>
									<div class="footer">
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
			
					break;
				case 'a_reject':
					$anchor = qa_anchor('A', $event['postid']);
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null, $anchor);
					
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-cross" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/your_answer_is_rejected').'</span>
									</div>
									<div class="footer">									
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					break;
				case 'c_reject':
					$anchor = qa_anchor('C', $event['postid']);
					$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), $url_param, qa_opt('site_url'),null, $anchor);
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-cross" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/your_comment_is_rejected').'</span>
									</div>
									<div class="footer">									
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					break;
				case 'u_level':
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a class="icon icon-user" href="'.$url.'"></a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/your_question_is_rejected').'</span>
									</div>
									<div class="footer">
										<span class="points">'.qa_lang_sub('cleanstrap/you_have_earned_x_points', $event_point['a_vote_up']).'</span>
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';
					break;
				
			}
		}
	}else{
		echo '<div class="no-more-activity">'. qa_lang_html('cleanstrap/no_more_activity') .'</div>';
	}

	die();
}
function cs_ajax_messagelist(){
	$offset = (int)qa_get('offset');
	$offset = isset($offset) ? ($offset*15) : 0;
	
	require_once QA_INCLUDE_DIR.'qa-db-users.php';
	// Get Events
	$message_events = array(
		'u_message',
		'u_wall_post',
	);
	$events = "'".implode("','",$message_events)."'";
	$userid = qa_get_logged_in_userid();
	$eventslist = qa_db_read_all_assoc(
		qa_db_query_sub(
			'SELECT id, UNIX_TIMESTAMP(datetime) AS datetime, userid, postid, effecteduserid, event, params, `read` FROM ^ra_userevent WHERE effecteduserid=# AND event IN (' . $events .') ORDER BY id DESC LIMIT 15 OFFSET #',
			$userid, $offset
		)
	);
	if(count($eventslist) > 0){
		$event = array();

		$userids = array();
		foreach ($eventslist as $event){
			$userids[$event['userid']]=$event['userid'];
			$userids[$event['effecteduserid']]=$event['effecteduserid'];
		}
		if (QA_FINAL_EXTERNAL_USERS)
			$handles=qa_get_public_from_userids($userids);
		else 
			$handles = qa_db_user_get_userid_handles($userids);
		// get event's: time, type, parameters
		// get post id of questions
		foreach ($eventslist as $event){
			$title='';
			$link='';
			$handle = $handles[$event['userid']];
			
			$reciever_handle = $handles[$event['effecteduserid']];
			$reciever_link = qa_path('user/'.$reciever_handle);
			$datetime = $event['datetime'];
			$event['date'] = qa_html(qa_time_to_string(qa_opt('db_time')-$datetime));
			$event['params'] = json_decode($event['params'],true);
			$message = substr($event['params']['message'], 0, 30).'..';
			$id = ' data-id="'.$event['id'].'"';
			$read = $event['read'] ? ' read' : ' unread';
			$url_param = array('ra_notification' => $event['id']);
			$user_link = qa_path_html('user/'.$handle, $url_param);
			
			switch($event['event']){
				case 'u_message': // related question to an answer
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
							<div class="event-right">
								<a href="'.qa_path_html('message/'.$handle, $url_param, qa_opt('site_url')).'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/sent_you_a_private_message').'</span>
										<span class="message">'.$message.'</span>
									</div>
									<div class="footer">
										<span class="event-icon icon-mail"></span>
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';						
					break;
				case 'u_wall_post': // user's question had been answered
					$url = qa_path_html('user/'.$reciever_handle.'/wall', $url_param, qa_opt('site_url'));
					echo '<div class="event-content clearfix'.$read.'"'.$id.'>
							<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
							<div class="event-right">
								<a href="'.$url.'">
									<div class="head">
										<strong class="user">'.$handle.'</strong>
										<span class="what">'.qa_lang_html('cleanstrap/posted_on_your_wall').'</span>
										<span class="message">'.$message.'</span>
									</div>
									<div class="footer">
										<span class="event-icon icon-pin"></span>
										<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
									</div>
								</a>
							</div>
						</div>';						
					break;
			}

		}
	}else{
		echo '<div class="no-more-activity">'. qa_lang_html('cleanstrap/no_more_messages') .'</div>';
	}

	die();
} 


function cs_set_all_activity_as_read($uid){
	qa_db_query_sub(
		'UPDATE ^ra_userevent SET `read` = 1 WHERE effecteduserid=# AND event NOT IN ("u_wall_post", "u_message")',
		$uid
	);
}
function cs_set_all_messages_as_read($uid){
	qa_db_query_sub(
		'UPDATE ^ra_userevent SET `read` = 1 WHERE effecteduserid=# AND event IN ("u_wall_post", "u_message")',
		$uid
	);
}
function cs_get_total_activity($uid){
	return qa_db_read_one_value(qa_db_query_sub(
		'SELECT COUNT(*) FROM ^ra_userevent WHERE `read` = 0 AND effecteduserid=#  AND event NOT IN ("u_wall_post", "u_message")',
		$uid
	), true);
}
function cs_get_total_messages($uid){
	return qa_db_read_one_value(qa_db_query_sub(
		'SELECT COUNT(*) FROM ^ra_userevent WHERE `read` = 0 AND effecteduserid=#  AND event IN ("u_wall_post", "u_message")',
		$uid
	), true);
}

function cs_ajax_mark_all_activity(){
	if(qa_is_logged_in())
		cs_set_all_activity_as_read(qa_get_logged_in_userid());
	
	die();
}
function cs_ajax_mark_all_messages(){
	if(qa_is_logged_in())
		cs_set_all_messages_as_read(qa_get_logged_in_userid());
	
	die();
}

function cs_ajax_activity_count(){
	echo cs_get_total_activity(qa_get_logged_in_userid());
	
	die();
}

function cs_ajax_messages_count(){
	echo cs_get_total_messages(qa_get_logged_in_userid());
	
	die();
}
