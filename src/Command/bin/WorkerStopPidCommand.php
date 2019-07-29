<?php

namespace App\Command\Bin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;

class WorkerStopPidCommand extends Command
{
    protected static $defaultName = 'spider:worker:stop_pid';

    private $em;

    private $container;

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
    protected function configure()
    {
        $this->addArgument('pid', InputArgument::REQUIRED, 'PID');
    }

    /**
     * Debugger
     * ==
     * @param array $data
     * @param bool $die
     */
    public function d($data = [], $die = true)
    {
        echo '<pre>'.print_r($data, true).'</pre>';
        if ($die) {
            die();
        }
    }

    /**
     * ==
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pid = $input->getArgument('pid');
        $this->stopProcesses($pid);
    }

    /**
     * ==
     * @param int $pid
     */
    protected function stopProcesses($pid = 0)
    {
        $command = "kill " . $pid ." > /dev/null 2>&1 & echo $!;";
        exec($command, $output);
        $proc = $this->em->getRepository('App:Process')->findBy(['pid' => $pid ]);
        foreach ($proc as $p) {
            $this->em->remove($p);
            $this->em->flush();
        }
    }
}