<?php

namespace App\Entity;

use App\Repository\ProjectMediaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectMediaRepository::class)
 */
class ProjectMedia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="nameMedia")
     */
    private $id_project;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameMedia;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProject(): ?Project
    {
        return $this->id_project;
    }

    public function setIdProject(?Project $id_project): self
    {
        $this->id_project = $id_project;

        return $this;
    }

    public function getNameMedia(): ?string
    {
        return $this->nameMedia;
    }

    public function setNameMedia(string $nameMedia): self
    {
        $this->nameMedia = $nameMedia;

        return $this;
    }
}
