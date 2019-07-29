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
     * DomainFactory constructor.
     *
     * @param null $entityManager
     */
    public function __construct($entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Set the prefix
     *
     * @param string $prefix
     */
    public function setPrefix($prefix = "")
    {
        $this->prefix = $prefix;
    }

    /**
     * Set the suffix
     *
     * @param string $suffix
     */
    public function setSuffix($suffix = "")
    {
        $this->suffix = $suffix;
    }

    /**
     * Suffix Exists
     *
     * @return mixed
     */
    public function suffixExists()
    {
        $result = $this->entityManager->getRepository('App:DomainSuffix')->findBy(
            ['suffix' => $this->suffix],
            ['id' => 'ASC']
        );
        return empty($result) ? false : $result;
    }

    /**
     * Prefix Exists
     *
     * @return mixed
     */
    public function prefixExists()
    {
        $result = $this->entityManager->getRepository('App:DomainPrefix')->findBy(
            ['prefix' => $this->prefix],
            ['id' => 'ASC']
        );
        return empty($result) ? false : $result;
    }

    /**
     * Name Exists
     *
     * @return mixed
     */
    public function nameExists()
    {
        $result = $this->entityManager->getRepository('App:DomainName')->findBy(
            ['name' => $this->name],
            ['id' => 'ASC']
        );
        return empty($result) ? false : $result;
    }

    /**
     * Get / create the suffix obj
     *
     * @return DomainSuffix
     */
    public function getSuffix()
    {
        try {
            $result = $this->suffixExists();
            if (!$result) {
                if ($this->suffix) {
                    $suffix = new DomainSuffix();
                    $suffix->setSuffix($this->suffix);
                    $this->entityManager->persist($suffix);
                    return $suffix;
                }
            }
            return isset($result[0]) ? $result[0] : false;
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
            $result = $this->prefixExists();
            if (!$result) {
                if ($this->prefix) {
                    $prefix = new DomainPrefix();
                    $prefix->setPrefix($this->prefix);
                    $this->entityManager->persist($prefix);
                    return $prefix;
                }
            }
            return isset($result[0]) ? $result[0] : false;
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
            $result = $this->nameExists();
            if (!$result) {
                if ($this->name) {
                    $domain = new DomainName();
                    $domain->setName($this->name);
                    $this->entityManager->persist($domain);
                    return $domain;
                }
            }
            return isset($result[0]) ? $result[0] : false;
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            die();
        }
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
            if ($domain) {
                $prefix = $this->getPrefix();
                if ($prefix) {
                    $domain->addPrefix($prefix);
                }

                $suffix = $this->getSuffix();
                if ($suffix) {
                    $domain->addSuffix($suffix);
                }
                
                $this->entityManager->persist($domain);
            }
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            die();
        }
    }
}
