<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

use App\Entity\Pending;
use App\Service\UrlService;

class DomainWorkerCommand extends Command
{
    protected static $defaultName = 'spider:worker:domain';

    private $em;

    private $container;

    private $pending_path = "";

    private $internal_links = [];

    private $external_links = [];

    private $current_domain = "";

    /**
     * QueueWorkerCommand constructor.
     * ==
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->pending_path = rtrim(dirname(__DIR__, 2), '/') . '/data/pending/';
    }

    /**
     * Add settings here..
     * ==
     */
    protected function configure()
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'The Seed URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        $this->current_domain  = UrlService::getDomain($url);

        $this->scrapePage($url);

        $this->linkRunner();

        $this->d([
            $this->internal_links,
            $this->external_links
        ]);
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

        if( FALSE !== $url ){

            $this->scrapePage($url);

            /*
            $this->d([
                $this->internal_links,
                $this->external_links
            ], 0);
            */
            
            $this->linkRunner();
        }
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

        $int_links = UrlService::getInternalLinks($html, $this->current_domain);

        $ext_links = UrlService::getExternalLinks($html, $this->current_domain);

        $this->addLink($int_links, true);

        $this->addLink($ext_links, false);
    }
}