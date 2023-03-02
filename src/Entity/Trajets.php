<?php

namespace App\Entity;

use App\Repository\TrajetsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrajetsRepository::class)]
class Trajets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $etat = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $T_depart = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $T_arrivee = null;

    #[ORM\Column(nullable: true)]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $nb_passager_max = null;

    #[ORM\Column]
    private ?int $nb_passager_courant = null;

    #[ORM\Column]
    private ?bool $public = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $renseignement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTDepart(): ?\DateTimeImmutable
    {
        return $this->T_depart;
    }

    public function setTDepart(\DateTimeImmutable $T_depart): self
    {
        $this->T_depart = $T_depart;

        return $this;
    }

    public function getTArrivee(): ?\DateTimeImmutable
    {
        return $this->T_arrivee;
    }

    public function setTArrivee(?\DateTimeImmutable $T_arrivee): self
    {
        $this->T_arrivee = $T_arrivee;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getNbPassagerMax(): ?int
    {
        return $this->nb_passager_max;
    }

    public function setNbPassagerMax(int $nb_passager_max): self
    {
        $this->nb_passager_max = $nb_passager_max;

        return $this;
    }

    public function getNbPassagerCourant(): ?int
    {
        return $this->nb_passager_courant;
    }

    public function setNbPassagerCourant(int $nb_passager_courant): self
    {
        $this->nb_passager_courant = $nb_passager_courant;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getRenseignement(): ?string
    {
        return $this->renseignement;
    }

    public function setRenseignement(?string $renseignement): self
    {
        $this->renseignement = $renseignement;

        return $this;
    }
}
