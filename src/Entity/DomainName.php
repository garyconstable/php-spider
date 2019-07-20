<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomainNameRepository")
 */
class DomainName
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
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\DomainPrefix", inversedBy="suffix")
     */
    private $prefix;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\DomainSuffix", inversedBy="domainNames")
     */
    private $suffix;

    public function __construct()
    {
        $this->prefix = new ArrayCollection();
        $this->suffix = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|DomainPrefix[]
     */
    public function getPrefix(): Collection
    {
        return $this->prefix;
    }

    public function addPrefix(DomainPrefix $prefix): self
    {
        if (!$this->prefix->contains($prefix)) {
            $this->prefix[] = $prefix;
        }

        return $this;
    }

    public function removePrefix(DomainPrefix $prefix): self
    {
        if ($this->prefix->contains($prefix)) {
            $this->prefix->removeElement($prefix);
        }

        return $this;
    }

    /**
     * @return Collection|DomainSuffix[]
     */
    public function getSuffix(): Collection
    {
        return $this->suffix;
    }

    public function addSuffix(DomainSuffix $suffix): self
    {
        if (!$this->suffix->contains($suffix)) {
            $this->suffix[] = $suffix;
        }

        return $this;
    }

    public function removeSuffix(DomainSuffix $suffix): self
    {
        if ($this->suffix->contains($suffix)) {
            $this->suffix->removeElement($suffix);
        }

        return $this;
    }
}
