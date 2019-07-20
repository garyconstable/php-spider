<?php

namespace App\Utils;

use App\Entity\DomainName;
use App\Entity\DomainSuffix;
use App\Entity\DomainPrefix;

class DomainFactory
{
    private $prefix;
    private $suffix;
    private $name;
    private $entityManager;

    /**
     * Get / create the suffix obj
     *
     * @return DomainSuffix
     */
    public function getSuffix()
    {
        try {
            if ($result = $this->entityManager->getRepository('App:DomainSuffix')->findOneBy(['suffix' => $this->suffix])) {
                return $result;
            }
            if ($this->suffix) {
                $suffix = new DomainSuffix();
                $suffix->setSuffix($this->suffix);
                $this->entityManager->persist($suffix);
                return $suffix;
            }
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            die();
        }
        return false;
    }

    /**
     * Get / Create the prefix obj
     *
     * @return DomainPrefix
     */
    public function getPrefix()
    {
        try {
            if ($result = $this->entityManager->getRepository('App:DomainPrefix')->findOneBy(['prefix' => $this->prefix])) {
                return $result;
            }
            if ($this->prefix) {
                $prefix = new DomainPrefix();
                $prefix->setPrefix($this->prefix);
                $this->entityManager->persist($prefix);
                return $prefix;
            }
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            die();
        }
        return false;
    }

    /**
     * Get / Create Domain name object
     *
     * @return DomainName
     */
    public function getDomain()
    {
        try {
            if ($result = $this->entityManager->getRepository('App:DomainName')->findOneBy(['name' => $this->name])) {
                return $result;
            }
            $domain = new DomainName();
            $domain->setName($this->name);
            $this->entityManager->persist($domain);
            return $domain;
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            die();
        }
    }

    /**
     * DomainFactory constructor.
     *
     * @param null $entityManager
     */
    public function __construct($entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Create
     *
     * @param array $materials
     */
    public function create($materials = [])
    {
        try {
            foreach ($materials as $key => $value) {
                $this->{$key} = $value;
            }
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            die();
        }

        try {
            $domain = $this->getDomain();
            if ($prefix = $this->getPrefix()) {
                $domain->addPrefix($prefix);
            }
            if ($suffix = $this->getSuffix()) {
                $domain->addSuffix($suffix);
            }
            $this->entityManager->persist($domain);
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            die();
        }
    }
}
