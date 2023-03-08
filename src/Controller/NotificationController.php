<?php

namespace App\Controller;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
