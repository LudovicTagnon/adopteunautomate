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

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $manager, NotificationService $notificationService,MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        $villes = $manager->getRepository(Villes::class)->findAll();
        $trajets = $manager->getRepository(Trajets::class)->findAll();
        $adoptions = $manager->getRepository(Adopte::class)->findAll();
        $form = $this->createForm(SearchTrajetType::class);
        $form->handleRequest($request);
        $notifications = [];//null par défaut
        if ($this->getUser() != null) {
            $notifications = $notificationService->getNotifications($this->getUser());
        }
        if ($form->isSubmitted()&&$form->isSubmitted()) {
            $villeDepart = $form->get('demarrea')->getData();
            
            $current_user = $this->getUser();

            $villes = $manager->getRepository(Villes::class)->findAll();
            echo $villeDepart; //TODO: fonctionne à reprendre
            //echo $jourDepart;
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
