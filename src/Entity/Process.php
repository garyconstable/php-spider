<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProcessRepository")
 */
class Process
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $parent_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $pid;

    /**
     * @ORM\Column(type="string")
     */
    private $worker_key;

    /**
     * @ORM\Column(type="string")
     */
    private $worker_type;

    /**
     * @ORM\Column(type="string")
     */
    private $worker_url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_add;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPid(): ?int
    {
        return $this->pid;
    }

    public function setPid(int $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function setParentId(int $pid): self
    {
        $this->parent_id = $pid;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->date_add;
    }

    public function setDateAdd(\DateTimeInterface $date_add): self
    {
        $this->date_add = $date_add;

        return $this;
    }

    public function getWorkerKey(): ?string
    {
        return $this->worker_key;
    }

    public function setWorkerKey(string $key): self
    {
        $this->worker_key = $key;

        return $this;
    }

    public function getWorkerType(): ?string
    {
        return $this->worker_type;
    }

    public function setWorkerType(string $worker_type): self
    {
        $this->worker_type = $worker_type;

        return $this;
    }

    public function getWorkerUrl(): ?string
    {
        return $this->worker_url;
    }

    public function setWorkerUrl(string $worker_url): self
    {
        $this->worker_url = $worker_url;

        return $this;
    }
}
