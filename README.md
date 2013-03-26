#PHP CustomTags

Allows you to create HTML custom tags that aids in templating and providing easy to use extendable functionality that designers find easy to work with. Create completely customisable tags and even comes with a simple internal templating engine.

Tags can also be collected and processed in bulk, which can be usefull when optimising performance of database queries and other such junk.

##An Example Custom Tag.

```html
<ct:inline some="attribute">
    This is an in line template. <br />
    This is a #{tag} that can be accessed by the callback function
</ct:inline>`
```

##Simple Integrated Example

```php
<?php

$current_dir = dirname(__FILE__).DIRECTORY_SEPARATOR;
require_once dirname($current_dir).DIRECTORY_SEPARATOR.'customtags.php';

$ct = new CustomTags(array(
    'parse_on_shutdown'     => true,
    'tag_directory'         => $current_dir.'tags'.DIRECTORY_SEPARATOR,
    'sniff_for_buried_tags' => true
));

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>untitled</title>
    <meta name="generator" content="TextMate http://macromates.com/">
    <meta name="author" content="Oliver Lillie">
    <!-- Date: 2010-07-10 -->
</head>
<body> 

    <ct:youtube id="wfI0Z6YJhL0" />

</body>
</html>
```

**Inside the related tag file; tags/youtube/tag.php:**

```php
<?php

function ct_youtube($tag)
{
    return '<object id="'.$tag['attributes']->id.'" value="http://www.youtube.com/v/'.$tag['attributes']->id.'" /><param ...etc...>';
}

````

**Resulting output:**

```html
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"> 

<html lang="en"> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
    <title>untitled</title> 
    <meta name="generator" content="TextMate http://macromates.com/"> 
    <meta name="author" content="Oliver Lillie"> 
    <!-- Date: 2010-07-10 --> 
</head> 
<body> 

    <object id="wfI0Z6YJhL0" value="http://www.youtube.com/v/wfI0Z6YJhL0" /><param ...etc...> 

</body> 
</html>
```