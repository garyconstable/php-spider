<?php

namespace App\Command;

use App\Entity\Domains;
use App\Entity\Queue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

use App\Entity\Pending;
use App\Service\UrlService;


class PageWorkerCommand extends Command
{
    protected static $defaultName = 'spider:worker:page';

    private $batch = 1;

    private $em;

    private $container;

    private $pending_path = "/Users/garyconstable/Desktop/code/vhosts/spider/data/pending/";

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
    }

    /**
     * Add settings here..
     * ==
     */
    protected function configure(){}

    /**
     * Command Entry Point
     * ==
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->queueRunner(1);
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
     * Get the HTML
     * ==
     * @param array $item
     * @return bool|false|string
     */
    public function getPage( $item = [] )
    {
        try {
            $filepath = $this->pending_path . $item['filename'];
            $page = \file_get_contents($filepath);
            if ( \strlen($page) > 0     ) {
                unlink($filepath);
                return $page;
            }
            return false;
        }catch( \Exception $ex ){
            return false;
        }
    }

    /**
     * Get the next batch from the Database
     * ==
     * @return array
     */
    public function build_queue()
    {
        $queue = [];
        $batch = $this->em->getRepository('App:Pending')->getBatch($this->batch);

        foreach($batch as $item){
            $queue[] = [
                'id'        => $item->getId(),
                'url'       => $item->getUrl(),
                'filename'  => $item->getFilename()
            ];
            $this->em->remove($item);
            $this->em->flush();
        }

        return $queue;
    }

    /**
     * Save the page and reference to file / url.
     * ==
     * @param string $page
     * @param string $url
     * @return bool
     * @throws \Exception
     */
    public function save_page($page = "", $url = "")
    {
        $milliseconds = round(microtime(true) * 1000);
        $file = $milliseconds . ".txt";
        $filename = $this->pending_path . $file;
        if( file_put_contents($filename, $page) )
        {
            $pending = new Pending();
            $pending->setFilename($file);
            $pending->setUrl($url);
            $pending->setDateAdd( new \DateTime() );
            $this->em->persist($pending);
            $this->em->flush();
            return true;
        }
        return false;
    }

    /**
     * ==
     * @param string $html
     * @return array|\DOMNodeList
     */
    public function getExternalLinks($html = "")
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
                    $ret[] = $href;
                }
            }
        }
        return $ret;
    }

    /**
     * ==
     * @param string $link
     */
    public function addDomain( $link = "" )
    {
        $domain_name = UrlService::getDomain($link);

        if(!\is_null($domain_name )){
            try {
                $domain = new Domains();
                $domain->setDomain($domain_name);
                $this->em->persist($domain);
                $this->em->flush();
            }catch (UniqueConstraintViolationException $e) {
                $this->em = $this->container->get('doctrine')->resetManager();
            }
        }
    }

    /**
     * Add to the queue
     * ==
     * @param string $link
     */
    public function addQueue($link = "")
    {
        try{
            $queue = new Queue();
            $queue->setUrl($link);
            $this->em->persist($queue);
            $this->em->flush();
        }catch (UniqueConstraintViolationException $e) {
            $this->em = $this->container->get('doctrine')->resetManager();
        }
    }

    /**
     * ==
     * @param int $attempt
     */
    public function process_queue( $attempt = 0 )
    {
        $pending = $this->build_queue();

        foreach($pending as $item)
        {
            $page = $this->getPage($item);

            if($page)
            {
                $externalLinks = $this->getExternalLinks($page);

                foreach($externalLinks as $link)
                {
                    $this->addQueue($link);

                    $this->addDomain($link);
                }
            }
        }
    }

    /**
     * Get the queue length
     * ==
     * @return int
     */
    public function get_queue_length()
    {
        $all = $this->em->getRepository('App:Pending')->tableSize();
        return isset($all[0]['total']) ? $all[0]['total'] : 0;
    }

    /**
     * Queue Runner
     * ==
     * @param int $attempt
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function queueRunner( $attempt = 0 )
    {
        print("[+] Run Batch: " . $attempt) . PHP_EOL;

        $queue_size = $this->get_queue_length();
        if($queue_size > 0){
            $this->process_queue($attempt);
            $attempt++;
            $queue_size = $this->get_queue_length();
            if($queue_size > 0) {
                $this->queueRunner($attempt);
            }
        }
    }
}