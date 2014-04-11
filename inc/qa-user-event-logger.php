<?php
	class qa_user_event_logger {
		
		function init_queries($tableslc) {
			if (qa_opt('uel_to_database')) {
				$tablename=qa_db_add_table_prefix('userlog');
				
				if (!in_array($tablename, $tableslc)) {
					require_once QA_INCLUDE_DIR.'qa-app-users.php';
					require_once QA_INCLUDE_DIR.'qa-db-maxima.php';

					return 'CREATE TABLE ^userlog ('.
						'datetime DATETIME NOT NULL,'.
						'userid '.qa_get_mysql_user_column_type().','.
						'postid int(10) unsigned DEFAULT NULL,'.
						'effecteduserid '.qa_get_mysql_user_column_type().','.
						'event VARCHAR (20) CHARACTER SET ascii NOT NULL,'.
						'params VARCHAR (1200) NOT NULL,'.
						'KEY datetime (datetime),'.
						'KEY userid (userid),'.
						'KEY event (event)'.
					') ENGINE=MyISAM DEFAULT CHARSET=utf8';
				}
			}
		}

		
		function admin_form(&$qa_content){

		//	Process form input

			$saved=false;
			
			if (qa_clicked('uel_save_button')) {
				qa_opt('uel_to_database', (int)qa_post_text('uel_to_database_field'));
				qa_opt('uel_to_files', qa_post_text('uel_to_files_field'));
				qa_opt('uel_directory', qa_post_text('uel_directory_field'));
='favorite'
				$saved=true;
			}
			
			return array(
				'ok' => ($saved && !isset($error)) ? 'Event log settings saved' : null,
				
				'fields' => array(
					array(
						'label' => 'Log events to <code>'.QA_MYSQL_TABLE_PREFIX.'userlog</code> database table',
						'tags' => 'name="uel_to_database_field"',
						'value' => qa_opt('uel_to_database'),
						'type' => 'checkbox',
					),
				),
				
				'buttons' => array(
					array(
						'label' => 'Save Changes',
						'tags' => 'name="uel_save_button"',
					),
				),
			);
		}

		function process_event($event, $userid, $handle, $cookieid, $params)
		{
			//qa_fatal_error(var_dump($params));
			if (qa_opt('uel_to_database')) {
				$loggeduserid = qa_get_logged_in_userid();
				$dolog=true;
				$postid = @$params['postid'];
				switch($event){
					case 'a_post': // user's question had been answered
						//qa_fatal_error(var_dump($params['question']['title']));
						if ($loggeduserid != $params['parent']['userid']){
							$effecteduserid = $params['parent']['userid'];
							$question = $this->GetQuestion($params);
							$params['qtitle'] = $question['title'];
							$params['qid'] = $question['postid'];
						}else 
							$dolog=false;
						break;
					case 'c_post': // user's answer had been commented
						if ($loggeduserid != $params['parent']['userid']){
							$effecteduserid = $params['parent']['userid'];
							$question = $this->GetQuestion($params);
							$params['qtitle'] = $question['title'];
							$params['qid'] = $question['postid'];
						}else 
							$dolog=false;
						break;
					case 'q_reshow':
						require_once QA_INCLUDE_DIR.'qa-app-posts.php';
						$post = qa_post_get_full($postid);
						if ($loggeduserid != $post['userid']){
							$effecteduserid = $post['userid'];
							$question = $this->GetQuestion($params);
							$params['qtitle'] = $question['title'];
							$params['qid'] = $question['postid'];
						}else 
							$dolog=false;
						break;
					case 'a_reshow':
						require_once QA_INCLUDE_DIR.'qa-app-posts.php';
						$post = qa_post_get_full($postid);
						if ($loggeduserid != $post['userid']){
							$effecteduserid = $post['userid'];
							$question = $this->GetQuestion($params);
							$params['qtitle'] = $question['title'];
							$params['qid'] = $question['postid'];
							unset($params['oldanswer']);
							unset($params['content']);
							unset($params['text']);
						}else 
							$dolog=false;
						break;
					case 'c_reshow':
						require_once QA_INCLUDE_DIR.'qa-app-posts.php';
						$post = qa_post_get_full($postid);
						if ($loggeduserid != $post['userid']){
							unset($params['oldcomment']);
							$effecteduserid = $post['userid'];
							$question = $this->GetQuestion($params);
							$params['qtitle'] = $question['title'];
							$params['qid'] = $question['postid'];
						}else 
							$dolog=false;
						break;
					case 'a_unselect':
						require_once QA_INCLUDE_DIR.'qa-app-posts.php';
						$post = qa_post_get_full($postid);
						$effecteduserid = $post['userid'];
						qa_db_query_sub(
							"DELETE FROM ^userlog WHERE effecteduserid=$ AND event=$ AND postid=$",
							$effecteduserid, 'a_select', $postid
						);
						$dolog=false;
						break;
					case 'a_select':
						require_once QA_INCLUDE_DIR.'qa-app-posts.php';
						$post = qa_post_get_full($postid);
						if ($loggeduserid != $post['userid']){
							$effecteduserid = $post['userid'];
							$question = $this->GetQuestion($params);
							$params['qtitle'] = $question['title'];
							$params['qid'] = $question['postid'];
						}else
							$dolog=false;
						break;
					case 'q_vote_up':
						$this->UpdateVote('in_q_vote', $postid,$userid, $params, 'q_vote_up', 1);
						$dolog=false;
						break;
					case 'a_vote_up':
						$this->UpdateVote('in_a_vote', $postid,$userid, $params, 'a_vote_up', 1);
						$dolog=false;
						break;
					case 'q_vote_down':
						$this->UpdateVote('in_q_vote', $postid,$userid, $params, 'q_vote_down', -1);
						$dolog=false;
						break;
					case 'a_vote_down':
						$this->UpdateVote('in_a_vote', $postid,$userid, $params, 'a_vote_down', -1);
						$dolog=false;
						break;
					case 'q_vote_nil':
						$this->UpdateVote('in_q_vote', $postid,$userid, $params, 'q_vote_nil', 0);
						$dolog=false;
						break;
					case 'a_vote_nil':
						$this->UpdateVote('in_a_vote', $postid,$userid, $params, 'a_vote_nil', 0);
						$dolog=false;
						break;
					case 'q_approve':
					case 'a_approve':
					case 'c_approve':
					case 'q_reject':
					case 'a_reject':
					case 'c_reject':
						require_once QA_INCLUDE_DIR.'qa-app-posts.php';
						$post = qa_post_get_full($postid);
						if ($loggeduserid != $post['userid']){
							$effecteduserid = $post['userid'];
							$question = $this->GetQuestion($params);
							$params['qtitle'] = $question['title'];
							$params['qid'] = $question['postid'];
						}else 
							$dolog=false;
						break;					
					case 'q_favorite':
						$this->UpdateVote('in_q_vote', $postid,$userid, $params, 'favorite', 1);
						$dolog=false;					
						break;
					case 'q_unfavorite':
						$this->UpdateVote('in_q_vote', $postid,$userid, $params, 'unfavorite', -1);
						$dolog=false;					
						break;
					case 'q_post':
						if ($params['parent']['type']=='A') // related question
						{
							$effecteduserid = $params['parent']['userid'];
							if ($loggeduserid != $effecteduserid)
								$event = 'related';
							else
								$dolog=false;
						} else
							$dolog=false;
						break;
					case 'u_favorite':
						$this->UpdateUserFavorite($postid,$userid, $params, 'u_favorite', 1);
						$dolog=false;
						break;
					case 'u_unfavorite':
						$this->UpdateUserFavorite($postid,$userid, $params, 'u_favorite', -1);
						$dolog=false;
						break;
					case 'u_message':
						$effecteduserid = $params['userid'];
						break;
					case 'u_wall_post':
						$effecteduserid = $params['userid'];
						$params['message']=$params['content'];
						break;
					case 'u_level':
						$effecteduserid = $params['userid'];
						break;
					default:
						$dolog=false;
				}
				if ($dolog){
					$this->AddEvent($postid,$userid, $effecteduserid, $params, $event);
				}
			}
		}
		function AddEvent($postid,$userid, $effecteduserid, $params, $event){
			$paramstring = $this->ParamToString($params);
			qa_db_query_sub(
				'INSERT INTO ^userlog (datetime, userid, effecteduserid, postid, event, params) '.
				'VALUES (NOW(), $, $, $, $, $)',
				$userid, $effecteduserid, $postid, $event, $paramstring
			);
		}
		
		function UpdateUserFavorite($postid,$userid, $params, $event, $value){
			$effecteduserid = $params['userid'];
			$posts = qa_db_read_all_values(qa_db_query_sub(
				'SELECT params FROM ^userlog WHERE effecteduserid=$ AND event=$',
				$effecteduserid, 'u_favorite'
			));
			if (count($posts) == 0 ){
				if ($value==1){
					$params['favorited']=1;
					$this->AddEvent($postid,$userid, $effecteduserid, $params, $event);
				}
			}else{
				$postparams=json_decode($posts[0],true);
				$params['favorited'] = (int)$postparams['favorited'] + $value;
				if ( ($params['favorited'])>=1 ){
					$paramstring = $this->ParamToString($params);
					qa_db_query_sub(
						"UPDATE ^userlog SET datetime=NOW(), userid=$, effecteduserid=$, postid=NULL, event=$, params=$ WHERE effecteduserid=$ AND event=$",
						$userid, $effecteduserid, $event,$paramstring, $effecteduserid, $event
					);
				}else{
					qa_db_query_sub(
						"DELETE FROM ^userlog WHERE effecteduserid=$ AND event=$",
						$effecteduserid, 'u_favorite'
					);
				}
			}
		}
		
		function UpdateVote($newevent, $postid,$userid, $params, $eventname, $value)
		{
			$effecteduserid = $this->GetUseridFromPost($postid);
			$posts = qa_db_read_all_values(qa_db_query_sub(
				'SELECT params FROM ^userlog WHERE postid=$ AND event=$',
				$postid, $newevent
			));
			if (!isset($effecteduserid))
				return; // post from anonymous user
			if (count($posts) == 0 ){ // Add New Event
				if(($eventname!='q_vote_nil') && ($eventname!='a_vote_nil') && ($eventname!='unfavorite')){
					$question = $this->GetQuestion($params);
					$params['qtitle'] = $question['title'];
					$params['qid'] = $question['postid'];
					$params['newvotes']=$value;
					//qa_fatal_error(var_dump($question));
					$params[$eventname]=1;
					$this->AddEvent($postid,$userid, $effecteduserid, $params, $newevent);
				}
			}else{
				$postparams=json_decode($posts[0],true);
				
				if (($eventname=='q_vote_nil') || ($eventname=='a_vote_nil') ){
					$netvotes = $this->GetVotesFromPost($postid);
					$params['newvotes'] = $netvotes;
					$diffrence = (int)$postparams['newvotes'] - (int)$netvotes;
					//qa_fatal_error(var_dump($netvotes));
					switch($eventname){
					case 'q_vote_nil': 
						if ( $diffrence == 1 ) //upvote cancelled
							$params['q_vote_up']=(int)$postparams['q_vote_up']-1;
						elseif ( $diffrence == -1 ) //downvote cancelled
							$params['q_vote_down']=(int)$postparams['q_vote_down']-1;
						break;
					case 'a_vote_nil': 
						if ( $diffrence == 1 ) //upvote cancelled
							$params['a_vote_up']=(int)$postparams['a_vote_up']-1;
						elseif ( $diffrence == -1 ) //downvote cancelled
							$params['a_vote_down']=(int)$postparams['a_vote_down']-1;
						break;
					}
				}else{				
					if (($eventname == 'favorite') || ($eventname == 'unfavorite'))
						$params[$eventname]=(int)$postparams[$eventname]+$value;
					else{
						$params[$eventname]=(int)$postparams[$eventname]+1;
						$params['newvotes']=(int)$postparams['newvotes']+$value;
					}
				}
				foreach ($postparams as $key => $value)
					if (!isset($params[$key]))
						$params[$key] = $value;
				//qa_fatal_error(var_dump($postparams));
				$paramstring = $this->ParamToString($params);
				qa_db_query_sub(
					"UPDATE ^userlog SET datetime=NOW(), userid=$, effecteduserid=$, postid=$, event=$, params=$ WHERE postid=$ AND event=$",
					$userid, $effecteduserid, $postid, $newevent,$paramstring, $postid, $newevent
				);
			}
		}
		
		function ParamToString($params)
		{
			if (isset($params)){
				unset($params['content']);
				unset($params['question']);
				unset($params['answer']);
				unset($params['text']);
				unset($params['parent']);
				unset($params['question']['content']);
				unset($params['oldquestion']);
				//qa_fatal_error(var_dump($postid));
				$paramstring = json_encode( $params );
			}
			else
				$paramstring = '';
			return $paramstring;
		}
		function GetVotesFromPost($postid)
		{
			$netvotes = qa_db_read_one_value(
				qa_db_query_sub(
					'SELECT netvotes FROM ^posts WHERE postid=#',
					$postid
				),true
			);
			return $netvotes;
		}
		function GetQuestion($params){
			$question = array();
			//qa_fatal_error(var_dump($params));
			if (isset($params['question'])){
				$question = $params['question'];
			}elseif(isset($params['parent']['question'])){
				$question = $params['parent']['question'];
			}elseif(isset($params['parent'])){
				$question = $params['parent'];
			}else{
				$postid = @$params['postid'];
				$question = qa_db_read_all_assoc(
					qa_db_query_sub(
						"SELECT qa_posts.type,
						CASE 
							WHEN qa_posts.type='Q'
								THEN qa_posts.title 
							WHEN parent.type='Q'
								THEN parent.title 
							WHEN grandparent.type='Q'
								THEN grandparent.title 
						END AS title,
						CASE 
							WHEN qa_posts.type='Q'
								THEN qa_posts.postid 
							WHEN parent.type='Q'
								THEN parent.postid 
							WHEN grandparent.type='Q'
								THEN grandparent.postid 
						END AS postid
						FROM qa_posts LEFT JOIN qa_posts AS parent ON qa_posts.parentid=parent.postid LEFT JOIN qa_posts as grandparent ON parent.parentid=grandparent.postid
						WHERE qa_posts.postid=#",
						$postid
					)
				);
				//qa_fatal_error(var_dump($question[0]));
				return $question[0];
			}
			//qa_fatal_error(var_dump($question));
			return $question;
		}
		function GetUseridFromPost($postid)
		{
			$uid = qa_db_read_one_value(
				qa_db_query_sub(
					'SELECT userid FROM ^posts WHERE postid=#',
					$postid
				),true
			);
			return $uid;
		}
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
