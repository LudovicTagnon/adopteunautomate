<?php

namespace App\Entity;

<<<<<<< HEAD
<<<<<<< HEAD
use DateTime;
=======
>>>>>>> Création de l'entité Notification
=======
>>>>>>> 681ff40e2b039df6df6196e984666cb3689b3ed7
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $message;

    #[ORM\Column]
    private ?bool $isRead = false;

    #[ORM\Column]
    private ?\DateTime $createdAt;

<<<<<<< HEAD
<<<<<<< HEAD
    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Utilisateurs $user = null;


    // GETTERS AND SETTERS 
=======
>>>>>>> Création de l'entité Notification
=======
>>>>>>> 681ff40e2b039df6df6196e984666cb3689b3ed7
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
<<<<<<< HEAD
<<<<<<< HEAD

    public function setMessage(?String $string): self
    {
        $this->message = $string;
        return $this;
    }

    

    public function setCreatedAt(?DateTime $date): self
    {
        $this->createdAt = $date;
        return $this;
    }

    public function getUser(): ?Utilisateurs
    {
        return $this->user;
    }

    public function setUser(?Utilisateurs $user): self
    {
        $this->user = $user;

        return $this;
    }
=======
>>>>>>> Création de l'entité Notification
=======
>>>>>>> 681ff40e2b039df6df6196e984666cb3689b3ed7
}
?>