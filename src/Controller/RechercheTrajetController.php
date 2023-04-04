<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use App\Entity\Trajets;
use App\Entity\Villes;
use Symfony\Component\HttpFoundation\Request;



class RechercheTrajetController extends AbstractController
{

    #[Route('/recherche/trajet', name: 'app_recherche_trajet')]
    public function index(): Response
    {

        return $this->render('recherche_trajet/index.html.twig', [
            'controller_name' => 'RechercheTrajetController',
            'trajets' => []

        ]);
    }

    #[Route('/recherche/trajet/search', name: 'app_recherche_trajet_search')]
    public function search(Request $request): Response
    {
        $startingCity = $request->query->get('startingCity');
        $destinationCity = $request->query->get('destinationCity');
        $date = \DateTime::createFromFormat('Y-m-d', $request->query->get('date'));

        $entityManager = $this->getDoctrine()->getManager();

        $trajets = [];

        if ($startingCity && $destinationCity && $date) {
            $trajets = $entityManager->createQueryBuilder()
                ->select('t')
                ->from('App\Entity\Trajets', 't')
                ->leftJoin('t.demarreA', 'villeDepart')
                ->leftJoin('t.arriveA', 'villeArrivee')
                ->where('villeDepart.nom_ville = :startingCity')
                ->andWhere('villeArrivee.nom_ville = :destinationCity')
                ->andWhere('t.date_depart >= :date')
                ->setParameters([
                    'startingCity' => $startingCity,
                    'destinationCity' => $destinationCity,
                    'date' => $date,
                ])
                ->getQuery()
                ->getResult();
        }


        return $this->render('recherche_trajet/index.html.twig', [
            'trajets' => $trajets,
        ]);
    }

}
