<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateurs;
use App\Repository\NoteRepository;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ORM\UniqueConstraint(name: "trajet_utilisateur_unique", columns: ["trajet_id", "utilisateur_id"])]

class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer", options: ["default" => null])]
    private $id;


    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: "Utilisateur_id", referencedColumnName: "id", nullable: false)]
    private $utilisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Trajets::class)
     * @ORM\JoinColumn(name="trajet_id", referencedColumnName="id", nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: Trajets::class)]
    #[ORM\JoinColumn(name: "trajet_id", referencedColumnName: "id", nullable: false)]
    private $trajet;


    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: "integer")]
    private $note;

    /**
     * @ORM\Column(type="text")
     */
    #[ORM\Column(type: "text", nullable: true)]
    private $commentaire;

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecv_id(): ?int
    {
        return $this->recv_id;
    }

    public function setRecv_id(int $recv_id): self
    {
        $this->recv_id = $recv_id;

        return $this;
    }

    public function getDonne_id(): ?int
    {
        return $this->donne_id;
    }

    public function setDonne_id(int $donne_id): self
    {
        $this->donne_id = $donne_id;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }


    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        if (empty($commentaire)) {
            throw new \InvalidArgumentException("Commentaire cannot be empty");
        }

        $this->commentaire = $commentaire;

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

    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateurs $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}