<?php

    namespace CustomTags;

	ini_set('error_reporting', 0);
	ini_set('track_errors', '0');
	ini_set('display_errors', '0');
	ini_set('display_startup_errors', '0');
	
	$current_dir = dirname(__FILE__).DIRECTORY_SEPARATOR;
	
	require_once dirname($current_dir).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'CustomTags.php';
	
	$ct = new CustomTags(array(
		'parse_on_shutdown' => true,
		'tag_directory' 	=> $current_dir.'tags'.DIRECTORY_SEPARATOR
	));
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Custom Tags - Example 4</title>
	<meta name="author" content="Oliver Lillie">
	<!-- Date: 2008-09-21 -->
</head>
<body>
	<ct:header example_name="Incoming Search Keyword Highlighting Example" />
	<br />
	<ct:search-highlight></ct:search-highlight><br />
	<br />
</body>
</html>
