<?php

    namespace CustomTags;

	ini_set('error_reporting', E_ALL);
	ini_set('track_errors', '1');
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	
	$current_dir = dirname(__FILE__).DIRECTORY_SEPARATOR;

	require_once dirname($current_dir).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'CustomTags.php';
	
	$ct = new CustomTags(array(
		'parse_on_shutdown' 	=> true,
		'tag_directory' 		=> $current_dir.'tags'.DIRECTORY_SEPARATOR,
		'sniff_for_buried_tags' => true
	));
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Custom Tags - Example 1</title>
	<meta name="author" content="Oliver Lillie">
	<!-- Date: 2008-09-04 -->
</head>
<body>
	
	<ct:header example_name="Simple Example" />
	This is text that is unaffected by the tags as it is outside the tag scope.<br />
	<br />
	<strong>ct:entities</strong><br />
	<ct:entities>
		<strong>This is the <span="color:#ff0000;">ct:entities</span> tag.</strong><br />
		<strong>This is the <em>ct:entities</em> tag.</strong><br />
	</ct:entities><br />
	<br />
	<strong>ct:upper</strong><br />
	<ct:upper type="all">
		This text is transformed by the custom tag.<br />
		Using the default example all the characters should be made into uppercase characters.<br />
		Try changing the type attribute to 'ucwords' or 'ucfirst'.<br />
		<br />
		<ct:lower>
			<strong>ct:lower</strong><br />
			THIS IS LOWERCASE TEXT TRANSFORMED BY THE ct:lower CUSTOM TAG even though it's inside the ct:upper tag.<br />
			<BR />
		</ct:lower>
	</ct:upper>
	
	<ct:analytics account="google-analytics-account-id" template="google" />
	
</body>
</html>