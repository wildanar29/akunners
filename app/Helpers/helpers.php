<?php

if (!function_exists('str_slug')) {
    function str_slug($string, $separator = '-')
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', $separator, $string), $separator));
    }
	
}

if (! function_exists('public_path')) {
    function public_path($path = '')
    {
        return app()->basePath('public') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

