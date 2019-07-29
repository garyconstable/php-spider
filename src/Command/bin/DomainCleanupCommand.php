<?php

namespace App\Command\Bin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Process;
use Psr\Log\LoggerInterface;

class DomainCleanupCommand extends Command
{
    protected static $defaultName = 'spider:domain:cleanup';

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
    }

    /**
     * Remove any worker process that has been running for over 15 minutes.
     * ==
     */
    public function cleanupProcess()
    {
        $processes = $this->entityManager->getRepository('App:Process')->findBy(['worker_type' => 'domain_cleanup' ]);

        foreach ($processes as $process) {
            $date = $process->getDateAdd();
            $diff = $date->diff(new \Datetime('now'));

            if ($diff->h > 0 || $diff->i > 15) {
                $pid = $process->getPid();
                $command = "kill " . $pid ." > /dev/null 2>&1 & echo $!;";
                exec($command, $output);
                $this->entityManager->remove($process);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$this->cleanupProcess();
        //$this->execInitiator();
    }

    /**
     *
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
     * Start a new parent process
     * ==
     * @param string $worker_type
     * @return Process
     * @throws \Exception
     */
    public function startProcess($worker_type = '')
    {
        $parent_process = new Process();
        $parent_process->setWorkerType($worker_type);
        $parent_process->setWorkerKey($worker_type);
        $parent_process->setWorkerUrl($worker_type);
        $parent_process->setDateAdd(new \DateTime());
        return $parent_process;
    }

    /**
     * ==
     * @throws \Exception
     */
    public function execInitiator()
    {
        $dir =  rtrim(dirname(__DIR__, 2), '/') ;
        $command = "php " . $dir . "/bin/console spider:domains:convert-external > /dev/null 2>&1 & echo $!;";
        $pid = exec($command, $output);

        $parent_process = $this->startProcess('domain_cleanup');
        $parent_process->setPid($pid);
        $parent_process->setDateAdd(new \DateTime());
        $parent_process->setParentId(0);
        $this->entityManager->persist($parent_process);
        $this->entityManager->flush();
    }
}
