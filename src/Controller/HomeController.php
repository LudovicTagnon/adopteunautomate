<?php

namespace App\Controller;

use DateTime;
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
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $current_user = $this->getUser();

        if ($current_user) {
            $villes = $manager->getRepository(Villes::class)->findAll();

            $villeDepart = $request->query->get('ville_depart');
            $villeArrivee = $request->query->get('ville_arrivee');
            $jourDepart = $request->query->get('date_depart');

            $trajets = $manager->getRepository(Trajets::class)->findByCritere($villeDepart, $villeArrivee, $jourDepart);

            $dateA = DateTime::createFromFormat('Y-m-d', $jourDepart);

            $dateDepart = null;

            if ($dateA instanceof DateTime) {
                $dateDepart = $dateA->format('d-m-Y');
            } else {
                // handle the case where the date string is invalid
            }

            return $this->render('trajets/search.html.twig', [
                'trajets' => $trajets,
                'nb_trajets' => count($trajets),
                'villes' => $villes,
                'depart' => $villeDepart,
                'arrivee' => $villeArrivee,
                'date' => $dateDepart,
                'utilisateur_actuel' => $current_user,
            ]);
        } else {
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
    }
}
