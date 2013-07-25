<?php
    
    /**
     * This file is part of the CustomTags package.
     *
     * @author Oliver Lillie (aka buggedcom) <publicmail@buggedcom.co.uk>
     * @license MIT
     * @copyright Copyright (c) 2008-2013 Oliver Lillie <http://www.buggedcom.co.uk>
     * @package CustomTags
     * @version 1.0.0
     */
    
    spl_autoload_register(function($class_name)
    {
        $parts = explode('\\', $class_name);
        $namespace = array_shift($parts);
        if($namespace === 'CustomTags')
        {
            $class = str_replace('_', DIRECTORY_SEPARATOR, array_pop($parts));
            $path = dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.ltrim(implode(DIRECTORY_SEPARATOR, $parts).DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR).$class.'.php';
            if(is_file($path) === true)
            {
                require_once $path;
            }
        }
    });