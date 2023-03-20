<?php
namespace App\Entity;

use App\Entity\Trajets;
use App\Entity\Utilisateurs;
use App\Repository\AdopteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdopteRepository::class)]
#[ORM\Table(name:"adopte")]
#[ORM\UniqueConstraint(name: "utilisateur_trajet_unique", columns: ["utilisateur_id", "trajet_id"])]
class Adopte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable:false)]
    private $utilisateur;

    #[ORM\ManyToOne(targetEntity: Trajets::class)]
    #[ORM\JoinColumn(nullable:false)]
    private $trajet;

    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateurs $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getTrajet(): ?Trajets
    {
        return $this->trajet;
    }

    public function setTrajet(Trajets $trajet): self
    {
        $this->trajet = $trajet;

        return $this;
    }
}
?>