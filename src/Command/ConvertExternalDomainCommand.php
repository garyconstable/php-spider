<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use App\Utils\ExternalDomainsEntityAdapter;
use App\Utils\DomainFactory;

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
        $all_domains = $this->entityManager->getRepository('App:ExternalDomain')->findBy([], ['id' => 'desc'], 10);

        if (!empty($all_domains)) {
            $factory = new DomainFactory($this->entityManager);
            foreach ($all_domains as $domain) {
                $adapter = new ExternalDomainsEntityAdapter($domain);
                $factory->create($adapter->getMaterials());
                $this->entityManager->remove($domain);
                $this->entityManager->flush();
            }

            $this->runner();

        } else {
            exit();
        }
    }
}


/*

select
dp.prefix,
dn.name,
ds.suffix
from

domain_name dn left join domain_name_domain_prefix dnp on dn.id = dnp.domain_name_id
left join domain_prefix dp on dnp.`domain_prefix_id` = dp.id

left join domain_name_domain_suffix dns on dn.id = dns.`domain_name_id`
left join domain_suffix ds on dns.`domain_suffix_id` = ds.id

order by dn.name

*/