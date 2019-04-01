<?php

namespace App\Service;


class DomainWorkerService
{
    private $ds;

    private $emails         = [];

    private $internal_links = [];

    private $external_links = [];

    private $current_domain = "";

    private $max_pages_crawled = 100;

    /**
     * DomainWorkerService constructor.
     * ==
     */
    public function __construct()
    {

    }

    /**
     * Debugger
     * ==
     * @param array $data
     * @param bool $die
     */
    public function d($data = [], $die = TRUE)
    {
        echo '<pre>'.print_r($data, TRUE).'</pre>';
        if($die){
            die();
        }
    }

    /**
     * ==
     * @param $url
     * @throws \Exception
     */
    public function crawl($url)
    {
        $this->current_domain = UrlService::getDomain($url);

        echo '--> ' . $this->current_domain . PHP_EOL;

        $this->scrapePage($url);

        $this->linkRunner();
    }

    /**
     * ==
     * @return mixed
     */
    public function getNextInternalLink()
    {
        foreach( $this->internal_links as &$item )
        {
            if($item['visited'] == false )
            {
                $item['visited'] = true;
                return $item['link'];
            }
        }
        return FALSE;
    }

    /**
     * ==
     * @throws \Exception
     */
    function linkRunner()
    {
        $url = $this->getNextInternalLink();

        $this->max_pages_crawled -= 1;

        if( FALSE !== $url &&  $this->max_pages_crawled > 0){

            $this->scrapePage($url);

            $this->linkRunner();
        }
    }

    /**
     * Get the page
     * ==
     * @param string $url
     * @return string
     */
    public function getPage($url = "")
    {
        try {
            $header_str = "Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.27 Safari/537.17 ";
            $ctx = stream_context_create(array(
                    'http' => array(
                        'timeout' => 1,
                        'method' => "GET",
                        'header' => "Accept-language: en\r\n" .
                            "Cookie: foo=bar\r\n" .
                            "User-Agent: " . $header_str . "\r\n"
                    ))
            );
            return file_get_contents($url, false, $ctx);
        }catch(\Exception $ex){
            return false;
        }
    }

    /**
     *
     * ==
     * @param $emails
     */
    public function addEmails($emails)
    {
        foreach($emails as $email)
        {
            if(!in_array($email, $this->emails))
            {
               $this->emails[] = $email;
            }
        }
    }

    /**
     * ==
     * @param array $links
     * @param bool $internal
     */
    function addLink( $links = [], $internal = true )
    {
        if($internal){
            $arr = &$this->internal_links;
        }else{
            $arr = &$this->external_links;
        }

        foreach($links as $link)
        {
            if(empty($arr)){
                $arr[] = [
                    'link'      => $link,
                    'visited'   => false
                ];
            }else{

                $found = false;

                foreach( $arr as $item )
                {
                    if( $link == $item['link']) {
                        $found = true;
                    }
                }

                if(!$found){
                    $arr[] = [
                        'link'      => $link,
                        'visited'   => false
                    ];
                }
            }
        }
    }

    /**
     * ==
     * @param string $url
     * @throws \Exception
     */
    public function scrapePage( $url = "" )
    {
        $html = $this->getPage($url);

        $int_links  = UrlService::getInternalLinks($html, $this->current_domain);

        $ext_links  = UrlService::getExternalLinks($html, $this->current_domain);

        $emails     = UrlService::getEmails($html);

        $this->addLink($int_links, true);

        $this->addLink($ext_links, false);

        $this->addEmails($emails);
    }
}