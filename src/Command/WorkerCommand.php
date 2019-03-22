<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Process\Process;



class WorkerCommand extends Command
{
    protected static $defaultName = 'spider:worker';

    private $em;

    private $container;

    private $max_workers = 3;


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
     * ==
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $running_processes = $this->getCurrentNumberOfWorkers();
        if($running_processes < $this->max_workers) {
            return $this->saveWorker( $this->execQueueWorker() );
        }
        return true;
    }

    /**
     * ==
     * @param int $pid
     * @return bool
     * @throws \Exception
     */
    public function saveWorker($pid = 0)
    {
        $proc = new \App\Entity\Process();
        $proc->setPid($pid);
        $proc->setDateAdd(new \DateTime());
        $this->em->persist($proc);
        $this->em->flush();
        return true;
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
     * Get number of running PID
     * ==
     * @return int
     */
    protected function getCurrentNumberOfWorkers()
    {
        $num = 0;
        $processes = $this->em->getRepository('App:Process')->findAll();
        if(empty($processes)){
            return 0;
        }
        foreach($processes as $process){

            $pid = $process->getPid();

            if( !$this->isRunning($pid))
            {
                $proc = $this->em->getRepository('App:Process')->find( $process->getID() );
                $this->em->remove($proc);
                $this->em->flush();

            }else{
                $num += 1;
            }
        }
        return $num;
    }

    /**
     * Run the queue worker
     * ==
     * @throws \Exception
     */
    public function execQueueWorker()
    {
        $dir =  rtrim(dirname(__DIR__, 2), '/') ;
        $command = "php " . $dir . "/bin/console spider:worker:queue > /dev/null 2>&1 & echo $!;";
        $pid = exec($command, $output);
        return $pid;
    }
}