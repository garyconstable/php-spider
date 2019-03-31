<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Service\UrlService;
use App\Entity\ExternalDomain;




class DomainWorkerCommand extends Command
{
    protected static $defaultName = 'spider:worker:domain';

    private $em;

    private $container;

    private $internal_links = [];

    private $external_links = [];

    private $current_domain = "";

    private $max_pages_crawled = 100;

    /**
     * ==
     * DomainWorkerCommand constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
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
     * Add settings here..
     * ==
     */
    protected function configure()
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'The Seed URL');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        $this->current_domain = UrlService::getDomain($url);

        $this->scrapePage($url);

        $this->linkRunner();

        $this->saveExternalLinks();

    }

    /**
     * Get the next Internal Link
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
     * Itterate through the links
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
     * Add the link to the array(s)
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
     * Scrape the page
     * ==
     * @param string $url
     * @throws \Exception
     */
    public function scrapePage( $url = "" )
    {
        $html = $this->getPage($url);

        $int_links = UrlService::getInternalLinks($html, $this->current_domain);

        $ext_links = UrlService::getExternalLinks($html, $this->current_domain);

        $this->addLink($int_links, true);

        $this->addLink($ext_links, false);
    }

    /**
     * Add the Domain to the Database
     * ==
     * @param string $domain_name
     */
    public function addDomain( $domain_name = "" )
    {

        if(!\is_null($domain_name) && $domain_name ){
            try {
                $domain = new ExternalDomain();
                $domain->setUrl($domain_name);
                $domain->setVisited(false);
                $domain->setDateAdd( new \DateTime() );
                $this->em->persist($domain);
                $this->em->flush();
            }catch (UniqueConstraintViolationException $e) {
                $this->em = $this->container->get('doctrine')->resetManager();
            }catch(\Exception $ex){

            }
        }
    }

    /**
     * Save the links
     * ==
     */
    public function saveExternalLinks()
    {
        foreach( $this->external_links as $link )
        {
            $this->addDomain($link['link']);
        }
    }
}
