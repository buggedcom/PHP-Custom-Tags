<?php

	function ct_syntax($tags, $source)
	{
// 		add the required scripts
		static $geshi_required;
		if(!$geshi_required)
		{
			require_once 'geshi/geshi.php';
			$geshi_required = true;
		}
// 		loop through all the tags and capture the block source and the 
// 		replacement value so they can be bulk replaced
		$replacements = array();
		foreach ($tags as $tag)
		{
			$language  = isset($tag['attributes']['lang']) ? $tag['attributes']['lang'] : 'php';
			$use_lines = isset($tag['attributes']['lines']) ? $tag['attributes']['lines'] === 'true' : false;
			$content   = isset($tag['attributes']['src']) ? file_get_contents($tag['attributes']['src']) : $tag['content'];
			$geshi = new GeSHi(trim($content), $language);
// 			$geshi->set_header_type(GESHI_HEADER_PRE);
			if($use_lines === true)
			{
				$geshi->set_line_style('background: #fcfcfc;', 'background: #f0f0f0;');
				$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
			}
			$geshi->set_tab_width(500);
			$code = $geshi->parse_code();
			$code .= '<span style="font-size:10px;"><a href="http://qbnz.com/highlighter/" title="GeSHi - Generic Syntax Highlighter :: Home" style="color:#898989;">Geshi syntax highlighter</a></span><br />';
			$replacements[$tag['tag']] = $code;
		}
		return strtr($source, $replacements);
	}
	
// 	this signals that the tag should be processed as a collection, meaning 
// 	that the argument supplied to the function is an array of tags and not 
// 	a single tag. The collected tags will be processed at the end of the parse.
	$collect = true;
	
	