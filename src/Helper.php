<?php 

namespace DataParser;

/**
 * 

 */
class Helper
{
	
	function arrayGet($input, $key, $default = null)
    {

        if (is_null($key)) {
            return $input;
        }

        $arr = explode('.', $key);
        foreach ($arr as $k) {
            $input = isset($input[$k]) ? $input[$k] : null;
        }

        if (is_null($input)) {
            return $default;
        }

        return $input;
    }
}