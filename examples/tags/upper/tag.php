<?php

    namespace CustomTags;

	function ct_upper($tag)
	{
		switch(strtolower($tag['attributes']['type']))
		{
			case 'ucwords' :
				return ucwords($tag['content']);
				break;
			case 'ucfirst' :
				return ucfirst($tag['content']);
				break;
			case 'ucwords' :
			default :
				return strtoupper($tag['content']);
		}
	}