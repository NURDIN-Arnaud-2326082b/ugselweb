<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\CompetitionRepository;

#[ORM\Entity(repositoryClass: CompetitionRepository::class)]
#[ORM\Table(name: 'competition')]
class Competition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(targetEntity: Championnat::class, inversedBy: 'competitions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Championnat $championnat = null;

    #[ORM\OneToMany(targetEntity: Epreuve::class, mappedBy: 'competition', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $epreuves;

    public function __construct()
    {
        $this->epreuves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getChampionnat(): ?Championnat
    {
        return $this->championnat;
    }

    public function setChampionnat(?Championnat $championnat): self
    {
        $this->championnat = $championnat;
        return $this;
    }

    /**
     * @return Collection<int, Epreuve>
     */
    public function getEpreuves(): Collection
    {
        return $this->epreuves;
    }

    public function addEpreuve(Epreuve $epreuve): static
    {
        if (!$this->epreuves->contains($epreuve)) {
            $this->epreuves->add($epreuve);
            $epreuve->setCompetition($this);
        }

        return $this;
    }

    public function removeEpreuve(Epreuve $epreuve): static
    {
        if ($this->epreuves->removeElement($epreuve)) {
            // set the owning side to null (unless already changed)
            if ($epreuve->getCompetition() === $this) {
                $epreuve->setCompetition(null);
            }
        }

        return $this;
    }
}
