<?php

	ini_set('error_reporting', E_ALL);
	ini_set('track_errors', '1');
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	
	$current_dir = dirname(__FILE__).DIRECTORY_SEPARATOR;
	
	if(!is_writable($current_dir.'cache'.DIRECTORY_SEPARATOR))
	{
		echo '<span style="color:#ff0000;">To continue, please make the cache directory - "'.$current_dir.'cache'.DIRECTORY_SEPARATOR.'" read writable by the webserver.</span>';
		exit;
	}

	require_once dirname($current_dir).DIRECTORY_SEPARATOR.'customtags.php';
	
	$ct = new CustomTags(array(
		'parse_on_shutdown' => true,
		'tag_directory' 	=> $current_dir.'tags'.DIRECTORY_SEPARATOR,
		'cache_tags' 		=> true,
		'cache_directory' 	=> $current_dir.'cache'.DIRECTORY_SEPARATOR
	));
	
?>><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Custom Tags - Example 2</title>
	<meta name="author" content="Oliver Lillie">
	<!-- Date: 2008-09-04 -->
</head>
<body>

	<ct:header example_name="Simple Caching Example" />
	This caching is primitive and if you require advanced functionality then it would be best to use your own caching class.<br />
	<br />
	<ct:date format="d.m.Y H:i:s" /> : This content will be cached!<br />
	<ct:date format="d.m.Y H:i:s" cache="false" /> : This content will not be cached even though caching is turned on!<br />
	
	<ct:analytics account="google-analytics-account-id" template="google" />
	
</body>
</html>
		