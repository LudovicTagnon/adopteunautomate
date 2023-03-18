<?php

namespace App\Controller;

use DateTime;
use Symfony\Component\VarDumper\VarDumper;
use App\Entity\Trajets;
use App\Entity\Villes;
use App\Form\TrajetsType;
use App\Entity\Utilisateurs;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\TrajetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormError;


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
<<<<<<<<< Temporary merge branch 1
=========
           
>>>>>>>>> Temporary merge branch 2
            //On récupère les données du formulaire
            $trajet = $form->getData();

            //On vérifie d'abord si les villes existent déjà dans la base de donnée
            // var_dump($form->get('demarrea')->getData());
            $villeDepart = $manager->getRepository(Villes::class)->find(['id' => $form->getData()->getDemarreA()]);
            $villeArrivee = $manager->getRepository(Villes::class)->find(['id' => $form->getData()->getArriveA()]);
            $trajet->setArriveA($villeArrivee);
            $trajet->setDemarreA($villeDepart);
            $public = $form->get('public')->getData();
            if ($trajet->getPublic() === false && $form->get('groupes')->getData()->isEmpty()) {
                $form->get('groupes')->addError(new FormError('Veuillez choisir au moins un groupe pour un trajet privé'));
                $this->addFlash(
                    'warning',
                    'Vous devez sélectionner au moins 1 groupe !'
                );
                return $this->render('trajets/new.html.twig', [
                    'form' => $form->createView(),
                    'trajet' => $trajet,
                    'user' => $user,
                ]);
            }
            if (!$public) {
                // Get the selected groups
                $groupes = $form->get('groupes')->getData();
                if (!empty($groupes)) {
                    foreach ($groupes as $groupe) {
                        $trajet->addGroupe($groupe);
                    }
                }
            }


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
        if (!$trajet) {
            throw $this->createNotFoundException('The Trajets object was not found.');
        }
        // modifications automatiques de l'état d'un trajet
        // dans l'affichage
        $demain = new DateTime('+24 hours');
        if ($trajet->getTDepart() <$demain ) {
            $trajet->setEtat('bloqué');

            $this->addFlash(
                'succès',
                'Votre trajet ne peut plus être modifié !'
            );    
        }
        $maintenant = new DateTime();
        $hier = new DateTime('-24 hours');
        if (( ($trajet->getTArrivee() !='null') and ($trajet->getTArrivee() <$maintenant))  or $trajet->getTDepart() <$hier )
         {
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
    public function edit(Request $request, Trajets $trajet, TrajetsRepository $trajetsRepository): Response
    {
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);
        $trajet = $form->getData();
        
        $demain = new DateTime('+24 hours');
        if ($trajet->getTDepart() <$demain ) {
            $this->addFlash(
                'warning',
                'Vous ne pouvez plus modifier ce trajet.'
            );
            
            return $this->redirectToRoute('app_trajets_index');
        }



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

        return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
    }

   #[Route('/rechercher-trajet', name: 'app_trajets_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $manager): Response
    {
        $current_user = $this->getUser();

        if ($current_user) {
            $villes = $manager->getRepository(Villes::class)->findAll();

            $villeDepart = $request->query->get('ville_depart');
            $villeArrivee = $request->query->get('ville_arrivee');
            $jourDepart = $request->query->get('date_depart');

            $trajets = $manager->getRepository(Trajets::class)->findByCritere($current_user, $villeDepart, $villeArrivee,  $jourDepart);

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
