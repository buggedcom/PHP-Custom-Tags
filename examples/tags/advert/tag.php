<?php

	/**
	 * Advert Block.
	 *
	 * This allows easy adding of adverts.
	 * 
	 * TEMPLATE OPTIONS -
	 * 	REQUIRED
	 * 		template	  - This is the id of the add you wish to place.
	 * 	OPTIONAL
	 * 		fallback	  - If the first template fails an you want to supply a fallback advert. If supplied it will be checked before usage
	 * 		fail_silently - If set to true and and an advert template is missing then the advert block will fail silently. It defaults to true
	 */
	
	$template = isset($tag['attributes']['template']) ? trim($tag['attributes']['template']) : false;
	if($template === false || empty($template))
	{
		$tag_data = CustomTags::throwError($tag['name'], 'tag has not been supplied with a template');
	}
	else
	{
		$fail_silently = isset($tag['attributes']['fail_silently']) && $tag['attributes']['fail_silently'] == 'false' ? false : true;
		$use_fallback = isset($tag['attributes']['fallback']);
		$template = CustomTagsHelper::getTemplate($tag, !$use_fallback || !$fail_silently);
		if($template)
		{
			$tag_data = $template;
		}
		else if($use_fallback)
		{
			$tag['attributes']['template'] = $tag['attributes']['fallback'];
			$tag_data = CustomTagsHelper::getTemplate($tag, $fail_silently);
		}
	}
	
