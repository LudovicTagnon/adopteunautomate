<?php

namespace App\Controller;

use App\Entity\Trajets;
use App\Entity\Villes;
use App\Entity\Utilisateurs;
use App\Form\TrajetsType;
use App\Repository\TrajetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/trajets')]
class TrajetsController extends AbstractController
{
    #[Route('/', name: 'app_trajets_index', methods: ['GET'])]
    public function index(TrajetsRepository $trajetsRepository): Response
    {
        
        // restriction à l'utilisateur logué en cours
        $trajets = $trajetsRepository->findBy(['publie'=> $this->getUser()]);
        // restriction à l'utilisateur qui a un véhicule - ne fonctionne pas, le User n'a pas d'attribut véhicule
       //if ($this->getUser()->getVehicule() ){
        return $this->render('trajets/index.html.twig', [
            'trajets' => $trajets
        ]);
        //}
        //else 
        //return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
    }

    
    #[Route('/new', name: 'app_trajets_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager ): Response
    {
        // retriction à ajouter: l'utilisateur doit avoir un véhicule
        //        TODO    

        $trajet = new Trajets();
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            // $trajetsRepository->save($trajet, true);
            $ville= new Villes();
            $ville ->setnom_ville('Ville_de_depart');
            printf($ville->getnom_ville());

            $ville= new Villes();
            $ville ->setnom_ville('Ville_arrivee');

            $trajet = $form->getData();
            // champs remplis d'office:
            $trajet->setPublie($this->getUser());
            $trajet->setEtat('ouvert');

            $manager->persist($trajet);
            $manager->persist($ville);
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
        ]);
    }
    

    #[Route('/{id}', name: 'app_trajets_show', methods: ['GET'])]
    public function show(Trajets $trajet): Response
    {
        return $this->render('trajets/show.html.twig', [
            'trajet' => $trajet,
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
    

    
    #[Route('/{id}', name: 'app_trajets_delete', methods: ['POST'])]
    public function delete(Request $request, Trajets $trajet, TrajetsRepository $trajetsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trajet->getId(), $request->request->get('_token'))) {
            $trajetsRepository->remove($trajet, true);
        }

        return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
