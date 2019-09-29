<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomainsRepository")
 */
class Domains
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, length=255)
     */
    private $domain;

    /**
     * @ORM\Column(type="boolean", options={"unsigned":true, "default":0})
     */
    private $visited;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getVisited(): ?bool
    {
        return $this->visited;
    }

    public function setVisited(bool $visited): self
    {
        $this->visited = $visited;

        return $this;
    }
}
