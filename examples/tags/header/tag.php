<?php

	function ct_header($tag)
	{
		return nl2br('<a class="backtotop" href="./index.php">&larr; Back to index</a>
		
<strong>Custom Tags &copy; Oliver Lillie '.date('Y').'
This text is an example CustomTag &lt;ct:header /&gt;
This examples name is "'.$tag['attributes']['example_name'].'" and is set in an attribute of the custom tag.</strong>

');
	}