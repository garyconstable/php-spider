<?php

namespace App\Command;

use PHPUnit\Runner\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Domains;
//use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class ConvertExternalDomainCommand extends Command
{
    protected static $defaultName = 'spider:domains:convert-external';

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
        echo '<pre>' . print_r($data, true) . '</pre>' . PHP_EOL;
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

    /**
     * Stuffs...
     * --
     */
    public function runner()
    {
        $all_domains = $this->entityManager->getRepository('App:ExternalDomain')->findBy([], ['id' => 'desc'], 10);
        if (!empty($all_domains)) {
            foreach ($all_domains as $domain) {


                $url = $domain->getUrl();
                $this->entityManager->remove($domain);


                $dm = new Domains();
                $dm->setDomain($url);


                try {
                    $this->entityManager->persist($domain);
                } catch (UniqueConstraintViolationException $e) {
                    $this->entityManager = $this->container->get('doctrine')->resetManager();
                }

                $this->entityManager->flush();
                $this->entityManager->clear();

            }

            $this->entityManager->flush();
            $this->entityManager->clear();
            $this->runner();
        } else {
            $this->entityManager->flush();
            $this->entityManager->clear();
            exit();
        }
    }
}
