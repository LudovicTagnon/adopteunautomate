<?php

namespace App\Entity;

use App\Entity\Utilisateurs;
use App\Repository\GroupesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupesRepository::class)]
class Groupes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    private Utilisateurs $createur;

    #[ORM\Column(length: 50)]
    private ?string $nom_groupe = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'groupes', targetEntity: EstDans::class)]
    protected $estDans;

    #[ORM\ManyToOne(inversedBy: 'groupes')]
    private ?Trajets $trajets = null;

    public function __construct()
    {
        $this->estDans = new ArrayCollection();;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreateur(): ?int
    {
        return $this->createur->getId();
    }

    public function setCreateur(Utilisateurs $id_Createur): void
    {
        $this->createur = $id_Createur;
    }

    public function getNomGroupe(): ?string
    {
        return $this->nom_groupe;
    }

    public function setNomGroupe(string $nom_groupe): self
    {
        $this->nom_groupe = $nom_groupe;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
    
    public function getUtilisateurs(): Collection
    {
        return $this->estDans;
    }

    public function getNbUtilisateurs(): int
    {
        return count($this->estDans);
    }

    public function estDansGroupes(int $userId): bool
{
    foreach ($this->estDans as $estDans) {
        if ($estDans->getIdUtilisateur() == $userId) {
            return true;
        }
    }
    return false;
}

    public function getTrajets(): ?Trajets
    {
        return $this->trajets;
    }

    public function setTrajets(?Trajets $trajets): self
    {
        $this->trajets = $trajets;

        return $this;
    }

}
