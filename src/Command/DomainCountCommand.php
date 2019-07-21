<?php

namespace App\Command;

use App\Entity\Log;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Process;
use Psr\Log\LoggerInterface;

class DomainCountCommand extends Command
{
    protected static $defaultName = 'spider:domain:count';

    private $domainsService;
    private $entityManager;
    private $container;
    private $logger;

    /**
     * ==
     * DomainCrawlerCommand constructor.
     * @param ContainerInterface $container
     * @param \App\Service\DomainService $ds
     * @param LoggerInterface $logger
     */
    public function __construct(
        ContainerInterface $container,
        \App\Service\DomainService $ds,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->container = $container;
        $this->entityManager = $this->container->get('doctrine')->getManager();
        $this->domainsService = $ds;
        $this->logger = new $logger('spider:domain:crawl');
    }

    /**
     * Get the Domain Count
     * ==
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|mixed|\Symfony\Component\Cache\CacheItem|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->domainsService->setDomainCount();
        exit();
    }
}