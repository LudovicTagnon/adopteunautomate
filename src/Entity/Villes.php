<?php

namespace App\Entity;

use App\Repository\VillesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VillesRepository::class)]
class Villes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom_ville = null;

    #[ORM\Column(nullable: true)]
    private ?int $CP = null;

    #[ORM\OneToOne(mappedBy: 'demarre_de', cascade: ['persist', 'remove'])]
    private ?Trajets $depart_de = null;

    #[ORM\OneToOne(mappedBy: 'arrive_a', cascade: ['persist', 'remove'])]
    private ?Trajets $arrivee_de = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomVille(): ?string
    {
        return $this->nom_ville;
    }

    public function setNomVille(string $nom_ville): self
    {
        $this->nom_ville = $nom_ville;

        return $this;
    }

    public function getCP(): ?int
    {
        return $this->CP;
    }

    public function setCP(?int $CP): self
    {
        $this->CP = $CP;

        return $this;
    }

    public function getDepartDe(): ?Trajets
    {
        return $this->depart_de;
    }

    public function setDepartDe(Trajets $depart_de): self
    {
        // set the owning side of the relation if necessary
        if ($depart_de->getDemarreDe() !== $this) {
            $depart_de->setDemarreDe($this);
        }

        $this->depart_de = $depart_de;

        return $this;
    }

    public function getArriveeDe(): ?Trajets
    {
        return $this->arrivee_de;
    }

    public function setArriveeDe(Trajets $arrivee_de): self
    {
        // set the owning side of the relation if necessary
        if ($arrivee_de->getArriveA() !== $this) {
            $arrivee_de->setArriveA($this);
        }

        $this->arrivee_de = $arrivee_de;

        return $this;
    }

}
