<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use App\Entity\Pending;


class QueueWorkerCommand extends Command
{
    protected static $defaultName = 'spider:worker:queue';

    private $batch = 60;

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
     * Get the page
     * ==
     * @param string $url
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPage($url = "")
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $url, [
                'timeout'         => 1,
                'connect_timeout' => 1,
                'headers' => [
                    'User-Agent' => "Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.27 Safari/537.17 "
                ]
            ]);
            if( $response->getStatusCode() == 200 ){
                return $response->getBody()->getContents();
            }
            return false;
        }catch(\Exception $ex){
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
        $batch = $this->em->getRepository('App:Queue')->getBatch($this->batch);
        foreach($batch as $item){
            $queue[] = [
                'id'    => $item->getId(),
                'url'   => $item->getUrl()
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
     * Process the batch / queue
     * ==
     * @param int $attempt
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function process_queue( $attempt = 0)
    {
        $pending = $this->build_queue();
        $index = 0;
        foreach($pending as $item)
        {
            $page = $this->getPage($item['url']);
            if($page){
                if( $this->save_page( $page, $item['url'] ) ){
                    $index++;
                    echo ("[+] Batch: "  . $attempt . ", Page " . $index . ", Url: " . $item['url']) . PHP_EOL;
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
        $all = $this->em->getRepository('App:Queue')->tableSize();
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