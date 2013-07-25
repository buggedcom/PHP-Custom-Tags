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
	<title>Custom Tags - Example 3</title>
	<meta name="author" content="Oliver Lillie">
	<!-- Date: 2008-09-04 -->
</head>
<body>
	<ct:header example_name="Collected Tags Example" />
	<br />
	<strong>Date tag.php example code:</strong><br />
	<ct:syntax lang="php" lines="true" src="<?php echo dirname(__FILE__).DIRECTORY_SEPARATOR.'tags'.DIRECTORY_SEPARATOR.'date'.DIRECTORY_SEPARATOR.'tag.php'; ?>" />
	<br />
	These dates (below) are parsed as a group. In order to parse tags as a group you must set the $collect value in the tag.php file, shown below.<br />
	<br />
	<strong>Date tags start:</strong><br />
	<br />
	<ct:date timestamp="1221080664" /><br />
	<ct:date format="d.m.Y H:i:s" timestamp="1221080664" /><br />
	<ct:date format="d.m.Y H:i:s" /><br />
	<ct:date format="l" /><br />
	<ct:date format="l" timestamp="1221080664" /><br />
	<ct:date format="W" /><br />
	<ct:date format="D dS M" /><br />
	<br />
</body>
</html>
