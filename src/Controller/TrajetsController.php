<?php

namespace App\Controller;

use Symfony\Component\VarDumper\VarDumper;
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
            // $trajetsRepository->save($trajet, true);
           
           // $villedepart ->setnom_ville($form->getData()['demarrea']);
                    
           // $villearrivee ->setnom_ville($form->getData()['arrivea']);


            //On récupère les données du formulaire
            $trajet = $form->getData();

            //On vérifie d'abord si les villes existent déjà dans la base de donnée
            // var_dump($form->get('demarrea')->getData());
            $villeDepart = $manager->getRepository(Villes::class)->find(['id' => $form->getData()->getDemarreA()]);
            $villeArrivee = $manager->getRepository(Villes::class)->find(['id' => $form->getData()->getArriveA()]);
            $trajet->setArriveA($villeArrivee);
            $trajet->setDemarreA($villeDepart);

            //Si elles existent, on ne les créer pas mais on récupère l'id de celle déjà existante
            //Sinon je la crée
            /*if (!$villeDepart) {
                $villeDepart = new Villes();
                $villeDepart->setNomVille($form->get('demarrea')->getData());
                $manager->persist($villeDepart);
                $manager->flush();
            }
        
            if (!$villeArrivee) {
                $villeArrivee = new Villes();
                $villeArrivee->setNomVille($form->get('arrivea')->getData());
                $manager->persist($villeArrivee);
                $manager->flush();
            }*/

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
