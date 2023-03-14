<?php

namespace App\Controller;

use DateTime;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Villes;
use App\Entity\Trajets;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $manager, NotificationService $notificationService): Response
    {
        $villes = $manager->getRepository(Villes::class)->findAll();
        
        $notifications = [];//null par défaut
        if ($this->getUser() != null) {
            $notifications = $notificationService->getNotifications($this->getUser());
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'villes' => $villes,
            'notifications' => $notifications,
        ]);

    }
}
