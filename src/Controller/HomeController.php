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
use App\Entity\Adopte;
use App\Form\SearchTrajetType;
use App\Controller\TrajetsController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $manager, NotificationService $notificationService,MailerInterface $mailer,TrajetsController $TrajetsController): Response
    {
        $user = $this->getUser();
        $villes = $manager->getRepository(Villes::class)->findAll();
        $trajets = $manager->getRepository(Trajets::class)->findAll();
        $adoptions = $manager->getRepository(Adopte::class)->findAll();
        $form = $this->createForm(SearchTrajetType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $response = $TrajetsController->search($request, $manager);
            return $response;
        }
        $notifications = [];//null par défaut
        if ($this->getUser() != null) {
            $notifications = $notificationService->getNotifications($this->getUser());
        }

        return $this->render('home/index.html.twig', [
            'user' => $user,
            'controller_name' => 'HomeController',
            'villes' => $villes,
            'notifications' => $notifications,
            'trajets' => $trajets,
            'adopte' => $adoptions,
            'form' => $form->createView(),
        ]);

    }
}
