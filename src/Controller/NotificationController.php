<?php

namespace App\Controller;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Notification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    #[IsGranted('ROLE_USER')]
    public function index(NotificationService $notificationService,EntityManagerInterface $manager): Response
    {
        $notifications = $notificationService->getNotifications($this->getUser());

        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications' => $notifications,
            'trajets' => $notifications,
        ]);
    }

    #[Route('/notification/{id}/mark-as-read', name: 'app_mark_notification_as_read')]
    public function markAsRead(Request $request, ManagerRegistry $doctrine): Response
    {
        $notificationId = $request->get('id');

        $entityManager = $doctrine->getManager();
        $notification = $entityManager->getRepository(Notification::class)->find($notificationId);

        if (!$notification) {
            throw $this->createNotFoundException('Notification introuvable pour id : '.$notificationId);
        }

        $notification->setIsRead(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_notification');
    }


    #[Route('/notification/{id}/supprimer', name: 'app_supprimer_notif')]
    public function supprimerNotification(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $notificationId = $request->get('id');
    
        $notification = $entityManager->getRepository(Notification::class)->find($notificationId);
    
        if (!$notification) {
            throw $this->createNotFoundException('Notification introuvable');
        }
    
        $entityManager->remove($notification);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_notification');
    }
    
}
