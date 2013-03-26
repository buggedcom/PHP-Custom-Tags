<?php

	function ct_entities($tag)
	{
		return htmlentities($tag['content'], ENT_QUOTES);
	}