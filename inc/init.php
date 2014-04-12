<?php
	class cs_init {
		
		function init_queries($tableslc) {
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
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
