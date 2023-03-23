<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: NoteRepository::class)]

class Note
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;


    #[ORM\Column(name: 'Trajet_id')]
    private $Trajet_id;

    /**
     * @ORM\ManyToOne(targetEntity=Trajets::class)
     * @ORM\JoinColumn(name="Trajet_id", referencedColumnName="id")
     */
    private $trajet;

    #[ORM\Column]
    private $recv_id;

    
    #[ORM\Column]
    private $donne_id;

    
    #[ORM\Column]
    private $note;

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrajet_id(): ?int
    {
        return $this->Trajet_id;
    }

    public function setTrajet_id(int $Trajet_id): self
    {
        $this->Trajet_id = $Trajet_id;

        return $this;
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

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;

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