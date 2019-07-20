<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomainPrefixRepository")
 */
class DomainPrefix
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
    private $prefix;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\DomainName", mappedBy="prefix")
     */
    private $suffix;

    public function __construct()
    {
        $this->suffix = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return Collection|DomainName[]
     */
    public function getSuffix(): Collection
    {
        return $this->suffix;
    }

    public function addSuffix(DomainName $suffix): self
    {
        if (!$this->suffix->contains($suffix)) {
            $this->suffix[] = $suffix;
            $suffix->addPrefix($this);
        }

        return $this;
    }

    public function removeSuffix(DomainName $suffix): self
    {
        if ($this->suffix->contains($suffix)) {
            $this->suffix->removeElement($suffix);
            $suffix->removePrefix($this);
        }

        return $this;
    }
}
