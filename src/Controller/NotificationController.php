<?php

namespace App\Controller;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Notification;
use Symfony\Component\HttpFoundation\Request;


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

    #[Route('/notification', name:'notification_mark_as_read')]
    public function markNotificationAsRead(NotificationService $notificationService, $id, Request $request): Response
    {
        $form = 
        //Marquer comme lu 
        $notificationService->markAsRead($id);
        return $this->redirectToRoute('app_home');
    }

    #[Route('/notification', name:'delete_notification')]
    public function deleteNotification(NotificationService $notificationService, Request $request,  $notification): Response
    {
        //Supprimer la notif
        if ($this->isCsrfTokenValid('delete_notification', $request->request->get('_token'))) {
            //$notificationService->deleteNotification($notification);
        }

        

        return $this->redirectToRoute('app_home');
    }

    
}
