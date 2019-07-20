<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use App\Utils\DomainsEntityAdapter;
use App\Utils\DomainFactory;

class ConvertDomainsCommand extends Command
{
    protected static $defaultName = 'spider:domains:convert';

    private $entityManager;
    private $container;

    /**
     * ConvertDomainsCommand constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(
        ContainerInterface $container
    ) {
        parent::__construct();
        $this->container = $container;
        $this->entityManager = $this->container->get('doctrine')->getManager();
    }

    /**
     * Die()
     *
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
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->runner();
    }

    public function runner()
    {
        $all_domains = $this->entityManager->getRepository('App:Domains')->findBy([], ['id' => 'desc'], 1000);

        if (!empty($all_domains)) {
            $factory = new DomainFactory($this->entityManager);
            foreach ($all_domains as $domain) {
                $adapter = new DomainsEntityAdapter($domain);
                $this->d($adapter, false);
                $factory->create($adapter->getMaterials());
                $this->entityManager->remove($domain);
            }

            $this->entityManager->flush();
            $this->runner();

        } else {
            exit();
        }
    }
}
