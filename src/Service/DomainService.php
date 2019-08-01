<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\ExternalDomain;
use App\Entity\Process;
use App\Service\DomainWorkerPoolService;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class DomainService
{
    private $em;
    private $container;
    private $domainWorkerPool;
    private $maxWorkers = 20;
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
    public static function d($data = [], $die = true)
    {
        echo '<pre>' . print_r($data, true) . '</pre>';
        if ($die) {
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
        if ($initiator_id) {
            $this->initiator_id = $initiator_id;
        }

        echo '--> Start Domain Crawler: ' . PHP_EOL;
        $this->domainWorkerPool = new DomainWorkerPoolService($this->maxWorkers);
        $this->flushWorkers();
        $this->crawlDomain();
        sleep($this->maxWorkers);
        $this->flushWorkers();
        echo '--> End Domain Crawler: ' . PHP_EOL;
    }

    /**
     * Recursive Crawl Domains until Complete
     * ==
     */
    public function crawlDomain()
    {
        $worker = $this->domainWorkerPool->get();
        if ($worker) {
            $domain = $this->em->getRepository('App:ExternalDomain')->findOneBy(['visited' => false], ['id' => 'asc']);
            if (!$domain) {
                return;
            }

            $domain->setVisited(true);
            $this->em->persist($domain);
            $this->em->flush();

            $pid = $worker->run($domain->getUrl());
            $key = spl_object_hash($worker);

            if ($this->initiator_id == 0) {
                $this->assignInitiatorId();
            }

            $process = new Process();
            $process->setParentId($this->initiator_id);
            $process->setPid($pid);
            $process->setWorkerKey($key);
            $process->setWorkerType($this->workerType);
            $process->setWorkerUrl($domain->getUrl());
            $process->setDateAdd(new \DateTime());

            $this->em->persist($process);
            $this->em->flush();
        }

        $this->flushWorkers();
        \sleep(1);
        $this->crawlDomain();
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
            if (count(preg_split("/\n/", $result)) > 2) {
                return true;
            }
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * Remove any dead processes
     * ==
     * @return bool
     */
    public function flushWorkers()
    {
        $processes = $this->em->getRepository('App:Process')->findAll();
        if (empty($processes)) {
            return false;
        }
        foreach ($processes as $process) {
            $pid = $process->getPid();
            if (!$this->isRunning($pid)) {
                $key = $process->getWorkerKey();
                $this->em->remove($process);
                $this->em->flush();
                $this->domainWorkerPool->disposeByKey($key);
            }
        }
    }

    /**
     * Assign the parent ID to the process
     * ==
     */
    public function assignInitiatorId()
    {
        $inits = $this->em->getRepository('App:Process')->findBy(['worker_type' => 'domain_initiator']);
        foreach ($inits as $i) {
            $this->initiator_id = $i->getId();
        }
    }

    /**
     * Get the domains row count - cached
     * ==
     * @return mixed|\Symfony\Component\Cache\CacheItem
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getDomainCount()
    {
        $cache = new FilesystemAdapter();
        $domainCount = $cache->getItem('domain.count');
        if (!$domainCount->isHit()) {
            $this->setDomainCount();
            $domainCount = $cache->getItem('domain.count');
        }
        return isset($domainCount->get()[0]['total']) ? $domainCount->get()[0]['total'] : 0;
    }

    /**
     * Set the domain count
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function setDomainCount()
    {
        $cache = new FilesystemAdapter();
        $domainCount = $cache->getItem('domain.count');
        $domainCount->set($this->em->getRepository('App:Domains')->tableSize());
        $domainCount->expiresAfter(93600);
        $cache->save($domainCount);
    }
}