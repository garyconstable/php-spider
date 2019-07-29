<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Domains;

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
            foreach ($all_domains as $domain) {
                try {
                    $url = $domain->getUrl();
                    $domain = new Domains();
                    $domain->setDomain($url);
                    $this->em->persist($domain);
                    $this->em->flush();
                } catch (\Exception $ex) {
                }
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

====

select dom.domain from
(select
CONCAT(dp.prefix,'.',dn.name,'.',ds.suffix) as domain
from
domain_name dn left join domain_name_domain_prefix dnp on dn.id = dnp.domain_name_id
left join domain_prefix dp on dnp.`domain_prefix_id` = dp.id
left join domain_name_domain_suffix dns on dn.id = dns.`domain_name_id`
left join domain_suffix ds on dns.`domain_suffix_id` = ds.id
order by dn.name) as dom
group by dom.domain
order by dom.domain

===

insert into domains (domain)
select abc.domain from (select dom.domain from
(select
CONCAT(dp.prefix,'.',dn.name,'.',ds.suffix) as domain
from
domain_name dn left join domain_name_domain_prefix dnp on dn.id = dnp.domain_name_id
left join domain_prefix dp on dnp.`domain_prefix_id` = dp.id
left join domain_name_domain_suffix dns on dn.id = dns.`domain_name_id`
left join domain_suffix ds on dns.`domain_suffix_id` = ds.id
order by dn.name) as dom
group by dom.domain
order by dom.domain) as abc where domain is not null

*/