<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomainSuffixRepository")
 */
class DomainSuffix
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $suffix;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\DomainName", mappedBy="suffix")
     */
    private $domainNames;

    public function __construct()
    {
        $this->domainNames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    public function setSuffix(string $suffix): self
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return Collection|DomainName[]
     */
    public function getDomainNames(): Collection
    {
        return $this->domainNames;
    }

    public function addDomainName(DomainName $domainName): self
    {
        if (!$this->domainNames->contains($domainName)) {
            $this->domainNames[] = $domainName;
            $domainName->addSuffix($this);
        }

        return $this;
    }

    public function removeDomainName(DomainName $domainName): self
    {
        if ($this->domainNames->contains($domainName)) {
            $this->domainNames->removeElement($domainName);
            $domainName->removeSuffix($this);
        }

        return $this;
    }
}
