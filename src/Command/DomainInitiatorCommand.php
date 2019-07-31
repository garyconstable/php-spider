<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use App\Service\DomainService;

class DomainInitiatorCommand extends Command
{
    protected static $defaultName = 'spider:domain:start';

    private $domainsService;
    private $entityManager;
    private $container;

    /**
     * Add settings here,
     */
    protected function configure()
    {
        $this->addArgument('initiator_process_id', InputArgument::OPTIONAL, 'The initiator_process_id');
    }

    /**
     * QueueWorkerCommand constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, \App\Service\DomainService $ds)
    {
        parent::__construct();
        $this->container = $container;
        $this->entityManager = $this->container->get('doctrine')->getManager();
        $this->domainsService = $ds;
    }

    /**
     * Execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $param = 0;
        $initiator_id = $input->getArgument('initiator_process_id');
        if ($initiator_id) {
            $param = $initiator_id;
        }
        $this->domainsService->startDomainCrawler($param);
    }
}
