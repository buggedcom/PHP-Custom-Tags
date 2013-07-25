<?php

    namespace CustomTags;

	/**
	 * Inspired by a smarty plugin.
	 * http://www.andrefiedler.de/smartyplugins/outputfilter.highlight_search_words.txt
	 * Smarty plugin
	 * Author:   André Fielder < mail [at] andrefiedler [dot] de >
	 * -------------------------------------------------------------
	 * File:     outputfilter.highlight_search_words.php
	 * Type:     outputfilter
	 * Name:     highlight_search_words
	 * Purpose:  Highlights words which were searched true an
	 *           search engine below. A css class named searchWords 
	 *           must be available.
	 * -------------------------------------------------------------
	 *
	 * @version 1.0
	 * @copyright 2005, André Fiedler
	 */
	
	function ct_search_highlight($tags, $source)
	{
// 		the search engine and incoming var name
		$search_engines = array(
			'google' 	=> 'q',
			'yahoo' 	=> 'p',
			'lycos' 	=> 'query',
			'altavista' => 'q',
			'alltheweb' => 'q',
			'excite' 	=> 'search',
			'msn' 		=> 'q'
		);
		$url = parse_url($_SERVER['HTTP_REFERER']);
// 		loop through all the tags and capture the block source and the 
// 		replacement value so they can be bulk replaced
		$replacements = array();
		foreach ($tags as $tag)
		{
// 			loop the search engine list
			foreach($search_engines as $engine_name => $var_name) 
			{
// 				search for a match of the current search engine name against the url host
				if(preg_match('/('.$engine_name.')/i', $url['host'])) 
				{
// 					loop the words and replace the words with a highlighted query
					parse_str($url['query'], $query_vars); 
					$words = explode(' ', urldecode($query_vars[$var_name]));
					foreach($words as $k => $word) 
					{
						if(trim($word) != '') 
						{
							$pattern[$k] = "/((<[^>]*)|$word)/ie";
							$replace[$k] = '"\2"=="\1"? "\1":"<span class=\"searchWords\">\1</span>"';
						}
					}
					$tag['content'] = preg_replace($pattern, $replace, $tag['content']); 	  	
				}
			}	
			$replacements[$tag['tag']] = $tag['content'];
		}
		return strtr($source, $replacements);
	}
	
// 	this signals that the tag should be processed as a collection, meaning 
// 	that the argument supplied to the function is an array of tags and not 
// 	a single tag. The collected tags will be processed at the end of the parse.
	$collect = true;
	