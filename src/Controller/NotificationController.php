<?php

namespace App\Controller;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Notification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\NotificationRepository;
class NotificationController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
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


    #[Route('/notification/mark-all-as-read', name: 'app_mark_all_notifications_as_read')]
public function markAllNotificationsAsRead(NotificationService $notificationService): Response
{
    $notificationService->markAllAsRead($this->getUser());
    
    return $this->redirectToRoute('app_notification');
}

#[Route('/notification/delete-all', name: 'app_delete_all_notifications')]
public function deleteAllNotifications(NotificationService $notificationService): Response
{
    $notificationService->deleteAll($this->getUser());
    
    return $this->redirectToRoute('app_notification');
}

#[Route('/notification/count', name: 'app_notification_count')]
public function countUnreadNotifications(): JsonResponse
{
    $user = $this->getUser();
    $count = $this->entityManager->getRepository(Notification::class)->countUnreadNotifications($user);
    dump($count);
    return new JsonResponse(['count' => $count]);
}

    
}
