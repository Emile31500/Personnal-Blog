<?php

namespace App\Entity;

use App\Repository\ProjectMediaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectMediaRepository::class)]
class ProjectMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['project'])]
    private $id;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: "name")]
    private $project;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(['project'])]
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProject(): ?Project
    {
        return $this->project;
    }

    public function setIdProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
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
}
