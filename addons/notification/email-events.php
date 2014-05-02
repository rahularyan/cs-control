<?php

//define the event hook event handlers 

cs_event_hook('a_post', NULL, 'cs_notification_event');
cs_event_hook('c_post', NULL, 'cs_notification_event');
cs_event_hook('q_reshow', NULL, 'cs_notification_event');
cs_event_hook('a_reshow', NULL, 'cs_notification_event');
cs_event_hook('c_reshow', NULL, 'cs_notification_event');
cs_event_hook('a_select', NULL, 'cs_notification_event');
cs_event_hook('q_vote_up', NULL, 'cs_notification_event');
cs_event_hook('a_vote_up', NULL, 'cs_notification_event');
cs_event_hook('q_vote_down', NULL, 'cs_notification_event');
cs_event_hook('a_vote_down', NULL, 'cs_notification_event');
cs_event_hook('q_vote_nil', NULL, 'cs_notification_event');
cs_event_hook('a_vote_nil', NULL, 'cs_notification_event');
cs_event_hook('q_approve', NULL, 'cs_notification_event');
cs_event_hook('a_approve', NULL, 'cs_notification_event');
cs_event_hook('c_approve', NULL, 'cs_notification_event');
cs_event_hook('q_reject', NULL, 'cs_notification_event');
cs_event_hook('a_reject', NULL, 'cs_notification_event');
cs_event_hook('c_reject', NULL, 'cs_notification_event');
cs_event_hook('q_favorite', NULL, 'cs_notification_event');
cs_event_hook('q_post', NULL, 'cs_notification_event');
cs_event_hook('u_favorite', NULL, 'cs_notification_event');
cs_event_hook('u_message', NULL, 'cs_notification_event');
cs_event_hook('u_wall_post', NULL, 'cs_notification_event');
cs_event_hook('u_level', NULL, 'cs_notification_event');

function cs_notification_event($data) {
      $params = $data[3];
      // writeToFile(print_r($params, true));
      cs_check_time_out_for_email();
      $postid = $params['postid'];
      $event = $data[4];
      $loggeduserid = qa_get_logged_in_userid();
      $effecteduserid = isset($data[2]) ? $data[2] : "";
      if (!!$effecteduserid) {
            cs_notify_users_by_email($event, $postid, $loggeduserid, $effecteduserid, $params);
      }
}

function cs_check_time_out_for_email() {
      /* //get the last run date 
        $last_run_date = qa_opt('cs_notification_last_run_date');
        //get the interval
        $email_event_interval = qa_opt('cs_notification_event_interval'); */
      //hardcode the values for testing 
      $date_format = "d/m/Y H:i:s";
      $last_run_date = "01/05/2014 07:23:28";
      $email_event_interval = 10; //always should be in seconds 

      $last_run_date = new DateTime($last_run_date);
      $email_event_interval = "PT" . $email_event_interval . "S";

      //get the event occurance date --> last_rundate + interval 
      $last_run_date->add(new DateInterval($email_event_interval));

      //get the current time 
      $current_time = new DateTime("now");

      //if current time is grater than last_rundate + interval then 
      if ($current_time > $last_run_date) {
            //extract the emails and send notification 
            cs_process_emails_from_db();
            //update the last rundate 
            cs_update_last_rundate($current_time);
      }
}

function cs_process_emails_from_db() {
      require_once QA_INCLUDE_DIR . 'qa-db-selects.php';
      require_once QA_INCLUDE_DIR . 'qa-util-string.php';
      //here extract all the email contents from database and perform the email sending operation 
      $email_queue_data = cs_get_email_queue();
      // writeToFile("The email list is " . print_r($email_queue_data, true));
      $email_list = cs_get_email_list($email_queue_data);
      // writeToFile("The email list is " . print_r($email_list, true));
      $subs = array();
      $subs['^site_title'] = qa_opt('site_title');
      $greeting = qa_lang("cleanstrap/greeting");
      $thank_you_message = qa_lang("cleanstrap/thank_you_message");
      $subject = strtr(qa_lang("cleanstrap/notification_email_subject"), $subs);

      foreach ($email_list as $email_data) {
            $email = $email_data['email'];
            $name = $email_data['name'];
            $subs['^user_name'] = $name;
            $email_body = cs_prepare_email_body($email_queue_data, $email);
            $email_body = $greeting . $email_body . $thank_you_message;
            $email_body = strtr($email_body, $subs);
            cs_send_email_notification(null, $email, $name, $subject, $email_body, $subs);
      }
}

function cs_prepare_email_body($email_queue_data, $email) {
      $email_body_arr = array();
      $summerized_email_body = array();
      $email_body = "";

      if (is_array($email_queue_data)) {
            foreach ($email_queue_data as $queue_data) {
                  if ($queue_data['email'] === $email) {
                        $event = $queue_data['event'];
                        $body = $queue_data['body'];
                        if (!!$body) {
                              $email_body_arr[$event] = (isset($email_body_arr[$event]) && !empty($email_body_arr[$event]) ) ? $email_body_arr[$event] . "\n\n" : "";
                              $email_body_arr[$event] .= $body;
                        } 
                  } //outer if 
            } //foreach
            foreach ($email_body_arr as $event => $email_body_for_event) {
                  if (!isset($summerized_email_body[$event])) {
                        $summerized_email_body[$event] = cs_get_email_headers($event) ;
                  }
                  $summerized_email_body[$event] .= (!!$email_body_for_event) ? $email_body_for_event . "\n" : "" ;
            }//foreach 

            foreach ($summerized_email_body as $event => $email_body_chunk) {
                  if (!!$email_body_chunk) {
                        $email_body .= $email_body_chunk;
                  }
            }//foreach 
      } //if 
      return $email_body;
}

function cs_get_email_list($email_queue_data) {
      $email_list = array();
      $unique_email_list = array();
      if (is_array($email_queue_data)) {
            foreach ($email_queue_data as $queue_data) {
                  if (isset($queue_data['email']) && !empty($queue_data['email'])) {
                        $email = $queue_data['email'];
                        if (!in_array($email, $unique_email_list)) {
                              $unique_email_list[] = $email;
                              $data = array('email' => $email);
                              if (isset($queue_data['name']) && !empty($queue_data['name'])) {
                                    $data['name'] = $queue_data['name'];
                              }
                              $email_list[] = $data;
                        }
                  }
            }
      }
      return $email_list;
}

function cs_update_last_rundate($current_time) {
      // code to update the last rundate option 
}

function cs_get_email_queue() {

      return qa_db_read_all_assoc(qa_db_query_sub("SELECT * from ^ra_email_queue queue join ^ra_email_queue_receiver rcv on queue.id = rcv.queue_id WHERE queue.status = 0 "));
}

function cs_get_name_from_userid($userid) {

      return qa_db_read_one_value(qa_db_query_sub("SELECT ^userprofile.content AS name from ^users JOIN ^userprofile ON ^users.userid=^userprofile.userid WHERE   ^userprofile.title = 'name' AND ^users.userid =# ", $userid), true);
}

function cs_notify_users_by_email($event, $postid, $userid, $effecteduserid, $params) {
      if (!!$effecteduserid) {
            $parent = $params['parent'];
            $name = cs_get_name_from_userid($effecteduserid);
            $name = (!!$name) ? $name : $parent['handle'];
            //get the working user data  
            $logged_in_handle = qa_get_logged_in_handle();
            $logged_in_user_name = cs_get_name_from_userid(qa_get_logged_in_userid());
            $logged_in_user_name = (!!$logged_in_user_name) ? $logged_in_user_name : $logged_in_handle;
            // writeToFile("The name is " . print_r($name, true));
            $notifying_user['userid'] = $effecteduserid;
            $notifying_user['name'] = $name;
            $notifying_user['email'] = $parent['email'];
            //consider only first 50 characters for saving notification 

            $content = (isset($params['text']) && !empty($params['text'])) ? $params['text'] : "";
            if (!!$content && (strlen($content) > 50)) $content = cs_shrink_email_body($params['text'], 50);
            $title = (isset($params['qtitle']) && !empty($params['qtitle'])) ? $params['qtitle'] : "";

            cs_save_email_notification(null, $notifying_user, $logged_in_handle, $event, array(
                '^q_handle' => isset($logged_in_user_name) ? $logged_in_user_name : isset($logged_in_handle) ? $logged_in_handle : qa_lang('main/anonymous'),
                '^q_title' => $title,
                '^q_content' => $content,
                '^url' => qa_q_path($params['qid'], $params['qtitle'], true),
                '^done_by' => isset($logged_in_user_name) ? $logged_in_user_name : isset($logged_in_handle) ? $logged_in_handle : qa_lang('main/anonymous'),
                    )
            );
      }
}

function cs_get_email_headers($event = "") {
      if (!!$event) {
            switch ($event) {
                  case 'a_post':
                        return qa_lang("cleanstrap/a_post_email_header");
                        break;
                  case 'c_post':
                        return qa_lang("cleanstrap/c_post_email_header");
                        break;
                  case 'q_reshow':
                        return qa_lang("cleanstrap/q_reshow_email_header");
                        break;
                  case 'a_reshow':
                        return qa_lang("cleanstrap/a_reshow_email_header");
                        break;
                  case 'c_reshow':
                        return qa_lang("cleanstrap/c_reshow_email_header");
                        break;
                  case 'a_select':
                        return qa_lang("cleanstrap/a_select_email_header");
                        break;
                  case 'q_vote_up':
                        return qa_lang("cleanstrap/q_vote_up_email_header");
                        break;
                  case 'a_vote_up':
                        return qa_lang("cleanstrap/a_vote_up_email_header");
                        break;
                  case 'q_vote_down':
                        return qa_lang("cleanstrap/q_vote_down_email_header");
                        break;
                  case 'a_vote_down':
                        return qa_lang("cleanstrap/a_vote_down_email_header");
                        break;
                  case 'q_vote_nil':
                        return qa_lang("cleanstrap/q_vote_nil_email_header");
                        break;
                  case 'a_vote_nil':
                        return qa_lang("cleanstrap/a_vote_nil_email_header");
                        break;
                  case 'q_approve':
                        return qa_lang("cleanstrap/q_approve_email_header");
                        break;
                  case 'a_approve':
                        return qa_lang("cleanstrap/a_approve_email_header");
                        break;
                  case 'c_approve':
                        return qa_lang("cleanstrap/c_approve_email_header");
                        break;
                  case 'q_reject':
                        return qa_lang("cleanstrap/q_reject_email_header");
                        break;
                  case 'a_reject':
                        return qa_lang("cleanstrap/a_reject_email_header");
                        break;
                  case 'c_reject':
                        return qa_lang("cleanstrap/c_reject_email_header");
                        break;
                  case 'q_favorite':
                        return qa_lang("cleanstrap/q_favorite_email_header");
                        break;
                  case 'q_post':
                        return qa_lang("cleanstrap/q_post_email_header");
                        break;
                  case 'u_favorite':
                        return qa_lang("cleanstrap/u_favorite_email_header");
                        break;
                  case 'u_message':
                        return qa_lang("cleanstrap/u_message_email_header");
                        break;
                  case 'u_wall_post':
                        return qa_lang("cleanstrap/u_wall_post_email_header");
                        break;
                  case 'u_level':
                        return qa_lang("cleanstrap/u_level_email_header");
                        break;
                  default:
                        break;
            }
      }
}

function cs_get_email_body($event = "") {
      if (!!$event) {
            switch ($event) {
                  case 'a_post':
                        return qa_lang("cleanstrap/a_post_body_email");
                        break;
                  case 'c_post':
                        return qa_lang("cleanstrap/c_post_body_email");
                        break;
                  case 'q_reshow':
                        return qa_lang("cleanstrap/q_reshown_body_email");
                        break;
                  case 'a_reshow':
                        return qa_lang("cleanstrap/a_reshown_body_email");
                        break;
                  case 'c_reshow':
                        return qa_lang("cleanstrap/c_reshown_body_email");
                        break;
                  case 'a_select':
                        return qa_lang("cleanstrap/a_selected_body_email");
                        break;
                  case 'q_vote_up':
                        return qa_lang("cleanstrap/q_vote_up_body_email");
                        break;
                  case 'a_vote_up':
                        return qa_lang("cleanstrap/a_vote_up_body_email");
                        break;
                  case 'q_vote_down':
                        return qa_lang("cleanstrap/q_vote_down_body_email");
                        break;
                  case 'a_vote_down':
                        return qa_lang("cleanstrap/a_vote_down_body_email");
                        break;
                  case 'q_vote_nil':
                        return qa_lang("cleanstrap/q_vote_nil_body_email");
                        break;
                  case 'a_vote_nil':
                        return qa_lang("cleanstrap/a_vote_nil_body_email");
                        break;
                  case 'q_approve':
                        return qa_lang("cleanstrap/q_approve_body_email");
                        break;
                  case 'a_approve':
                        return qa_lang("cleanstrap/a_approve_body_email");
                        break;
                  case 'c_approve':
                        return qa_lang("cleanstrap/c_approve_body_email");
                        break;
                  case 'q_reject':
                        return qa_lang("cleanstrap/q_reject_body_email");
                        break;
                  case 'a_reject':
                        return qa_lang("cleanstrap/a_reject_body_email");
                        break;
                  case 'c_reject':
                        return qa_lang("cleanstrap/c_reject_body_email");
                        break;
                  case 'q_favorite':
                        return qa_lang("cleanstrap/q_favorite_body_email");
                        break;
                  case 'q_post':
                        return qa_lang("cleanstrap/q_post_body_email");
                        break;
                  case 'u_favorite':
                        return qa_lang("cleanstrap/u_favorite_body_email");
                        break;
                  case 'u_message':
                        return qa_lang("cleanstrap/u_message_body_email");
                        break;
                  case 'u_wall_post':
                        return qa_lang("cleanstrap/u_wall_post_body_email");
                        break;
                  case 'u_level':
                        return qa_lang("cleanstrap/u_level_body_email");
                        break;
                  default:
                        break;
            }
      }
}

function cs_shrink_email_body($email_body, $max_body_length = 50) {
      if (!!$email_body) {
            $email_body = substr($email_body, 0, $max_body_length);
            $email_body .= "....";
      }
      return $email_body;
}

function cs_save_email_notification($bcclist, $notifying_user, $handle, $event, $subs) {
      require_once QA_INCLUDE_DIR . 'qa-db-selects.php';
      require_once QA_INCLUDE_DIR . 'qa-util-string.php';

      $subs['^site_title'] = qa_opt('site_title');
      $subs['^handle'] = $handle;
      $subs['^open'] = "\n";
      $subs['^close'] = "\n";
      $body = cs_get_email_body($event);
      $id = cs_dump_email_content_to_db(array(
          'event' => $event,
          'body' => strtr($body, $subs),
          'by' => $handle,
      ));
      cs_dump_email_to_db($notifying_user, $id);
}

function cs_dump_email_content_to_db($param) {
      qa_db_query_sub(
              'INSERT INTO ^ra_email_queue (event, body , created_by ) ' .
              'VALUES ($, $ , $ )', $param['event'], $param['body'], $param['by']
      );

      return qa_db_last_insert_id();
}

function cs_dump_email_to_db($notifying_user, $queue_id) {
      qa_db_query_sub(
              'INSERT INTO ^ra_email_queue_receiver (userid, email , name , queue_id ) ' .
              'VALUES (#, $ , $ , # )', $notifying_user['userid'], $notifying_user['email'], $notifying_user['name'], $queue_id
      );

      return qa_db_last_insert_id();
}

function cs_send_email_notification($bcclist, $email, $handle, $subject, $body, $subs) {

      global $qa_notifications_suspended;

      if ($qa_notifications_suspended > 0) return false;

      require_once QA_INCLUDE_DIR . 'qa-db-selects.php';
      require_once QA_INCLUDE_DIR . 'qa-util-string.php';

      $subs['^site_title'] = qa_opt('site_title');
      $subs['^handle'] = $handle;
      $subs['^open'] = "\n";
      $subs['^close'] = "\n";
      return cs_send_email(array(
          'fromemail' => qa_opt('from_email'),
          'fromname' => qa_opt('site_title'),
          'mail_list' => $email,
          'toname' => $handle,
          'bcclist' => $bcclist,
          'subject' => strtr($subject, $subs),
          'body' => strtr($body, $subs),
          'html' => false,
      ));
}

function cs_send_email($params) {
      require_once QA_INCLUDE_DIR . 'qa-class.phpmailer.php';
      $mailer = new PHPMailer();
      $mailer->CharSet = 'utf-8';
      $mailer->From = $params['fromemail'];
      $mailer->Sender = $params['fromemail'];
      $mailer->FromName = $params['fromname'];
      if (isset($params['mail_list'])) {
            if (is_array($params['mail_list'])) {
                  foreach ($params['mail_list'] as $email) {
                        $mailer->AddAddress($email['toemail'], $email['toname']);
                        writeToFile("Sending email to - " . $email['toemail'] . '-' . $email['toname']);
                  }
            } else {
                  $mailer->AddAddress($params['mail_list'], $params['toname']);
                  writeToFile("Sending email to - " . $params['mail_list'] . '-' . $params['toname']);
            }
      }
      $mailer->Subject = $params['subject'];
      $mailer->Body = $params['body'];
      writeToFile("Subject is " . $params['subject']);
      writeToFile("Body is " . $params['body']);
      if (isset($params['bcclist'])) {
            foreach ($params['bcclist'] as $email) {
                  $mailer->AddBCC($email);
                  // writeToFile($email);
            }
      }

      if ($params['html']) $mailer->IsHTML(true);

      if (qa_opt('smtp_active')) {
            // writeToFile("smtp is active and sending mail ");
            $mailer->IsSMTP();
            $mailer->Host = qa_opt('smtp_address');
            $mailer->Port = qa_opt('smtp_port');

            if (qa_opt('smtp_secure')) $mailer->SMTPSecure = qa_opt('smtp_secure');

            if (qa_opt('smtp_authenticate')) {
                  $mailer->SMTPAuth = true;
                  $mailer->Username = qa_opt('smtp_username');
                  $mailer->Password = qa_opt('smtp_password');
            }
      } else {
            //smtp is not active 
      }
      return $mailer->Send();
}

function writeToFile($string) {
      if (qa_opt('event_logger_to_files')) {
            //   Open, lock, write, unlock, close (to prevent interference between multiple writes)
            $directory = qa_opt('event_logger_directory');

            if (substr($directory, -1) != '/') $directory.='/';

            $log_file_name = $directory . 'q2a-log-' . date('Y\-m\-d') . '.txt';

            $log_file_exists = file_exists($log_file_name);

            $log_file = @fopen($log_file_name, 'a');
            if (is_resource($log_file) && (!!$log_file_exists)) {
                  if (flock($log_file, LOCK_EX)) {
                        fwrite($log_file, $string . PHP_EOL);
                        flock($log_file, LOCK_UN);
                  }
            }
            @fclose($log_file);
      }
}
