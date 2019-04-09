<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Process;

class WorkerStopCommand extends Command
{
    protected static $defaultName = 'spider:worker:stop';

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
        $this->stopProcesses();
    }

    /**
     * Save the worker PID to the table
     * ==
     * @param int $pid
     * @throws \Exception
     */
    public function saveWorker($pid = 0)
    {
        $proc = new \App\Entity\Process();
        $proc->setPid($pid);
        $proc->setDateAdd(new \DateTime());
        $this->em->persist($proc);
        $this->em->flush();
    }

    /**
     * ==
     * @return bool
     */
    protected function stopProcesses()
    {
        $idx    = 0;
        $limit  = 1;
        $processes = $this->em->getRepository('App:Process')->findAll();
        if (empty($processes)) {
            return false;
        }
        foreach ($processes as $process) {
            if ($idx == $limit) {
                continue;
            }
            $pid = $process->getPid();
            $command = "kill " . $pid ." > /dev/null 2>&1 & echo $!;";
            exec($command, $output);
            $proc = $this->em->getRepository('App:Process')->find($process->getID());
            $this->em->remove($proc);
            $this->em->flush();
            $idx++;
        }
        return true;
    }
}