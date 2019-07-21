<?php

namespace App\Utils;

interface DomainFactoryInterface
{
    public function getMaterials();

    public function extract($domain);
}
