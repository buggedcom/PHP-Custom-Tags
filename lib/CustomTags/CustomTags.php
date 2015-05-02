<?php

    /* SVN FILE: $Id$ */
// print_r(debug_backtrace());exit;

//  ini_set('error_reporting', E_ALL);
//  ini_set('track_errors', '1');
//  ini_set('display_errors', '1');
//  ini_set('display_startup_errors', '1');

    /**
     * @author Oliver Lillie (aka buggedcom) <publicmail@buggedcom.co.uk>
     *
     * @license BSD
     * @copyright Copyright (c) 2008 Oliver Lillie <http://www.buggedcom.co.uk>
     * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
     * files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
     * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
     * is furnished to do so, subject to the following conditions:  The above copyright notice and this permission notice shall be
     * included in all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
     * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
     * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
     * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
     *
     * @package CustomTags
     * @version 1.0.0
     */
    
    namespace CustomTags;

    class CustomTags
    {
        
        public $version                 = '0.2.4';
        private static $_instance       = 0;
        
        /**
         * Holds the options array
         * @access private
         * @var array
         */
        private $_options               = array(
//          parse_on_shutdown; boolean; If 'use_buffer' is enabled and this option is also enalbed it will create a 
//              register_shutdown_function that will process the buffered output at the end of the script without any hassle.
            'parse_on_shutdown'         => false,
//          use_buffer; boolean; You can optionally use output buffering instead of providing the html for compacting.
            'use_buffer'                => false,
//          echo_output; boolean; If after processing you want to output the content set this to true, otherwise it
//              will be up to you to echo out the compacted html.
            'echo_output'               => false,
//          tag_name; string; The custom tag prefix
            'tag_name'                  => 'ct',
            'tag_callback_prefix'       => 'ct_', 
//          tag_global_callback if this is set ALL callbacks will go through this function, it will be given 3 arguments
//          the first is the name of the function to be called, the second is the tag data and the third is the buffer. Note
//          YOU MUST make sure this callback is available to be called, otherwise custom tags will fail without error.
            'tag_global_callback'       => false,
//          tag_directory; The directory of the custom tags.
            'tag_directory'             => false,
//          template_directory; The directory of the custom tags.
            'template_directory'        => false,
//          missing_tags_error_mode; The error mode for outputting errors.
//              CustomTags::ERROR_EXCEPTION throws and exception for catching.
//              CustomTags::ERROR_SILENT returns empty data if a tag or error occurs.
//              CustomTags::ERROR_ECHO returns an error string.
            'missing_tags_error_mode'   => CustomTags::ERROR_EXCEPTION,
//          sniff_for_buried_tags; Checks the parsed tag content for buried tags. If you know it's not needed then
//              you should not enable the sniffing for code optimisation
            'sniff_for_buried_tags'     => false,
//          cache_tags; You can optionaly cache the tag output for improved performance. If enabled you will also
//              need to set the cache_directory option, alternativley you can use your own cache class.
            'cache_tags'                => false,
//          cache_directory; The diretory to save the tag cache in.
            'cache_directory'           => false,
//          custom_cache_tag_class; A custom cache class for your own manipulation of the tag cache
//              using the custom_cache_tag_class, the class should have two functions one for checking the cache
//              "getCache" and one for saving the cache "cache".
            'custom_cache_tag_class'    => false,
//          hash_tags; A boolean value that dictates if tags with inner content should have hash tags #{varname}
//             matched for values and put into the array returned to the callback function
            'hash_tags'    => false,
        );
        
        const ERROR_EXCEPTION           = 'THROW_EXCEPTION';
        const ERROR_SILENT              = 'ERROR_SILENT';
        const ERROR_ECHO                = 'ERROR_ECHO';
        
        public static $name             = 'CustomTags';
        public static $nocache_tags     = array();
        private static $_required       = array();
        private static $_tags_to_collect        = array();
        private $_collections           = array();
        private $_registered            = array();
        private $_buffer_in_use         = false;
        public static $tag_collections = array();
        private static $_tag_order      = array();
        
        public static $tag_directory_base = false; 
        public static $template_directory_base = false;
        public static $error_mode       = false;
        
//      The custom tag open string
        public static $tag_open         = '<';
//      The custom tag close string
        public static $tag_close        = '>';
        
        function __construct($options=array())
        {
            $this->setOption($options);
            if($this->_options['parse_on_shutdown'])
            {
                $this->setOption(array(
                    'use_buffer' => true,
                    'echo_output' => true
                ));
            }
            
            if($this->_options['template_directory'] === false)
            {
                $this->setOption('template_directory', isset($this->_options['tag_directory']) === false ? dirname(__FILE__).DIRECTORY_SEPARATOR.'tags'.DIRECTORY_SEPARATOR : $this->_options['tag_directory']);
            }
            
            if($this->_options['tag_directory'] === false)
            {
                $this->setOption('tag_directory', dirname(__FILE__).DIRECTORY_SEPARATOR.'tags'.DIRECTORY_SEPARATOR);
            }
            
            if($this->_options['missing_tags_error_mode'] === false)
            {
                $this->setOption('missing_tags_error_mode',  self::ERROR_EXCEPTION);
            }

            if($this->_options['cache_tags'])
            {
                if($this->_options['custom_cache_tag_class'] === false)
                {
                    if($this->_options['cache_directory'] === false)
                    {
                        $this->setOption('cache_directory',  dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);
                    }
                    else
                    {
                        if(is_dir($this->_options['cache_directory']) || !is_writable($this->_options['cache_directory']) === false)
                        {
                            $this->_options['cache_tags'] = false;
                        }
                    }
                }
                else
                {
                    if(class_exists($this->_options['custom_cache_tag_class']) === false)
                    {
                        $this->_options['cache_tags'] = false;
                    }
                }
            }
            
            if($this->_options['use_buffer'] === true)
            {
                ob_start();
            }
            if($this->_options['parse_on_shutdown'] === true)
            {
                register_shutdown_function(array(&$this, 'parse'));
            }
            
        }
        
        /**
         * Sets an option in the option array();
         * 
         * @access public
         * @param mixed $varname Can take the form of an array of options to set a string of an option name.
         * @param mixed $varvalue The value of the option you are setting.
         **/
        public function setOption($varname, $varvalue=null)
        {
            $keys = array_keys($this->_options);
            if(gettype($varname) === 'array')
            {
                foreach($varname as $name=>$value)
                {
                    $this->setOption($name, $value);
                }
            }
            else
            {
                if(in_array($varname, $keys) === true)
                {
                    $this->_options[$varname] = $varvalue;
                }
                switch($varname)
                {
                    case 'template_directory' :
                        self::$template_directory_base = $varvalue;
                        break;
                    case 'tag_directory' :
                        self::$tag_directory_base = $varvalue;
                        break;
                    case 'missing_tags_error_mode' :
                        self::$error_mode = $varvalue;
                        break;
                    case 'custom_cache_tag_class' :
                        if(class_exists($varvalue) === false)
                        {
                            $this->_options['cache_tags'] = false;
                        }
                        break;
                    case 'cache_directory' :
                        if(is_dir($varvalue) === false || is_writable($varvalue) === false)
                        {
                            $this->_options['cache_tags'] = false;
                        }
                        break;
                }
            }
        }
        
        /**
         * Registers a parsed tag. Each tag callback must register the parsed tag so
         * deep buried tags can be correctly replaced.
         * 
         * @access public
         * @param array $tag 
         * @return void
         */
        public static function registerParsedTag($tag)
        {
            self::$registered[$tag['source_marker']] = $tag;
        }
        
        /**
         * Parses the source for any custom tags.
         * @access public
         * @param mixed|boolean|string $source If false then it will capture the output buffer, otherwise if a string
         *  it will use this value to search for custom tags.
         * @param boolean $parse_collections If true then any collected tags will be parsed after the tags are parsed.
         * @return string The parsed $source value.
         */
        public function parse($source=false, $parse_collections=false, $_internal_loop=false)
        {
//          increment the parse count so it has unique identifiers
            self::$_instance += 1;
//          capture the source from the buffer
            if($source === false)
            {
                $source = ob_get_clean();
                $this->_buffer_in_use = true;
                $parse_collections = true;
            }
            
//          collect the tags for processing
            $tags = $this->collectTags($source);
            if(count($tags) > 0)
            {
//              there are tags so process them
                $output = $this->_parseTags($tags);
                if($output && $parse_collections === true)
                {
//                  parse any collected tags if required
                    $output = $this->_processCollectedTags($output);
                }
                if($this->_options['echo_output'] === true)
                {
                    echo $output;
                }
                return $output;
            }
            return $source;
        }
        
        /**
         * Processes a tag by loading
         * @access private
         * @param array $tag The tag to parse.
         * @return string The content of the tag.
         */
        private function _parseTag($tag)
        {   
//          return nothing if the tag is disabled
            if(isset($tag['attributes']->disabled) === true && $tag['attributes']->disabled === 'true')
            {
                return '';
            }
            
            $tag_data = false;
            $caching_tag = isset($tag['attributes']->cache) && $tag['attributes']->cache === 'false' ? false : true;
            if($this->_options['cache_tags'] === true && $caching_tag === true)
            {
                if($this->_options['custom_cache_tag_class'] !== false)
                {
                    $tag_data = call_user_func_array(array($this->_options['custom_cache_tag_class'], 'getCache'), array($tag));
                }
                else 
                {
                    $cache_file = $this->_options['cache_directory'].md5(serialize($tag));
                    if(is_file($cache_file) === true)
                    {
                        $tag_data = file_get_contents($cache_file);
                    }
                }
                if($tag_data)
                {
                    $tag['cached'] = true;
                    return $tag_data;
                }
            }    
            
//          look for and load tag function file
            $tag_func_name = ucwords(str_replace(array('_', '-'), ' ', $tag['name']));
            $tag_func_name = strtolower(substr($tag_func_name, 0, 1)).substr($tag_func_name, 1);
            $func_name = str_replace(' ', '', $this->_options['tag_callback_prefix'].$tag_func_name);
            $tag_data = '';
            $collect_tag = false;
            $tag_order = false;
            if(function_exists($func_name) === false)
            {
                $has_resource = false;
                if(is_array($this->_options['tag_directory']) === false)
                {
                    $this->_options['tag_directory'] = array($this->_options['tag_directory']);
                }
                foreach ($this->_options['tag_directory'] as $directory)
                {
                    $tag_file = rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$tag['name'].DIRECTORY_SEPARATOR.'tag.php';
                    if(is_file($tag_file) === true)
                    {
                        if(isset(self::$_required[$tag['name']]) === false)
                        {
                            self::$_required[$tag['name']] = true;
                            $collect = false;
                            if(function_exists($func_name) === false)
                            {
                                include_once $tag_file;
                            }
                            self::$_tags_to_collect[$tag['name']] = $collect;
                            if(function_exists($func_name) === false)
                            {
                                return self::throwError($tag['name'], 'tag resource "'.$directory.DIRECTORY_SEPARATOR.'tag.php" found but callback "'.$func_name.'" wasn\'t.');
                            }
                            $has_resource = true;
                        }
                    }
                }
                if($has_resource === false)
                {
                    return self::throwError($tag['name'], 'tag resource not found.');
                } 
            }
            
//          do we have to collect this tag for parsing at a later point
            if(self::$_tags_to_collect[$tag['name']] !== false)
            {
                if(isset(self::$tag_collections[$tag['name']]) === false)
                {
                    self::$tag_collections[$tag['name']] = array();
                }
                $tag['collected'] = true;
                $index = array_push(self::$tag_collections[$tag['name']], $tag)-1;
                if($tag_order !== false)
                {
                    if(isset(self::$_tag_order[$block['name']]) === false)
                    {
                        self::$_tag_order[$block['name']] = array();
                    }
                    self::$_tag_order[$block['name']] = $tag_order;
                }
                return self::$tag_collections[$tag['name']][$index]['tag'] = '------@@%'.self::$_instance.'-'.$tag['name'].'-'.$index.'-'.uniqid(time().'-').'%@@------';
            }
            
//          excecute the tag callback
            if($this->_options['tag_global_callback'] !== false)
            {
                $tag_data = trim(call_user_func($this->_options['tag_global_callback'], 'tag', $func_name, $tag));
            }
            else
            {
                $tag_data = trim(call_user_func($func_name, $tag));
            }
            
//          this is where we sniff for buried tags within the returned content
            if(empty($tag_data) === false)
            {
                if($this->_options['sniff_for_buried_tags'] === true && strpos($tag_data, self::$tag_open.$this->_options['tag_name'].':') !== false)
                {
//                  we have the possibility of buried tags so lets parse
//                  but first make sure the output isn't echoed out
                    $old_echo_value = $this->_options['echo_output'];
                    $this->_options['echo_output'] = false; 
//                  parse the tag_data 
                    $tag_data = $this->parse($tag_data, false, true);
//                  restore the echo_output value back to what it was originally
                    $this->_options['echo_output'] = $old_echo_value;
                }
                
                if($this->_options['cache_tags'] === true && $caching_tag === true)
                {
                    if($this->_options['custom_cache_tag_class'] !== false)
                    {
                        call_user_func_array(array($this->_options['custom_cache_tag_class'], 'cache'), array($tag, $tag_data));
                    }
                    else
                    {
                        file_put_contents($this->_options['cache_directory'].md5(serialize($tag)), $tag_data, LOCK_EX);
                    }
                }
            }
            return $tag_data;
        }
        
        /**
         * Produces an error.
         * @access public
         * @param string $tag The name of the tag producing an error.
         * @param string $message The message of the error.
         * @return mixed|error|string Either a string or thrown error is returned dependent 
         *  on the 'missing_tags_error_mode' option.
         */
        public static function throwError($tag, $message)
        {
            if(self::$error_mode === self::ERROR_EXCEPTION && !$this->_buffer_in_use)
            {
                throw new CustomTagsException('<strong>'.$tag.'</strong> '.$message.'.');
            }
            else if(self::$error_mode !== self::ERROR_SILENT)
            {
                return '<strong>['.self::$name.' Error]</strong>: '.ucfirst($tag).' Tag - '.$message.'<br />';
            }
            return '';
        }
        
        /**
         * Loops and parses the found custom tags.
         * @access private
         * @param array $tags An array of found custom tag data.
         * @return mixed|string|boolean Returns false if there are no tags, string otherwise.
         */
        private function _parseTags($tags)
        {
            if(count($tags) > 0)
            {   
//              loop through the tags
                foreach($tags as $key=>$tag)
                {   
//                  if a tag is delayed, it is rendered after everything else and it's has to be re parsed.
                    if(isset($tag['attributes']) === true && isset($tag['attributes']->delayed) === true && $tag['attributes']->delayed === 'true')
                    {
                        continue;
                    }
//                  check for buried preserved tags, so they can be replaced.
//                  NOTE: this only works with no collected tags. Collected tags are a massive pain in the rectum.
                    if(($has_buried = preg_match_all('!------@@%([0-9\-]+)%@@------!', $tag['content'], $info)) > 0)
                    {   
                        $containers = $info[0];
                        $indexs = $info[1];
                        $replacements = array();
                        foreach ($indexs as $key2=>$index)
                        {
                            $index_parts = explode('-', $index);
                            $tag_index = array_pop($index_parts);
                            if(isset($tags[$tag_index]['parsed']) === true)
                            {
                                $replacements[$key2] = $tags[$tag_index]['parsed'];
                            }
                            else
                            {   
                                if(isset($tags[$tag_index]['block']) === true)
                                {
                                    $block = preg_replace('/ delayed="true"/', '', $tags[$tag_index]['block'], 1);
                                    if(isset($tag['block']) === true)
                                    {
                                        $tag['block'] = str_replace($containers[$key2], $block, $tag['block']); 
                                    }
                                    $tag['content'] = str_replace($containers[$key2], $block, $tag['content']); 
                                }
                            }
                        }
                        $tags[$key]['buried_source_markers'] = $tag['buried_source_markers'] = $containers;
                    }

//                  if the tag is a nocahe tag then the block must be replaced as is for later processing
                    if($tag['name'] === 'nocache')
                    {
                        array_push(self::$nocache_tags, $tag);
                        $tags[$key]['parsed'] = $tag['source_marker'];
//                      $tags[$key]['parsed'] = $tag['block'];
                    }
//                  if the tag is just plain text then just shove the content back into the parsed as it doesn't require processing
                    else if($tag['name'] === '___text')
                    {
                        $tags[$key]['parsed'] = $tag['content'];
                    }
//                  otherwise we have a tag and must post process
                    else
                    {   
                        $tags[$key]['parsed'] = $this->_parseTag($tag);
                    }
//                  update any buried tags within the parsed content
                    $tags[$key]['parsed'] = $has_buried > 0 ? str_replace($containers, $replacements, $tags[$key]['parsed']) : $tags[$key]['parsed'];
                }
// //              if there are items within the nocache elements, parse the contents to pull them out
//                 $tagname = $this->_options['tag_name'];
//                 if(substr_count($tags[$key]['parsed'], self::$tag_open.$tagname.':') > substr_count($tags[$key]['parsed'], self::$tag_open.$tagname.':nocache'))
//                 {
//                     $tags[$key]['parsed'] = $this->parse($tags[$key]['parsed'], true, true);
// //                  $tags[$key]['parsed'] = $this->_processTags($tags[$key]['parsed'], true);
//                 }
                
//                 Debug::info($tags[$key]);
                
                return $tags[$key]['parsed'];
            }
            return false;
        }
        
        /**
         * Process collected blocks. These are different types of block that are required to be processed as a group.
         * Usual reasons for doing this are to reduce resources and sql queries.
         *
         * @param string $source The source of the output.
         * @return string
         */
        function _processCollectedTags($source)
        {
            $to_replace = array();
            $ordered = array();
            foreach(self::$tag_collections as $tag_name=>$tags)
            {
//              if this block has a collection order use it
                if(isset(self::$_tag_order[$tag_name]) === true)
                {
                    $pos = self::$_tag_order[$tag_name];
                    $ordered[$pos] = $tags;
                    continue;
                }
//              the source should be modified by the collection script
                $tag_func_name = ucwords(str_replace(array('_', '-'), ' ', $tag_name));
                $tag_func_name = strtolower(substr($tag_func_name, 0, 1)).substr($tag_func_name, 1);
                $tag_func_name = str_replace(' ', '', $this->_options['tag_callback_prefix'].$tag_func_name);
                if($this->_options['tag_global_callback'] !== false)
                {
                    $tags = call_user_func($this->_options['tag_global_callback'], 'collection', $tag_func_name, $tags, $source);
                }
                else
                {
                    $tags = call_user_func($tag_func_name, $tags, $source);
                }
                
                if($tags === -1)
                {
                    $source = self::throwError($tag_name, 'tag collection parsed however callback "'.$tag_func_name.'" returned -1.');
                }
                else if(is_array($tags) === false)
                {
                    $source = self::throwError($tag_name, 'tag collection parsed however callback "'.$tag_func_name.'" did not return the array of process tags.');
                }
                else if(count($tags) > 0)
                {
//                  input the parsed tags back into the source or store for re-intergration
                    foreach ($tags as $key => $tag)
                    {
                        if(strpos($source, $tag['tag']) !== false)
                        {
                            $source = str_replace($tag['tag'], $tag['parsed'], $source);
                        }
                        else
                        {
//                          do a reverse tag lookup here as the tag was not found in the source, thus it must be buried and un processed
                            array_push($to_replace, $tag);
                        }
                    }
                }
            }
            foreach($ordered as $key=>$tags)
            {
//              the source should be modified by the collection script
                $tag_func_name = ucwords(str_replace(array('_', '-'), ' ', $tag_name));
                $tag_func_name = strtolower(substr($tag_func_name, 0, 1)).substr($tag_func_name, 1);
                $tag_func_name = str_replace(' ', '', $this->_options['tag_callback_prefix'].$tag_func_name);
                $tags = call_user_func($tag_func_name, $tags);
                if($tags === -1)
                {
                    $source = self::throwError($tag['name'], 'tag collection parsed however callback "'.$tag_func_name.'" returned -1.');
                }
            }
            return $this->_doBuriedReplacements($to_replace, $source);
        }
        
        private function _doBuriedReplacements($replacements, $source)
        {
            if(count($replacements) > 0)
            {
                $to_replace = array();
                foreach ($replacements as $key => $tag)
                {
                    if(strpos($source, $tag['source_marker']) !== false)
                    {
                        $source = str_replace($tag['source_marker'], $tag['parsed'], $source);
                    }
                    else
                    {
                        array_push($to_replace, $tag);
                    }
                }
                if(count($to_replace) > 0)
                {
                    $source = $this->_doBuriedReplacements($to_replace, $source);
                }
            }
            return $source;
        }
        
        /**
         * Searches and parses a source for custom tags.
         * @access public
         * @param string $source The source to search for custom tags in.
         * @param mixed|boolean|string $tag_name If false then the default option 'tag_name' is used
         *  when searching for custom tags, if not and $tag_name is a string then a custom tag beginning 
         *  with that prefix will be looked for.
         * @return array An array of found tags.
         */
        public function collectTags($source, $tag_name=false)
        {
            $tagname = $tag_name === false ? $this->_options['tag_name'] : $tag_name;
            
            $tags = $tag_names = array();
            $tag_count = 0;
            
            $inner_tag_open_pos = $source_len = strlen($source);
            $opener = self::$tag_open.$tagname.':';
            $opener_len = strlen($opener);
            $closer = self::$tag_open.'/'.$tagname.':';
            $closer_len = strlen($closer);
            $closer_end = self::$tag_close;

            while ($inner_tag_open_pos !== false)
            {
//              start getting the last found opener tag
                $open_tag_look_source   = substr($source, 0, $inner_tag_open_pos);
                $open_tag_look_len      = strlen($open_tag_look_source);
                $inner_tag_open_pos     = strrpos($open_tag_look_source, $opener);

//              if there is no last tag then the rest is the final text
                if($inner_tag_open_pos === false)
                {
                    array_push($tags, array('content'=>$source, 'name'=>'___text'));
                    break;
                }
                else
                {
//                  get the source from the start of that last tag
                    $tag_look_source            = substr($source, $inner_tag_open_pos);
                    $open_bracket_pos           = strpos($tag_look_source, self::$tag_open, 1);
                    $short_tag_close_pos        = strpos($tag_look_source, '/'.self::$tag_close);
                    
                    if($short_tag_close_pos !== false && $short_tag_close_pos < $open_bracket_pos)
                    {
                        $inner_tag_close_pos = $short_tag_close_pos + 2;
                    }
                    else
                    {
                        $inner_tag_close_pos_begin  = strpos($tag_look_source, $closer);
                        $inner_tag_close_pos        = strpos($tag_look_source, $closer_end, $inner_tag_close_pos_begin)+1;
                    }

//                  get the content of the block
                    $tag_source     = substr($tag_look_source, 0, $inner_tag_close_pos);
    
                    $tag            = $this->_buildTag($tag_source, $tagname);
                    $index          = count($tags);
                    $tag['source_marker'] = '------@@%'.self::$_instance.'-'.$index.'%@@------';
                    array_push($tags, $tag);
        
//                  modify the source so it doesn't get repeated
                    $source = substr($source, 0, $inner_tag_open_pos).$tag['source_marker'].substr($source, $inner_tag_open_pos+$inner_tag_close_pos);
//                  $source = str_replace($tag_source, '------@@%'.$index.'%@@------', $source);
                }
            }
            return $tags;
        }
        
        /**
         * Parses a tag for the tag attributes and inner content.
         * @access private
         * @param string $str The tag string to be parsed.
         * @param string $tagname The prefix of the custom tag being parsed.
         * @return array The tag
         */
        private function _buildTag($str, $tagname)
        {
//          $tagname = $this->_options['tag_name'];
            $tag = array(
                'block'         => $str,
                'content'       => '',
                'name'          => '',
                'attributes'    => array()
            );
            $begin_len = strlen(self::$tag_open.$tagname.':');
//          echo substr($str, 0, $begin_len)."\r\n";
            if(substr($str, 0, $begin_len) !== self::$tag_open.$tagname.':')
            {
//              closing tag
                $tag['name'] = '___text';
                return $tag;
            }
            else if(substr($str, 1, 1) === '/')
            {
//              closing tag
                $tag['name'] = '---ERROR---';
                return $tag;
            }
            else
            {
//              opening tag
                $matches = array();
//              check to see if this is a full tag or an openclose tag
                $has_closing_tag = substr($tag['block'], -2) !== '/'.self::$tag_close;
//              perform data matches
                if($has_closing_tag === true)
                {
                    $preg = '!(\\'.self::$tag_open.$tagname.':([_\-A-Za-z0-9]*)[^\\'.self::$tag_close.']*\\'.self::$tag_close.'| \/\\'.self::$tag_close.').*\\'.self::$tag_open.'\/'.$tagname.':[_\-A-Za-z0-9]*\\'.self::$tag_close.'!is';
//                  $preg = '!(\<'.$tagname.':([_\-A-Za-z0-9]*)[^\>]*\>| \/\>).*\<\/'.$tagname.':[_\-A-Za-z0-9]*\>!is';
                }
                else
                {
                    $preg = '!(\\'.self::$tag_open.$tagname.':([_\-A-Za-z0-9]*)([^\\'.self::$tag_close.']*))!is';
//                  $preg = '!(\<'.$tagname.':([_\-A-Za-z0-9]*)([^\/\>]*))!is';
                }
                
                if(preg_match_all($preg, $tag['block'], $matches) > 0)
                {
//                  get the tag type
                    $tag['name']    = $matches[2][0];
                
//                  get the tag inner content
                    $tag['content'] = '';
                    $tag['vars'] = false;
                    $attribute_string = $matches[1][0];
                    if($has_closing_tag === true)
                    {
                        $begin_len      = strlen($matches[1][0]);
                        $end_len = $has_closing_tag ? strlen(self::$tag_open.'/'.$tagname.':'.$tag['name'].self::$tag_close) : 0;
                        $tag['content'] = substr($str, $begin_len, strlen($matches[0][0])-$begin_len-$end_len);
//                      get hash tags vars?
                        if($this->_options['hash_tags'] === true && preg_match_all('/#{([^}]+)?}/i', $tag['content'], $vars) > 0)
                        {                       
                            $variables = array();
                            foreach ($vars[0] as $key => $var)
                            {
                                $variables[$var] = $vars[1][$key]; 
                            }
                            $tag['vars'] = $variables;
                        }
                    }
                    else
                    {
                        $attribute_string = rtrim($attribute_string, '/ ');
                    }
                    
//                  get the attributes
                    $attributes = array();
//                  preg_match_all("/([_\-A-Za-z0-9]*)((=\"|='))([\w\W\s]*)(\"|')/", $opener, $attributes);
//                  preg_match_all("/([_\-A-Za-z0-9]*)((=\"|='))([_\-A-Za-z0-9]*)(\"|')/", $opener, $atts);
//                  $result = preg_match_all("!([_\-A-Za-z0-9]*)(=\"|=')([\#\s\:\.\?\&\=\%\+\,\@\_\-A-Za-z0-9]*)(\"|')!is", $matches[1][0], $attributes);
                    if(preg_match_all("!([_\-A-Za-z0-9]*)(=\")([^\"]*)(\")!is", $attribute_string, $attributes) > 0)
                    {
                        foreach($attributes[0] as $key=>$row)
                        {
                            $tag['attributes'][$attributes[1][$key]] = $attributes[3][$key];
                        }
                        if(isset($tag['attributes']['template']) === true)
                        {                                             
                            $template = $this->_options['template_directory'].$tag['name'].DS.$tag['attributes']['template'].'.html';
                            if(is_file($template) === false)
                            {                                 
//                                $tag['attributes']['template'] = false;
                                $tag['attributes']['_template'] = $template;
                            }
                            else
                            {
                                $tag['attributes']['template'] = $template;
                            }
                        }
                    }
                } 
                $tag['attributes'] = (object) $tag['attributes'];
                return $tag;
            }
        }
        
        
    }
    
    /**
     * The Custom Tags exception.
     */
    class CustomTagsException extends \Exception { }
    
    /**
     * The Custom Tags Helper class, It can help in making small custom tags,
     * however it is best to use your own template system or way of doing things.
     */
    class CustomTagsHelper
    {
        /**
         * Returns the content of a template.
         * @access public
         * @param array $tag The tag array.
         * @param boolean $produce_error If there is an error with the template and this is set to true then an error is produced.
         */
        public static function getTemplate($tag, $produce_error=true)
        {
//          get the template
            $template_name = isset($tag['attributes']['template']) === true ? $tag['attributes']['template'] : 'default';
            if(is_array(CustomTags::$tag_directory_base) === false)
            {
                CustomTags::$tag_directory_base = array(CustomTags::$tag_directory_base);
            }
            foreach (CustomTags::$tag_directory_base as $directory)
            {
                $template = rtrim($directory, DIRECTORY_SEPARATOR).$tag['name'].DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template_name.'.html';
                if(is_file($template) === true)
                {
                    return file_get_contents($template);
                }
            }
//          template doesn't exist so produce error if required
            if($produce_error === true)
            {
                CustomTags::throwError($tag['name'], 'tag template resource not found.');
            }
            return false;
        }
        
        /**
         * A simple templater, replaces %VARNAME% with the value.
         * @access public
         * @param array $tag The tag array.
         * @param array $replacements The array of search and replace values.
         */
        public static function parseTemplate($tag, $replacements)
        {
//          get template
            $template = self::getTemplate($tag);
            $search = $replace = array();
//          compile search and replace values for replacement
            foreach ($replacements as $varname => $varvalue)
            {
                array_push($search, '%'.strtolower($varname).'%');
                array_push($replace, $varvalue);
            }
            return str_replace($search, $replace, $template);
        }
        
    }
    
