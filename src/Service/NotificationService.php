<?php
// src/Service/NotificationService.php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class NotificationService
{
    private $entityManager;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    public function addNotification($message, $user)
    {
        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setUser($user);
        $notification->setCreatedAt(new \DateTime());
        if ($user->getAutorisationMail()) {
            $email = (new Email())
            ->from('adopteautomate-noreply@example.com')
            ->to($user->getEmail())
            ->subject('Notification')
            ->text($message);

            $this->mailer->send($email);
        }

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    public function getNotifications(Utilisateurs $user)
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
