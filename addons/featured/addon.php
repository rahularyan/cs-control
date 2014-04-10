<?php

/*
	Name:Featured
	Type:page
	Class:cs_featured_page
	Version:1.0
	Author: Rahul Aryan
	Description:For showing featured questions in a creative style
*/	

if (!defined('QA_VERSION')) {
		header('Location: /');
		exit;
}


class cs_featured_page {
	var $directory;
	var $urltoroot;

	function load_module($directory, $urltoroot) {
		$this->directory=$directory;
		$this->urltoroot=$urltoroot;
	}

	function match_request($request)
	{
		if ($request=='featured')
			return true;

		return false;
	}
	function process_request($request)
	{
	
		$qa_content=qa_content_prepare();		
		$qa_content['site_title']="Featured";
		$qa_content['title']="Featured questions";
		$qa_content['error']="";
		$qa_content['suggest_next']="";
		
		$qa_content['custom']= 'Yo buddy hahahh' ;
		
		return $qa_content;	
	}
	
}

