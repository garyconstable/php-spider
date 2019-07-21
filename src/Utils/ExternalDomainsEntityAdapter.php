<?php

namespace App\Utils;

use App\Utils\DomainAbstract;
use App\Utils\DomainFactoryInterface;

class ExternalDomainsEntityAdapter extends DomainAbstract implements DomainFactoryInterface
{
    public function extract($domain)
    {
        $this->parts = tld_extract($domain->getUrl());
    }
}
