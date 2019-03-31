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

    /**
     *
     * ==
     * @param string $needle
     * @param string $haystack
     * @return bool
     */
    public function startsWith( $needle = "", $haystack = "" )
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     *
     * ==
     * @param string $needle
     * @param string $haystack
     * @return bool
     */
    public function endsWith( $needle = "", $haystack = "" )
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    /**
     *
     * ==
     * @param string $url
     * @return mixed
     */
    public function getDomain( $url = "")
    {
        $parts = \parse_url($url);
        return isset($parts['host']) ? $parts['host'] : FALSE;
    }

    public function getDomainWithPrefix( $url = "")
    {
        $parts = \parse_url($url);
        if(!isset($parts['scheme'])){
            $parts['scheme'] = 'http';
        }
        return isset($parts['host']) ? $parts['scheme'] . '://' . $parts['host'] : $url;
    }

    /**
     *
     * ==
     * @param string $url
     * @return bool|string
     */
    public static function removeQueryString( $url = "" )
    {
        try {
            $parts = parse_url($url);
            return $parts['scheme'] . '://' . $parts['host'] . $parts['path'];
        }catch(\Exception $ex){
            return false;
        }
    }

    /**
     * Are we matching a file ?
     * ==
     * @param string $url
     * @return false|int
     */
    public static function isImageFile( $url = "")
    {
        return preg_match("/^[^\?]+\.(pdf|jpg|jpeg|gif|png)(?:\?|$)/", $url);
    }


    /**
     * Get External Links
     * ==
     * @param string $html
     * @return array
     */
    public static function getExternalLinks($html = "", $current_domain = false)
    {
        $ret = [];
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link)
        {
            $href = $link->getAttribute('href');

            if ( !UrlService::startsWith('#', $href) && $href != '/' )
            {
                if( UrlService::startsWith( "http:", $href ) || UrlService::startsWith('https:', $href ) )
                {
                    $link_domain = self::getDomain($href);

                    if( $link_domain != $current_domain ){

                        $cleanedUrl =   self::removeQueryString($href);

                        $cleanedUrl = self::getDomainWithPrefix($cleanedUrl);

                        if( FALSE !== $cleanedUrl ){

                            if( !self::isImageFile( $cleanedUrl) ){

                                $ret[] = $cleanedUrl;

                                //$ret[] = $link_domain;
                            }
                        }
                    }
                }
            }
        }

        $ret = array_unique($ret);

        return $ret;
    }


    /**
     * Get External Links
     * ==
     * @param string $html
     * @return array
     */
    public static function getInternalLinks($html = "", $current_domain = false)
    {
        $ret = [];
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link)
        {
            $href = $link->getAttribute('href');

            if ( !UrlService::startsWith('#', $href) && $href != '/' )
            {
                if( UrlService::startsWith( "http:", $href ) || UrlService::startsWith('https:', $href ) )
                {
                    $link_domain = self::getDomain($href);

                    if( $link_domain == $current_domain ){

                        $cleanedUrl = self::removeQueryString($href);

                        if( FALSE !== $cleanedUrl ){

                            if( !self::isImageFile( $cleanedUrl) ){

                                $ret[] = $cleanedUrl;
                            }
                        }
                    }
                }
            }
        }

        $ret = array_unique($ret);

        return $ret;
    }

}