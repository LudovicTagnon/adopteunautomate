<?php
// src/Service/NotificationService.php

namespace App\Service;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;


class NotificationService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addNotification($message, $user)
    {
        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setUser($user);
        $notification->setCreatedAt(new \DateTime());

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    public function getNotifications($user)
    {
        return $this->entityManager->getRepository(Notification::class)->findBy([
            'user' => $user,
        ]);
    }

    public function markAsRead($notificationId)
    {
        $notification = $this->entityManager->getRepository(Notification::class)->find($notificationId);
        $notification->setIsRead(true);

        $this->entityManager->flush();
    }
}
