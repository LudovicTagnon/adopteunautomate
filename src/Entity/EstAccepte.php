<?php
namespace App\Entity;

use App\Repository\EstAccepteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstAccepteRepository::class)]
class EstAccepte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Utilisateurs $utilisateur = null;

    #[ORM\ManyToOne]
    private ?Trajets $trajet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateurs $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getTrajet(): ?Trajets
    {
        return $this->trajet;
    }

    public function setTrajet(?Trajets $trajet): self
    {
        $this->trajet = $trajet;

        return $this;
    }
}
