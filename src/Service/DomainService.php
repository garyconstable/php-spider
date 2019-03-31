<?php

namespace App\Service;
use Symfony\Component\DependencyInjection\ContainerInterface;

use App\Entity\ExternalDomain;
use App\Entity\Process;
use App\Service\DomainWorkerPoolService;


class DomainService
{
    private $em;
    private $container;
    private $domainWorkerPool;
    private $maxWorkers = 10;
    private $workerType = 'domain_worker';
    private $initiator_id = 0;

    /**
     * DomainService constructor.
     * ==
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * Debugger function
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
     * Initiate the crawler
     * ==
     * @param bool $initiator_id
     */
    public function startDomainCrawler($initiator_id = false)
    {
        if($initiator_id){
            $this->initiator_id = $initiator_id;
        }

        echo '--> Start Domain Crawler: '.PHP_EOL;
        $this->domainWorkerPool = new DomainWorkerPoolService($this->maxWorkers);
        $this->flushWorkers();
        $this->crawlDomain();
        sleep($this->maxWorkers);
        $this->flushWorkers();
        echo '--> End Domain Crawler: '.PHP_EOL;
    }

    /**
     * Recursive Crawl Domains until Complete
     * ==
     */
    public function crawlDomain()
    {
        $worker = $this->domainWorkerPool->get();
        if($worker)
        {
            $domain = $this->em->getRepository('App:ExternalDomain')->findOneBy( ['visited' => false], ['id' => 'asc'] );
            if(!$domain){return;}

            $domain->setVisited(true);
            $this->em->persist($domain);
            $this->em->flush();

            $pid = $this->exeCommand( $domain->getUrl() );
            $key = spl_object_hash($worker);

            if($this->initiator_id == 0){
                $this->assignInitiatorId();
            }

            $process = new Process();
            $process->setPid($pid);
            $process->setParentId($this->initiator_id);
            $process->setWorkerKey($key);
            $process->setWorkerType($this->workerType);
            $process->setWorkerUrl($domain->getUrl());
            $process->setDateAdd( new \DateTime() );
            $this->em->persist($process);
            $this->em->flush();

        }
        $this->flushWorkers();
        \sleep(1);
        $this->crawlDomain();
    }

    /**
     * Execute command and return PID
     * ==
     * @param string $url
     * @return string
     */
    public function exeCommand($url = '')
    {
        $dir =  rtrim(dirname(__DIR__, 2), '/') ;
        $command = "php " . $dir . "/bin/console spider:worker:domain ".$url." > /dev/null 2>&1 & echo $!;";
        $pid = exec($command, $output);
        return $pid;
    }


    /**
     * Is the PID running?
     * ==
     * @param $pid
     * @return bool
     */
    public function isRunning($pid)
    {
        try {
            $result = shell_exec(sprintf('ps %d', $pid));
            if(count(preg_split("/\n/", $result)) > 2) {
                return true;
            }
        } catch(Exception $e) {}

        return false;
    }

    /**
     * ==
     * @return bool
     */
    public function flushWorkers()
    {
        $processes = $this->em->getRepository('App:Process')->findAll();
        if(empty($processes)){
            return false;
        }
        foreach($processes as $process)
        {
            $pid = $process->getPid();
            if(!$this->isRunning($pid))
            {
                $key = $process->getWorkerKey();
                $this->em->remove($process);
                $this->em->flush();
                $this->domainWorkerPool->disposeByKey($key);
            }
        }
    }


    public function assignInitiatorId()
    {
        $inits = $this->em->getRepository('App:Process')->findBy( ['worker_type' => 'domain_initiator'] );
        foreach($inits as $i){
            $this->initiator_id = $i->getId();
        }
    }
}