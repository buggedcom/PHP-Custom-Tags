<?php

    namespace CustomTags;

	function ct_entities($tag)
	{
		return htmlentities($tag['content'], ENT_QUOTES);
	}