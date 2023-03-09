<?php

namespace App\Controller;

use Symfony\Component\VarDumper\VarDumper;
use App\Entity\Trajets;
use App\Entity\Villes;
use App\Entity\Utilisateurs;
use App\Form\TrajetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\SearchTrajetType;
use App\Repository\TrajetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/trajets')]
class TrajetsController extends AbstractController
{
    

    #[Route('/', name: 'app_trajets_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(TrajetsRepository $trajetsRepository): Response
    {
        
        // restriction à l'utilisateur logué en cours
        $trajets = $trajetsRepository->findBy(['publie'=> $this->getUser()]);
       
        return $this->render('trajets/index.html.twig', [
            'trajets' => $trajets
        ]);
      
    }

    
    #[Route('/new', name: 'app_trajets_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager ): Response
    {
        
        $user = $this->getUser();
        $trajet = new Trajets();
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère les données du formulaire
            $trajet = $form->getData();

            //On vérifie d'abord si les villes existent déjà dans la base de donnée
            // var_dump($form->get('demarrea')->getData());
            $villeDepart = $manager->getRepository(Villes::class)->find(['id' => $form->getData()->getDemarreA()]);
            $villeArrivee = $manager->getRepository(Villes::class)->find(['id' => $form->getData()->getArriveA()]);
            $trajet->setArriveA($villeArrivee);
            $trajet->setDemarreA($villeDepart);

            // champs remplis d'office:
            $trajet->setPublie($this->getUser());
            $trajet->setEtat('ouvert');

            $manager->persist($trajet);

            $manager->flush();

            $this->addFlash(
                'succès',
                'Votre trajet a bien été créé !'
            );

            return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trajets/new.html.twig', [
            'trajet' => $trajet,
            'form' => $form,
            'user' => $user,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_trajets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trajets $trajet, TrajetsRepository $trajetsRepository): Response
    {
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajetsRepository->save($trajet, true);

            return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trajets/edit.html.twig', [
            'trajet' => $trajet,
            'form' => $form,
        ]);
    }
    

    
    #[Route('/search', name: 'app_trajets_delete', methods: ['POST'])]
    public function delete(Request $request, Trajets $trajet, TrajetsRepository $trajetsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trajet->getId(), $request->request->get('_token'))) {
            $trajetsRepository->remove($trajet, true);
        }

        return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/rechercher-trajet', name: 'app_trajets_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(SearchTrajetType::class);
        $current_user = $this->getUser();

        $villes = $manager->getRepository(Villes::class)->findAll();

        $villeDepart = $request->query->get('ville_depart');
        $villeArrivee = $request->query->get('ville_arrivee');
        $jourDepart = $request->query->get('date_depart');

        $trajets = $manager->getRepository(Trajets::class)->findByCritere($villeDepart, $villeArrivee, $jourDepart);

        $dateDepart = \DateTime::createFromFormat('Y-m-d', $jourDepart);

        return $this->render('trajets/search.html.twig', [
            'form' => $form->createView(),
            'trajets' => $trajets,
            'nb_trajets' => count($trajets),
            'villes' => $villes,
            'depart' => $villeDepart,
            'arrivee' => $villeArrivee,
            'date' => $dateDepart,
            'utilisateur_actuel' => $current_user,
        ]);
    }
    
}
