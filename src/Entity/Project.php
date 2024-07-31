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

    public function isPublished(): ?bool
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

    public function addProjectMedia(ProjectMedia $projectMedia): self
    {
        if (!$this->media->contains($projectMedia)) {
            $this->media[] = $projectMedia;
            $projectMedia->setIdProject($this);
        }

        return $this;
    }

    public function removeProjectMedia(ProjectMedia $projectMedia): self
    {
        if ($this->media->removeElement($projectMedia)) {
            // set the owning side to null (unless already changed)
            if ($projectMedia->getIdProject() === $this) {
                $projectMedia->setIdProject(null);
            }
        }

        return $this;
    }
}
