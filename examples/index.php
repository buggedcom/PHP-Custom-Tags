<?php

    namespace CustomTags;

// 	ini_set('error_reporting', E_ALL);
// 	ini_set('track_errors', '1');
// 	ini_set('display_errors', '1');
// 	ini_set('display_startup_errors', '1');

	$changelog = file_get_contents('../CHANGELOG');
	$most_recent_changes = trim(substr($changelog, 0, strpos($changelog, "[", 15)));
	
	$current_dir = dirname(__FILE__).DIRECTORY_SEPARATOR;

	require_once dirname($current_dir).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'CustomTags.php';
	
	$ct = new CustomTags();
	$current_version = $ct->version;	
	
?><html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>CustomTags, &copy; Oliver Lillie <?php echo date('Y'); ?></title>
	<meta name="author" content="Oliver Lillie">
	<style>
		.backtotop
		{
			font-size:11px;
		}
		.alert
		{
			color:red;
		}
	</style>
</head>
<body id="top">
	<strong>CustomTags &copy; Oliver Lillie, <?php echo date('Y'); ?></strong><br />
	<br />
	1. <a href="#about">About &amp; Current Version</a><br />      
	2. <a href="#recentchanges">Most Recent Changes</a><br />
	3. <a href="#installation">Installation</a><br />
	4. <a href="#support">Support &amp; Feedback</a><br />
	5. <a href="#examples">Examples</a><br />
	6. <a href="#license">License</a><br />
	7. <a href="#changelog">Changes</a><br />
	<br />
	<hr />
	<a id="about"></a><strong>About &amp; Current Version</strong><br />
	<br />
	You are currently using version <?php echo $current_version; ?>.<br />
	<br />
	This class aims to provide an easy way of creating re-useable custom (and often complicated portions of code) that can easily be utilized by designers in site templates so they don't have to get involved in any php code. In a sense this mimics ASP or the FBML (Facebook Markup Language).<br />
	<br />
	What is special about this class is the the Custom Tags can provide it's functionality regardless of any pre-existing template or CMS system.<br />
	<br />
	<a class="backtotop" href="#top">&uarr; Back to top</a><br />
	<br />
	<hr />
	<a id="mostrecent"></a><strong>Most Recent Changes</strong><br />
	<pre><?php echo $most_recent_changes; ?></pre>
	<a class="backtotop" href="#top">&uarr; Back to top</a><br />
	<hr />
	<a id="installation"></a><strong>Installation</strong><br />
	<br />
	PHP 5 is required to use CustomTags (Please note, a PHP 4 library will not be offered because after all PHP 4 is officially dead). Other than that no extra libraries or special configuration options are required to install this class. <br />
	<br /><i>You will however need to make your own custom tag scripts, alternatively you may be able to use some predefined custom tags if anybody has <a href="http://www.buggedcom.co.uk/discuss/viewforum.php?id=21">uploaded them</a>.</i> <br />
	<br />
	<a class="backtotop" href="#top">&uarr; Back to top</a><br />
	<br />
	<hr />
	<a id="support"></a><strong>Support &amp; Feedback</strong><br />
	<br />
	You may post support or help requests in the <a href="http://www.buggedcom.co.uk/discuss/viewforum.php?id=20">CustomTags Forum</a>. If you wish to contribute your Custom Tag tag code and templates then you can <a href="http://www.buggedcom.co.uk/discuss/viewforum.php?id=21">contribute your code here</a>.<br />
	<br />
	<a class="backtotop" href="#top">&uarr; Back to top</a><br />
	<br />
	<hr />
	<a id="examples"></a><strong>Examples</strong><br />
	<br />
	I have compiled a great number of examples to show you how to use CustomTags. You can find links to the demo files and brief explanations about each below. All the custom tags found aren't meant for any merchantability of any kind and are simple for demo purposes only.<br />
	<br />
	<?php
	
		if(!is_file('example01.php'))
		{
			
	?>
	<span class="alert">The examples are required to be downloaded. Please <a href="http://www.buggedcom.co.uk/projects/customtags/demofiles.zip" class="alert">download them here</a> and then place them in the examples directory. Once you have done so this message will disappear and you will have access to the examples menu.</span><br />
	<br />
	<?php
	
		}
		else
		{
		
	?>
	<ul>
		<li><a href="example01.php"><strong>Example 1</strong></a>, is a very brief example that shows you how to simply use custom tags. The tags used demonstrate the usage of standalone tags and tags that use the inner content.<br /></li>
		<li><a href="example02.php"><strong>Example 2</strong></a>, demonstrates the included simple tag caching functionality.<br /></li>
		<li><a href="example03.php"><strong>Example 3</strong></a>, shows how to make use of the bulk tag processors, ie the tag collections. Processing tags in this way reduces server load and uses less resources to process large scripts.<br /></li>
	</ul>
	<?php
	
		}
		
	?>
	<br />
	<a class="backtotop" href="#top">&uarr; Back to top</a><br />
	<br />
	<hr />
	<a id="license"></a><strong>License</strong><br />
	<br />
	<?php echo nl2br(file_get_contents('../LICENSE')); ?><br />
	<br />
	<a class="backtotop" href="#top">&uarr; Back to top</a><br />
	<br />
	<hr />
	<a id="changelog"></a><strong>Changes</strong><pre><?php
	
echo $changelog;

?></pre>
	<a class="backtotop" href="#top">&uarr; Back to top</a><br />
	<br />
</body>
</html>	

		