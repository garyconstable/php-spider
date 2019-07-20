<?php

namespace App\Utils;

use App\Utils\DomainAbstract;
use App\Utils\DomainFactoryInterface;

class DomainsEntityAdapter extends DomainAbstract implements DomainFactoryInterface
{
    private $name = "";
    private $prefix = "";
    private $suffix = "";

    public function __construct($domain)
    {
        $this->extract($domain);
    }

    public function extract($domain)
    {
        $result = tld_extract($domain->getDomain());

        $this->prefix = $result->getSubdomain();
        $this->name = $result->getHostname();
        $this->suffix = $result->getSuffix();
    }

    public function getMaterials()
    {
        return [
            'name' => $this->name,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
        ];
    }
}
