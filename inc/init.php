<?php
	class cs_init {
		
		function init_queries($tableslc) {
			$tablename=qa_db_add_table_prefix('ra_userevent');
			
			if (!in_array($tablename, $tableslc)) {
				require_once QA_INCLUDE_DIR.'qa-app-users.php';
				require_once QA_INCLUDE_DIR.'qa-db-maxima.php';

				return 'CREATE TABLE ^ra_userevent ('.
					'id bigint(20) NOT NULL AUTO_INCREMENT,'.
					'datetime DATETIME NOT NULL,'.
					'userid '.qa_get_mysql_user_column_type().','.
					'postid int(10) unsigned DEFAULT NULL,'.
					'effecteduserid '.qa_get_mysql_user_column_type().' unsigned DEFAULT NULL,'.
					'event VARCHAR (20) CHARACTER SET utf8 NOT NULL,'.
					'params text NOT NULL,'.
					'`read` tinyint(1) NOT NULL DEFAULT "0",'.
					'PRIMARY KEY (id),'.
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
