<?php

	/**
	 * Analytics Block
	 *
	 * Outputs analytic scripts.
	 *
	 * TEMPLATE OPTIONS -
	 * 	REQUIRED
	 * 		template	- The template to use. If set it is relative to the blocks template folder.
	 */
	
	function ct_analytics($tag)
	{
		return CustomTagsHelper::parseTemplate($tag, $tag['attributes']);
	}
	
