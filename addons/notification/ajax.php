<?php

function cs_ajax_activitylist(){
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
			'SELECT UNIX_TIMESTAMP(datetime) AS datetime, userid, postid, effecteduserid, event, params FROM ^userlog WHERE effecteduserid=# ORDER BY datetime DESC',
			$userid 
		)
	);
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
		$user_link = qa_path('user/'.$handle);
		$datetime = $event['datetime'];
		$event['date'] = qa_html(qa_time_to_string(qa_opt('db_time')-$datetime));
		$event['params'] = json_decode($event['params'],true);

		switch($event['event']){
			case 'related': // related question to an answer
				$url = qa_path_html(qa_q_request($event['postid'], $event['params']['title']), null, qa_opt('site_url'),null,null);
							
				echo '<div class="event-content clearfix">
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
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null,$anchor);
				
				$title = cs_truncate($event['params']['qtitle'], 60);
				
				echo '<div class="event-content clearfix">
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
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null,$anchor);
				
				$title = cs_truncate($event['params']['qtitle'], 60);
				
				if($event['params']['parenttype'] == 'Q')
					$type =	qa_lang_html('cleanstrap/question');
				elseif($event['params']['parenttype'] == 'A')
					$type =	qa_lang_html('cleanstrap/answer');
				else
					$type =	qa_lang_html('cleanstrap/comment');
				
				echo '<div class="event-content clearfix">
						<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
						<div class="event-right">
							<a href="'.$url.'">
								<div class="head">
									<strong class="user">'.$handle.'</strong>
									<span class="what">'.qa_lang_html('cleanstrap/replied_to_your').'</span>
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
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null,null);
				
				echo '<div class="event-content clearfix">
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
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null,$anchor);
				
				echo '<div class="event-content clearfix">
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
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null,$anchor);
				
				echo '<div class="event-content clearfix">
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
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null,$anchor);
				echo '<div class="event-content clearfix">
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
				
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null);
				
				$title = cs_truncate($event['params']['qtitle'], 60);
				echo '<div class="event-content clearfix">
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
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null,$anchor);
			
				echo '<div class="event-content clearfix">
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
				
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null);
				
				echo '<div class="event-content clearfix">
						<div class="avatar"><a class="icon icon-checkmark3" href="'.$url.'"></a></div>
						<div class="event-right">
							<a href="'.$url.'">
								<div class="head">
									<strong class="user">'.$handle.'</strong>
									<span class="what">'.qa_lang_html('cleanstrap/approved_your').'</span>
									<strong class="where">'.qa_lang_html('cleanstrap/question').'</strong>
								</div>
								<div class="footer">
									<span class="points">'.qa_lang_sub('cleanstrap/you_have_earned_x_points', $event_point['a_vote_up']).'</span>
									<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
								</div>
							</a>
						</div>
					</div>';
			
				break;
			case 'a_approve':
				$anchor = qa_anchor('A', $event['postid']);
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null,$anchor);
				
				echo '<div class="event-content clearfix">
						<div class="avatar"><a class="icon icon-checkmark3" href="'.$url.'"></a></div>
						<div class="event-right">
							<a href="'.$url.'">
								<div class="head">
									<strong class="user">'.$handle.'</strong>
									<span class="what">'.qa_lang_html('cleanstrap/approved_your').'</span>
									<strong class="where">'.qa_lang_html('cleanstrap/answer').'</strong>
								</div>
								<div class="footer">
									<span class="points">'.qa_lang_sub('cleanstrap/you_have_earned_x_points', $event_point['a_vote_up']).'</span>
									<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
								</div>
							</a>
						</div>
					</div>';
				
				break;
			case 'u_favorite': 
				echo '<div class="event-content clearfix">
						<div class="avatar"><a href="'.$user_link.'">'.cs_get_avatar($handle, 32, true).'</a></div>
						<div class="event-right">
							<a href="'.$url.'">
								<div class="head">
									<strong class="user">'.$handle.'</strong>
									<span class="what">'.qa_lang_html('cleanstrap/added_you_to').'</span>
									<strong class="where">'.qa_lang_html('cleanstrap/favourite').'</strong>
								</div>
								<div class="footer">
									<span class="event-icon icon-heart"></span>
									<span class="points">'.qa_lang_sub('cleanstrap/you_have_earned_x_points', $event_point['a_vote_up']).'</span>
									<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
								</div>
							</a>
						</div>
					</div>';
				break;
			case 'c_approve':
				$anchor = qa_anchor('C', $event['postid']);
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null,$anchor);
				echo '<div class="event-content clearfix">
						<div class="avatar"><a class="icon icon-checkmark3" href="'.$url.'"></a></div>
						<div class="event-right">
							<a href="'.$url.'">
								<div class="head">
									<strong class="user">'.$handle.'</strong>
									<span class="what">'.qa_lang_html('cleanstrap/approved_your').'</span>
									<strong class="where">'.qa_lang_html('cleanstrap/comment').'</strong>
								</div>
								<div class="footer">
									<span class="points">'.qa_lang_sub('cleanstrap/you_have_earned_x_points', $event_point['a_vote_up']).'</span>
									<span class="date">'.qa_lang_sub('cleanstrap/x_ago', $event['date']).'</span>
								</div>
							</a>
						</div>
					</div>';
				break;
			case 'q_reject':
	
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null);
				
				$title = cs_truncate($event['params']['qtitle'], 60);
				
				cs_notification_event_item($event, $handle, qa_lang_html('cleanstrap/your_question_is_rejected') , $url, $title);
		
				break;
			case 'a_reject':
				$anchor = qa_anchor('A', $event['postid']);
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null, $anchor);
				
				$title = cs_truncate($event['params']['qtitle'], 60);
				
				cs_notification_event_item($event, $handle, qa_lang_html('cleanstrap/your_answer_is_rejected') , $url, $title);
				break;
			case 'c_reject':
				$anchor = qa_anchor('C', $event['postid']);
				$url = qa_path_html(qa_q_request($event['params']['qid'], $event['params']['qtitle']), null, qa_opt('site_url'),null, $anchor);
				
				$title = cs_truncate($event['params']['qtitle'], 60);
				
				cs_notification_event_item($event, $handle, qa_lang_html('cleanstrap/your_comment_is_rejected') , $url, $title);
				break;
			case 'u_level':
				echo '<div class="event-content">
						<div class="avatar">' . cs_get_avatar($handle, 30, true) . '</div>
						<div class="event-right">
									<p class="what">You level had been changed from' . qa_html(qa_user_level_string($event['params']['oldlevel'])) . ' to ' . qa_html(qa_user_level_string($event['params']['level'])) . '</p>
								<span class="date"> ' . $event['date'] . '</span>
						</div>
					</div>';
				break;
			
		}
	}

	die();
}
function cs_ajax_messagelist(){
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
			'SELECT UNIX_TIMESTAMP(datetime) AS datetime, userid, postid, effecteduserid, event, params FROM ^userlog WHERE effecteduserid=# AND 
			DATE_SUB(CURDATE(),INTERVAL # DAY) <= datetime AND
			event IN (' . $events .')
			ORDER BY datetime DESC'.(qa_opt('qat_activity_number')?' LIMIT '.(int)qa_opt('qat_activity_number'):''),
			$userid, qa_opt('qat_activity_age')
		)
	);
	$event = array();
	$output='';
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
		$handle = $handles[$event['userid']];
		$user_link = qa_path('user/'.$handle);
		$reciever_handle = $handles[$event['effecteduserid']];
		$reciever_link = qa_path('user/'.$reciever_handle);
		$datetime = $event['datetime'];
		$event['date'] = qa_html(qa_time_to_string(qa_opt('db_time')-$datetime));
		$event['params'] = json_decode($event['params'],true);
		$message = substr($event['params']['message'], 0, 20);
		$output .='<li>';
		switch($event['event']){
			case 'u_message': // related question to an answer
				$url = qa_path_html(qa_path('message/' . $handle ));
				$output .='<div class="event-icon pull-left icon-chat"></div>
							<div class="event-content">
								<p class="title"><strong class="avatar"><a href="' . $user_link . '">' . $handle . '</a></strong>
								<span class="what">left you a message</span>
								</p>
								<a class="title" href="' . $url . '">'. $message . '</a>
								<span class="date"> ' . $event['date'] . '</span>
							</div>';						
				break;
			case 'u_wall_post': // user's question had been answered
				$url = qa_path_html(qa_path('user/' . $reciever_handle . '/wall' ));
				$output .='<div class="event-icon pull-left icon-chat"></div>
							<div class="event-content">
								<p class="title"><strong class="avatar"><a href="' . $user_link . '">' . $handle . '</a></strong>
								<span class="what">posted a message on your wall</span>
								</p>
								<a class="title" href="' . $url . '">'. $message . '</a>
								<span class="date"> ' . $event['date'] . '</span>
							</div>';						
				break;
		}
		$output .='</li>';
	}
	echo $output;
	die();
} 


function cs_notification_event_item($event, $handle, $what, $url, $title){
	$user_link = qa_path('user/'.$handle);
	?>
		<div class="event-content">
			<div class="avatar"><a href="<?php echo $user_link; ?>"><?php echo cs_get_avatar($handle, 30, true); ?></a></div>
			<div class="event-right">
				<a class="user" href="<?php echo $user_link; ?>"><?php echo $handle; ?></a>
				<a class="what" href="<?php echo $url; ?>"><?php echo $what; ?></a>
				<span class="date"><?php echo $event['date']; ?></span>
			</div>
		</div>
	<?php
}