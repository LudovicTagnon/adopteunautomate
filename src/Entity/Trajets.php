<?php

namespace App\Entity;

use App\Repository\TrajetsRepository;
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
}
