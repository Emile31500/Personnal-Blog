<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $title;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $github_link;

    #[ORM\Column(type: "text")]
    private $content;

    #[ORM\Column(type: "boolean")]
    private $isPublished;

    #[ORM\OneToMany(targetEntity: ProjectMedia::class, mappedBy: "id_project")]
    private $media;

    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getGithubLink(): ?string
    {
        return $this->github_link;
    }

    public function setGithubLink(?string $github_link): self
    {
        $this->github_link = $github_link;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * @return Collection<int, ProjectMedia>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addNameMedium(ProjectMedia $nameMedium): self
    {
        if (!$this->media->contains($nameMedium)) {
            $this->media[] = $nameMedium;
            $nameMedium->setIdProject($this);
        }

        return $this;
    }

    public function removeNameMedium(ProjectMedia $nameMedium): self
    {
        if ($this->media->removeElement($nameMedium)) {
            // set the owning side to null (unless already changed)
            if ($nameMedium->getIdProject() === $this) {
                $nameMedium->setIdProject(null);
            }
        }

        return $this;
    }
}
