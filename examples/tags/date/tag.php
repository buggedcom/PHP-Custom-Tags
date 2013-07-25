<?php

    namespace CustomTags;

	function ct_date($tags, $source)
	{
// 		loop through all the tags and capture the block source and the 
// 		replacement value so they can be bulk replaced
		$replacements = array();
		foreach ($tags as $tag)
		{
			$format = isset($tag['attributes']['format']) ? $tag['attributes']['format'] : 'd.m.Y';
			$replacements[$tag['tag']] = isset($tag['attributes']['timestamp']) ? date($format, $tag['attributes']['timestamp']) : date($format);
		}
		return strtr($source, $replacements);
	}
	
// 	this signals that the tag should be processed as a collection, meaning 
// 	that the argument supplied to the function is an array of tags and not 
// 	a single tag. The collected tags will be processed at the end of the parse.
	$collect = true;
	
	