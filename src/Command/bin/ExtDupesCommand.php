<?php

namespace App\Command\Bin;

use App\Entity\Log;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Process;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputArgument;

class ExtDupesCommand extends Command
{
    protected static $defaultName = 'spider:ext:dupes';

    private $domainsService;
    private $entityManager;
    private $container;
    private $logger;


    /**
     * Add settings here..
     * ==
     */
    protected function configure()
    {
        $this->addArgument('runner', InputArgument::REQUIRED, 'Should we run');
    }

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
    }

    /**
     * Exec
     * ==
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runner = $input->getArgument('runner');
        if ($runner) {
            $this->runner();
            //$this->logger = new $logger('spider:domain:crawl');
        }
    }

    /**
     * Debugger
     * ==
     * @param array $data
     * @param bool $die
     */
    public function d($data = [], $die = true)
    {
        echo '<pre>' . print_r($data, true) . '</pre>';
        if ($die) {
            die();
        }
    }

    /**
     * Runner
     * ==
     */
    public function runner()
    {
        $sql = 'select * from `external_domain` limit 100';
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $datas = $stmt->fetchAll();

        if (!empty($datas)) {
            foreach ($datas as $data) {
                $this->updateInsert($data);
            }
            $this->runner();
        } else {
            echo '---> complete.';
        }
    }

    /**
     * Update / insert
     * ==
     * @param array $item
     */
    public function updateInsert($item = [])
    {
        $parts = parse_url($item['url']);

        try {
            if (isset($parts['host']) && !empty($parts['host'])) {
                $sql = " select * from `external_domain_copy` where url = '".addslashes($parts['host'])."' ";
                $stmt = $this->entityManager->getConnection()->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetchAll();

                if (empty($data)) {
                    $sql = 'insert into `external_domain_copy` 
(`url`, `visited`, `date_add`, `scheme`) values ( "'.$parts['host'].'", 1, "'.$item['date_add'].'", "'.$parts['scheme'].'")';

                    $stmt = $this->entityManager->getConnection()->prepare($sql);
                    $stmt->execute();

                } else {
                    $scheme = $data[0]['scheme'] . '|' . $parts['scheme'];

                    $sql = 'update `external_domain_copy` set scheme = "'.$scheme.'" where url = "'.$parts['host'].'" ';
                    $stmt = $this->entityManager->getConnection()->prepare($sql);
                    $stmt->execute();
                }
            }
        } catch (\Exception $ex) {

        }

        $sql = " delete from `external_domain` where id = '".$item['id']."' ";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
    }
}
