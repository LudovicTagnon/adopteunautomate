<?php

namespace App\Entity;

use App\Entity\Utilisateurs;
use App\Repository\GroupesRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupesRepository::class)]
class Groupes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_groupe = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    private Utilisateurs $createur;

    #[ORM\Column(length: 50)]
    private ?string $nom_groupe = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $description = null;

    #[ORM\ManyToMany(mappedBy:'groupes',targetEntity: Utilisateurs::class,cascade:["persist","remove"])]
    private Collection $utilisateurs;

    public function __construct()
    {
        $this->utilisateurs = new \Doctrine\Common\Collections\ArrayCollection();;
    }

    public function getId(): ?int
    {
        return $this->id_groupe;
    }

    public function getCreateur(): ?int
    {
        return $this->createur;
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

    /**
     * @return Collection|Utilisateurs[]
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateurs $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateurs $utilisateur): self
        {
        if ($this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->removeElement($utilisateur);
        }

    return $this;
    }

}
