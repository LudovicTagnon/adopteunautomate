<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification;


class HeaderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager;
}
    #[Route('/header', name: 'app_header')]
    public function index(NotificationService $notificationService): Response
    {
        $user = $this->getUser();
        $notifications = $this->entityManager->getRepository(Notification::class)->findBy(['user' => $user]);
        $notificationCount = 0;
        foreach ($notifications as $notification) {
            $notificationCount = $notification->countUnreadNotifications($user);
        }
        return $this->render('partials/_header.html.twig', [
            'notificationCount' => $notificationCount,
        ]);
    }
}
