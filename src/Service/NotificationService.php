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

    public function addNotificationModifProfil($message, $user)
    {
        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setUser($user);
        $notification->setCreatedAt(new \DateTime());
        if ($user->getAutorisationMail()) {
            $email = (new Email())
            ->from('adopteautomate-noreply@example.com')
            ->to($user->getEmail())
            ->subject('Notification - Modification du profil')
            ->text($message);

            $this->mailer->send($email);
        }

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
    public function addNotificationAdopteTrajet($message, $user,$trajet)
    {
        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setUser($user);
        $notification->setCreatedAt(new \DateTime());
        if ($user->getAutorisationMail()) {
            $email = (new Email())
            ->from('adopteautomate-noreply@example.com')
            ->to($user->getEmail())
            ->subject('Notification - Trajet adopté')
            ->text($message);

            $this->mailer->send($email);
        }
        //puis on envoi un mail au chauffeur
        $chauffeur = $trajet->getPublie();
        $message = $user->getNom()."  : veut participer à votre trajet de ".$trajet->getDemarreA()->getnomVille()." vers ".$trajet->getArriveA()->getnomVille()." du ".$trajet->getJourDepartString();
        $notification = new Notification();
        $notification->setUserQuiDemandeTrajet($user);
        $notification->setTrajetQuiEstDemande($trajet);
        $notification->setTypeNotif(2);
        $notification->setMessage($message);
        $notification->setUser($chauffeur);
        $notification->setCreatedAt(new \DateTime());
        if($chauffeur->getAutorisationMail()){ //si ses mails sont autorisés

        }

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    public function addNotificationAbandonneTrajet($message, $user,$trajet)
    {
        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setUser($user);
        $notification->setCreatedAt(new \DateTime());
        if ($user->getAutorisationMail()) {
            $email = (new Email())
            ->from('adopteautomate-noreply@example.com')
            ->to($user->getEmail())
            ->subject('Notification - Trajet abandonné')
            ->text($message);

            $this->mailer->send($email);
        }

        //puis on envoi un mail au chauffeur
        $chauffeur = $trajet->getPublie();
        $message = $user->getNom()." a abandonné votre trajet de ".$trajet->getDemarreA()." vers ".$trajet->getArriveA()." du ".$trajet->getJourDepartString();
        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setUser($chauffeur);
        $notification->setCreatedAt(new \DateTime());
        if($chauffeur->getAutorisationMail()){ //si ses mails sont autorisés

        }

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    public function addNotificationAccepteTrajet($message, $user)
    {
        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setUser($user);
        $notification->setCreatedAt(new \DateTime());
        if ($user->getAutorisationMail()) {
            $email = (new Email())
            ->from('adopteautomate-noreply@example.com')
            ->to($user->getEmail())
            ->subject('Notification - Trajet accepté')
            ->text($message);

            $this->mailer->send($email);
        }

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    public function addNotificationRefuseTrajet($message, $user)
    {
        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setUser($user);
        $notification->setCreatedAt(new \DateTime());
        if ($user->getAutorisationMail()) {
            $email = (new Email())
            ->from('adopteautomate-noreply@example.com')
            ->to($user->getEmail())
            ->subject('Notification - Trajet refusé')
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
