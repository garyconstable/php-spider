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
    public static function d($data = [], $die = true)
    {
        echo '<pre>'.print_r($data, true).'</pre>';
        if ($die) {
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
    public static function startsWith($needle = "", $haystack = "")
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
    public static function endsWith($needle = "", $haystack = "")
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
    public static function getDomain($url = "")
    {
        $parts = \parse_url($url);
        return isset($parts['host']) ? $parts['host'] : false;
    }

    /**
     *
     * ==
     * @param string $url
     * @return string
     */
    public static function getDomainWithPrefix($url = "")
    {
        $parts = \parse_url($url);
        if (!isset($parts['scheme'])) {
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
    public static function removeQueryString($url = "")
    {
        try {
            $parts = parse_url($url);
            return $parts['scheme'] . '://' . $parts['host'] . $parts['path'];
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Are we matching a file ?
     * ==
     * @param string $url
     * @return false|int
     */
    public static function isImageFile($url = "")
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
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (!UrlService::startsWith('#', $href) && $href != '/') {
                if (UrlService::startsWith("http:", $href) || UrlService::startsWith('https:', $href)) {
                    $link_domain = self::getDomain($href);
                    if ($link_domain != $current_domain) {
                        $cleanedUrl =   self::removeQueryString($href);
                        $cleanedUrl = self::getDomainWithPrefix($cleanedUrl);
                        if (false !== $cleanedUrl) {
                            if (!self::isImageFile($cleanedUrl)) {
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
     * @param bool $current_domain
     * @return array
     */
    public static function getInternalLinks($html = "", $current_domain = false)
    {
        $ret = [];
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');

            if (!UrlService::startsWith('#', $href) && $href != '/') {
                if (UrlService::startsWith("http:", $href) || UrlService::startsWith('https:', $href)) {
                    $link_domain = self::getDomain($href);
                    if ($link_domain == $current_domain) {
                        $cleanedUrl = self::removeQueryString($href);
                        if (false !== $cleanedUrl) {
                            if (!self::isImageFile($cleanedUrl)) {
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

    /**
     *
     * ==
     * @param $html
     * @return array
     */
    public static function getEmails($html)
    {
        //initialise an empty array.
        $matches = array();

        //regular expression that matches most email addresses, courtesy of @Eric-Karl.
        $pattern = '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}/i';

        //perform global regular expression match,
        // ie search the entire web page for a particular thing, and store it in the previously initialised array.
        preg_match_all($pattern, $html, $matches);

        //store above in array for upcoming bit.
        return array_values(array_unique($matches[0]));
    }
}