<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

//use Symfony\Component\Console\Input\InputArgument;
//use Symfony\Component\Console\Input\ArrayInput;
//use Symfony\Component\Console\Output\NullOutput;

use App\Entity\Process;


class DomainCrawlerCommand extends Command
{
    protected static $defaultName = 'spider:domain:crawl';

    private $domainsService;
    private $entityManager;
    private $container;
    private $mailer;

    /**
     * QueueWorkerCommand constructor.
     * ==
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->execInitiator();
    }

    /**
     *
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
     * @throws \Exception
     */
    public function execInitiator()
    {
        $this->sendEmail();

        $worker_type = 'domain_initiator';
        $processes = $this->entityManager->getRepository('App:Process')->findBy(['worker_type' => $worker_type ]);
        $parent_process = false;
        $can_start = false;
        $workers_running = 0;
        $parent_process_id = false;

        //get domain initiator
        foreach($processes as $process)
        {
            $pid = $process->getPid();
            if( $this->isRunning($pid)  ){
                $parent_process = $process;
                break;
            }
        }

        //get workers
        if($parent_process)
        {
            $parent_process_id = $parent_process->getId();
            $process_workers = $this->entityManager->getRepository('App:Process')->findBy(['parent_id' => $parent_process->getId()  ]);
            foreach($process_workers as $worker){
                $pid = $worker->getPid();
                if( $this->isRunning($pid)  ){
                    $workers_running += 1;
                }
            }
        }

        //if no workers
        if( $workers_running < 1){
            $can_start = true;
        }

        //can start no workers
        if($can_start)
        {
            $dir =  rtrim(dirname(__DIR__, 2), '/') ;
            $command = "php " . $dir . "/bin/console spider:domain:start ".$parent_process_id." > /dev/null 2>&1 & echo $!;";
            $pid = exec($command, $output);

            if(!$parent_process_id)
            {
                $process = new Process();
                $process->setPid($pid);
                $process->setWorkerType($worker_type);
                $process->setDateAdd( new \DateTime() );

                $this->entityManager->persist($process);
                $this->entityManager->flush();
            }
        }
    }


    public function sendEmail()
    {
        $msg = "Domain Crawler Start.";
        $msg = wordwrap($msg,70);
        mail("garyconstable80@gmail.com","Ghostfrog - Spider status",$msg);
    }
}