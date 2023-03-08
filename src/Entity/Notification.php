<?php

namespace App\Entity;

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
}
?>