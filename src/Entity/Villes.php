<?php

namespace App\Entity;

use App\Repository\VillesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormTypeInterface;

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

    #[ORM\OneToMany(mappedBy: 'demarre_a', targetEntity: Trajets::class)]
    private Collection $demarrant;

    #[ORM\OneToMany(mappedBy: 'arrive_a', targetEntity: Trajets::class)]
    private Collection $arrivant;

    public function __construct()
    {
        $this->demarrant = new ArrayCollection();
        $this->arrivant = new ArrayCollection();
    }

    //#[ORM\OneToOne(mappedBy: 'demarre_de', cascade: ['persist', 'remove'])]
    //private ?Trajets $depart_de = null;

    //#[ORM\OneToOne(mappedBy: 'arrive_a', cascade: ['persist', 'remove'])]
    //private ?Trajets $arrivee_de = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getnom_ville(): ?string
    {
        return $this->nom_ville;
    }

    public function setnom_ville(string $nom_ville): self
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
/*
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
*/

/**
 * @return Collection<int, Trajets>
 */
public function getDemarrant(): Collection
{
    return $this->demarrant;
}

public function addDemarrant(Trajets $demarrant): self
{
    if (!$this->demarrant->contains($demarrant)) {
        $this->demarrant->add($demarrant);
        $demarrant->setDemarreA($this);
    }

    return $this;
}

public function removeDemarrant(Trajets $demarrant): self
{
    if ($this->demarrant->removeElement($demarrant)) {
        // set the owning side to null (unless already changed)
        if ($demarrant->getDemarreA() === $this) {
            $demarrant->setDemarreA(null);
        }
    }

    return $this;
}

/**
 * @return Collection<int, Trajets>
 */
public function getArrivant(): Collection
{
    return $this->arrivant;
}

public function addArrivant(Trajets $arrivant): self
{
    if (!$this->arrivant->contains($arrivant)) {
        $this->arrivant->add($arrivant);
        $arrivant->setArriveA($this);
    }

    return $this;
}

public function removeArrivant(Trajets $arrivant): self
{
    if ($this->arrivant->removeElement($arrivant)) {
        // set the owning side to null (unless already changed)
        if ($arrivant->getArriveA() === $this) {
            $arrivant->setArriveA(null);
        }
    }

    return $this;
}

public function __toString()
{
    return $this->nom_ville;
}

}
