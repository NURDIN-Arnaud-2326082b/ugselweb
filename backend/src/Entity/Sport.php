<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SportRepository;

#[ORM\Entity(repositoryClass: SportRepository::class)]
#[ORM\Table(name: 'sport')]
class Sport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $type = null; // 'individuel' ou 'collectif'

    /**
     * @var Collection<int, Championnat>
     */
    #[ORM\OneToMany(targetEntity: Championnat::class, mappedBy: 'sport', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $championnats;

    public function __construct()
    {
        $this->championnats = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, ['individuel', 'collectif'])) {
            throw new \InvalidArgumentException("Le type doit être 'individuel' ou 'collectif'");
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return Collection<int, Championnat>
     */
    public function getChampionnats(): Collection
    {
        return $this->championnats;
    }

    public function addChampionnat(Championnat $championnat): self
    {
        if (!$this->championnats->contains($championnat)) {
            $this->championnats->add($championnat);
            $championnat->setSport($this);
        }

        return $this;
    }

    public function removeChampionnat(Championnat $championnat): self
    {
        if ($this->championnats->removeElement($championnat)) {
            // set the owning side to null (unless already changed)
            if ($championnat->getSport() === $this) {
                $championnat->setSport(null);
            }
        }

        return $this;
    }
}
