<?php

namespace App\Controller;

use App\Entity\Villes;
use App\Entity\Trajets;
use App\Form\TrajetsType;
use App\Entity\Utilisateurs;
use App\Repository\TrajetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
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
        //$villedepart= new Villes();
        //$villearrivee= new Villes();
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
           
            //On récupère les données du formulaire
            $trajet = $form->getData();

            //On vérifie d'abord si les villes existent déjà dans la base de donnée

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
    

    #[Route('/{id}', name: 'app_trajets_show', methods: ['GET'])]
    public function show(Trajets $trajet, Request $request, EntityManagerInterface $manager): Response
    {
        // modifications automatiques de l'état d'un trajet
        // dans l'affichage
        $demain = new DateTime('tomorrow');
        if ($trajet->getTDepart() <$demain ) {
            $trajet->setEtat('bloqué');

            $this->addFlash(
                'succès',
                'Votre trajet ne peut plus être modifié !'
            );    
        }
        $maintenant = new DateTime();
        $hier = new DateTime('yesterday');
        if ($trajet->getTArrivee() <$maintenant  || $trajet->getTDepart() <$hier ) {
            $trajet->setEtat('terminé');
            $this->addFlash(
                'succès',
                'Votre trajet est terminé !'
            );    
        }
        
        $manager->persist($trajet);

        $manager->flush();

        return $this->render('trajets/show.html.twig', [
            'trajet' => $trajet,
        ]);
    }

    
    #[Route('/{id}/edit', name: 'app_trajets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trajets $trajet, TrajetsRepository $trajetsRepository, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);
        $trajet = $form->getData();
        
        $demain = new DateTime('tomorrow');
        if ($trajet->getTDepart() <$demain ) {
            $this->addFlash(
                'warning',
                'Vous ne pouvez plus modifier ce trajet.'
            );
            
            return $this->redirectToRoute('app_trajets_index');
        }



        if ($form->isSubmitted() && $form->isValid()) {
            $trajetsRepository->save($trajet, true);
            
            $manager->persist($trajet);

            $manager->flush();
            return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trajets/edit.html.twig', [
            'trajet' => $trajet,
            'form' => $form,
        ]);
    }
    

    
    #[Route('/{id}', name: 'app_trajets_delete', methods: ['POST'])]
    public function delete(Request $request, Trajets $trajet, TrajetsRepository $trajetsRepository, EntityManagerInterface $manager): Response
    {
        $demain = new DateTime('tomorrow');
        if ($trajet->getTDepart() <$demain ) {
            $trajet->setEtat('bloqué');
            $this->addFlash(
                'warning',
                'Vous ne pouvez plus supprimer ce trajet.'
            );

            return $this->redirectToRoute('app_trajets_index');
        }
        
        if ($this->isCsrfTokenValid('delete'.$trajet->getId(), $request->request->get('_token'))) {
            $trajet->setEtat('annulé');
            // si on l'enlève carrément:
            $trajetsRepository->remove($trajet, true);
        }
        $manager->persist($trajet);

        $manager->flush();
        return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
