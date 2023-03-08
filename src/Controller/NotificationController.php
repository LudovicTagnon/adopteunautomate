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
class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    public function index(NotificationService $notificationService): Response
    {
        $notifications = $notificationService->getNotifications($this->getUser());

        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications' => $notifications,
        ]);
    }
    #[Route('/notification/supprimer', name: 'app_supprimer_notif')]
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
