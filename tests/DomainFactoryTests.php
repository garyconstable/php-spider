<?php

namespace App\Tests;

use App\Utils\DomainFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

//https://symfony.com/doc/current/testing.html

class DomainFactoryTests extends WebTestCase
{
    /**
     * Should only be on suffix
     *
     * @test
     */
    public function suffixExists()
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $df = new DomainFactory($entityManager);
        $df->setSuffix('wiki');
        $suffixObjs = $df->suffixExists();
        var_dump($suffixObjs);
        $this->assertCount(1, $suffixObjs);
    }
}
