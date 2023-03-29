<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 300)]
    private ?string $message;

    #[ORM\Column]
    private ?bool $isRead = false;

    #[ORM\Column]
    private ?\DateTime $createdAt;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Utilisateurs $user = null;

    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private ?int $typeNotif = 1;

    #[ORM\ManyToOne]
    private ?Utilisateurs $userQuiDemandeTrajet = null;

    #[ORM\ManyToOne]
    private ?Trajets $TrajetQuiEstDemande = null;

    // GETTERS AND SETTERS 
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
    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

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

    public function getTypeNotif(): ?int
    {
        return $this->typeNotif;
    }

    public function setTypeNotif(int $typeNotif): self
    {
        $this->typeNotif = $typeNotif;

        return $this;
    }

    public function getUserQuiDemandeTrajet(): ?Utilisateurs
    {
        return $this->userQuiDemandeTrajet;
    }

    public function setUserQuiDemandeTrajet(?Utilisateurs $userQuiDemandeTrajet): self
    {
        $this->userQuiDemandeTrajet = $userQuiDemandeTrajet;

        return $this;
    }

    public function getTrajetQuiEstDemande(): ?Trajets
    {
        return $this->TrajetQuiEstDemande;
    }

    public function setTrajetQuiEstDemande(?Trajets $TrajetQuiEstDemande): self
    {
        $this->TrajetQuiEstDemande = $TrajetQuiEstDemande;

        return $this;
    }

    public function countUnreadNotifications(Utilisateurs $user): int
{
    $notifications = $user->getNotifications();

    $unreadNotifications = 0;

    foreach ($notifications as $notification) {
        if (!$notification->getIsRead()) {
            $unreadNotifications++;
        }
    }

    return $unreadNotifications;
}



}
?>