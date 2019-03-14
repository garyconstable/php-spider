<?php

namespace App\Service;

class UrlService
{

    /**
     * Debugger
     * ==
     * @param array $data
     * @param bool $die
     */
    public static function d($data = [], $die = TRUE)
    {
        echo '<pre>'.print_r($data, TRUE).'</pre>';
        if($die){
            die();
        }
    }

    public function startsWith( $needle = "", $haystack = "" )
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public function endsWith( $needle = "", $haystack = "" )
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    public function getDomain( $url = "")
    {
        $parts = \parse_url($url);
        return $parts['host'];
    }
}