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
      writeToFile("This method is invoked here ok . So no need to worry ");
      cs_check_time_out_for_email();
      $postid = @$params['postid'];
      $event = $data[4];
      $loggeduserid = qa_get_logged_in_userid();
      if ($loggeduserid != $params['parent']['userid']) {
            $effecteduserid = $data[2];
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
            //update the last rundate 
            writeToFile("Its a time out now . Will send the emails ");
            process_emails_from_db();
      }
}

function process_emails_from_db() {
      //here extract all the email contents from database and perform the email sending operation 
      $email_queue_data = cs_get_email_queue();
      writeToFile("The email list is " . print_r($email_queue_data, true));
      $email_list = get_email_list($email_queue_data);
      writeToFile("The email list is " . print_r($email_list, true));
      foreach ($email_list as $email_data) {
            $email_body = prepare_email_body($email_queue_data, $email_data['email']);
      }
}

function prepare_email_body($email_queue_data, $email) {
      $email_body_arr = array();
      $email_body = "";

      if (is_array($email_queue_data)) {
            foreach ($email_queue_data as $queue_data) {
                  if ($queue_data['email'] === $email) {
                        $event = $queue_data['event'];
                        $body = $queue_data['body'];
                        if (!!$body) {
                              $email_body_arr[$email][$event] = (!!$email_body_arr[$event] ) ? $email_body_arr[$event] . "\n\n" : "";
                              $email_body_arr[$email][$event] .= $body;
                        }
                  }
            } //foreach
            foreach ($email_body_arr as $email_body_data) {
                writeToFile("email body data " . print_r($email_body_data , true ));
            }
            /*
            switch ($event) {
                  case 'a_post':
                        $your_question_answered = (!!$your_question_answered) ? $your_question_answered . "\n\n" : "";
                        break;
                  case 'c_post':
                        break;
                  case 'q_reshow':
                        break;
                  case 'a_reshow':
                        break;
                  case 'c_reshow':
                        break;
                  case 'a_select':
                        break;
                  case 'q_vote_up':
                        break;
                  case 'a_vote_up':
                        break;
                  case 'q_vote_down':
                        break;
                  case 'a_vote_down':
                        break;
                  case 'q_vote_nil':
                        break;
                  case 'a_vote_nil':
                        break;
                  case 'q_approve':
                        break;
                  case 'a_approve':
                        break;
                  case 'c_approve':
                        break;
                  case 'q_reject':
                        break;
                  case 'a_reject':
                        break;
                  case 'c_reject':
                        break;
                  case 'q_favorite':
                        break;
                  case 'q_post':
                        break;
                  case 'u_favorite':
                        break;
                  case 'u_message':
                        break;
                  case 'u_wall_post':
                        break;
                  case 'u_level':
                        break;
                  default:
                        break;
            }*/
      }
}

function get_email_list($email_queue_data) {
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

function shrink_email_body($email_queue_data, $max_body_length = 50) {
      if (is_array($email_queue_data)) {
            foreach ($email_queue_data as $queue_data) {
                  if (isset($queue_data['body'])) {
                        $queue_data['body'] = substr($queue_data['body'], 0, $max_body_length);
                        $queue_data['body'] .= "....";
                  }
            }
      }
      return $email_queue_data;
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
            // writeToFile("The name is " . print_r($name, true));
            $notifying_user['userid'] = $effecteduserid;
            $notifying_user['name'] = $name;
            $notifying_user['email'] = $parent['email'];

            cs_save_email_notification(null, $notifying_user, $logged_in_handle, $event, cs_get_email_body($event), array(
                '^q_handle' => isset($name_of_logged_in_user) ? $name_of_logged_in_user : isset($handle) ? $handle : qa_lang('main/anonymous'),
                '^q_title' => $params['qtitle'],
                '^q_content' => $params['text'],
                '^url' => qa_q_path($params['qid'], $params['qtitle'], true),
                    )
            );
      }
}

function cs_get_email_subject($event = "") {
      if (!!$event) {
            switch ($event) {
                  case 'a_post':
                        return qa_lang("cleanstrap/your_question_answered_sub");
                        break;
                  case 'c_post':
                        return qa_lang("cleanstrap/your_question_has_a_comment_sub");
                        break;
                  case 'q_reshow':
                        break;
                  case 'a_reshow':
                        break;
                  case 'c_reshow':
                        break;
                  case 'a_select':
                        break;
                  case 'q_vote_up':
                        break;
                  case 'a_vote_up':
                        break;
                  case 'q_vote_down':
                        break;
                  case 'a_vote_down':
                        break;
                  case 'q_vote_nil':
                        break;
                  case 'a_vote_nil':
                        break;
                  case 'q_approve':
                        break;
                  case 'a_approve':
                        break;
                  case 'c_approve':
                        break;
                  case 'q_reject':
                        break;
                  case 'a_reject':
                        break;
                  case 'c_reject':
                        break;
                  case 'q_favorite':
                        break;
                  case 'q_post':
                        break;
                  case 'u_favorite':
                        break;
                  case 'u_message':
                        break;
                  case 'u_wall_post':
                        break;
                  case 'u_level':
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
                        return qa_lang("cleanstrap/your_question_answered_body_email");
                        break;
                  case 'c_post':
                        return qa_lang("cleanstrap/your_question_has_a_comment_body");
                        break;
                  case 'q_reshow':
                        break;
                  case 'a_reshow':
                        break;
                  case 'c_reshow':
                        break;
                  case 'a_select':
                        break;
                  case 'q_vote_up':
                        break;
                  case 'a_vote_up':
                        break;
                  case 'q_vote_down':
                        break;
                  case 'a_vote_down':
                        break;
                  case 'q_vote_nil':
                        break;
                  case 'a_vote_nil':
                        break;
                  case 'q_approve':
                        break;
                  case 'a_approve':
                        break;
                  case 'c_approve':
                        break;
                  case 'q_reject':
                        break;
                  case 'a_reject':
                        break;
                  case 'c_reject':
                        break;
                  case 'q_favorite':
                        break;
                  case 'q_post':
                        break;
                  case 'u_favorite':
                        break;
                  case 'u_message':
                        break;
                  case 'u_wall_post':
                        break;
                  case 'u_level':
                        break;
                  default:
                        break;
            }
      }
}

function cs_save_email_notification($bcclist, $notifying_user, $handle, $event, $body, $subs) {
      require_once QA_INCLUDE_DIR . 'qa-db-selects.php';
      require_once QA_INCLUDE_DIR . 'qa-util-string.php';

      $subs['^site_title'] = qa_opt('site_title');
      $subs['^handle'] = $handle;
      $subs['^open'] = "\n";
      $subs['^close'] = "\n";
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
          'body' => (empty($handle) ? '' : qa_lang_sub('emails/to_handle_prefix', $handle)) . strtr($body, $subs),
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
            foreach ($params['mail_list'] as $email) {
                  $mailer->AddAddress($email['toemail'], $email['toname']);
                  // writeToFile("Sending email to - " . $email['toemail'] . '-' . $email['toname']);
            }
      }
      $mailer->Subject = $params['subject'];
      $mailer->Body = $params['body'];
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
