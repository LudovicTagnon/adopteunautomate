<?php

namespace App\Entity;

<<<<<<< HEAD
use DateTime;
=======
>>>>>>> Création de l'entité Notification
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
    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Utilisateurs $user = null;


    // GETTERS AND SETTERS 
=======
>>>>>>> Création de l'entité Notification
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
}
?>