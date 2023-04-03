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
use App\Entity\EstAccepte;
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

        $trajets = $manager->getRepository(Trajets::class)
            ->createQueryBuilder('t')
            ->where('t.etat != :etat')
            ->andWhere('t.publie = :user')
            ->setParameter('etat', 'terminé')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $adoptions = $manager->getRepository(Adopte::class)
            ->createQueryBuilder('a')
            ->join('a.trajet', 't')
            ->where('t.etat != :etat')
            ->setParameter('etat', 'terminé')
            ->getQuery()
            ->getResult();

        $inscriptions = $manager->getRepository(EstAccepte::class)
            ->createQueryBuilder('i')
            ->join('i.trajet', 't')
            ->where('t.etat != :etat')
            ->setParameter('etat', 'terminé')
            ->getQuery()
            ->getResult();

        $form = $this->createForm(SearchTrajetType::class);

        $form->handleRequest($request);
        $notifications = [];//null par défaut

        if ($this->getUser() != null) {
            $notifications = $notificationService->getNotifications($this->getUser());
        }
        if ($form->isSubmitted()) {
            $villeDepart = $form->get('demarrea')->getData();
            $villeArrivee = $form->get('arrivea')->getData();
            $dateDepart = $form->get('T_depart')->getData();
            $estAccepteRepository = $manager->getRepository(EstAccepte::class);
            $estAccepte = $estAccepteRepository->findAll();

            
            $current_user = $this->getUser();
            $trajets = $manager->getRepository(Trajets::class)->findByCritere($current_user, $villeDepart, $villeArrivee,  $dateDepart);

            $dateA = $dateDepart;

            $dateDepart = null;

            if ($dateA instanceof DateTime) {
                $dateDepart = $dateA->format('d-m-Y');
            } else {
                // handle the case where the date string is invalid
            }

            return $this->render('trajets/search.html.twig', [
                'user' => $user,
                'trajets' => $trajets,
                'nb_trajets' => count($trajets),
                'villes' => $villes,
                'depart' => $villeDepart,
                'arrivee' => $villeArrivee,
                'date' => $dateDepart,
                'utilisateur_actuel' => $current_user,
                'form' => $form->createView(),
                'estAccepte' => $estAccepte,
            ]);

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
            'inscriptions' => $inscriptions,
        ]);

    }
}
