<?php

namespace App\Controller;

use App\Entity\EstAccepte;
use DateTime;
use Symfony\Component\VarDumper\VarDumper;
use App\Entity\Trajets;
use App\Entity\Villes;
use App\Form\TrajetsType;
use App\Form\SearchTrajetType;
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
use App\Service\NotificationService;

#[Route('/trajets')]
class TrajetsController extends AbstractController
{
    

    #[Route('/mes_propositions', name: 'app_trajets_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(TrajetsRepository $trajetsRepository): Response
    {
        
        // restriction à l'utilisateur logué en cours
        $trajets = $trajetsRepository->findBy(['publie'=> $this->getUser()]);
       
        return $this->render('trajets/index.html.twig', [
            'trajets' => $trajets
        ]);
      
    }

    
    #[Route('/créer_un_trajet', name: 'app_trajets_new', methods: ['GET', 'POST'])]
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
    

    #[Route('/{id}/visualiser', name: 'app_trajets_show', methods: ['GET'])]
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
        /*
        if ($trajet->getTDepart() <$maintenant ) {
            $trajet->setEtat('terminé');

            $this->addFlash(
                'succès',
                'Votre trajet est terminé !'
            );    
        }
        */
        
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
    public function edit(Request $request, Trajets $trajet, TrajetsRepository $trajetsRepository, EntityManagerInterface $manager, NotificationService $notificationService): Response
    {
        $trajetAncien = $trajet->__toString();
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);
        $trajet = $form->getData();
        
        // ne pas pouvoir modifier un trajet qui part dans moins de 24h
        $demain = new DateTime('+24 hours');
        if ($trajet->getTDepart() <$demain ) {
            $trajet->setEtat('bloqué');
            $this->addFlash(
                'warning',
                'Vous ne pouvez plus modifier ce trajet.'
            );
            $manager->persist($trajet);

            $manager->flush();
            return $this->redirectToRoute('app_trajets_index');
        }

        
        $form = $this->createForm(TrajetsType::class, $trajet);

/*
        if ($trajet->getTDepart() < $demain ) {
            $form->remove('Modifier'); // supprimer le bouton "submit" pour désactiver le formulaire
        }
*/
        $form->handleRequest($request);
        $trajet = $form->getData();
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $trajetsRepository->save($trajet, true);
            
            $manager->persist($trajet);
            $manager->flush();

            $users = [];
            //ENVOI DE LA NOTIFICATION A TOUS LES UTILISATEURS INSCRITS AU TRAJET
            $estAcceptes = $manager->getRepository(EstAccepte::class)->findBy(['trajet' => $trajet]);
            foreach ($estAcceptes as $estAccepte) {
                $users[] = $estAccepte->getUtilisateur();
            }
    
            foreach ($users as $user) {
                $notificationService->addNotificationModifTrajet("Le trajet : ".$trajetAncien." a été modifié, voici le nouveau trajet : ".$trajet->__toString(),$user);
            }
            return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
        }

         
        return $this->render('trajets/edit.html.twig', [
            'trajet' => $trajet,
            'form' => $form,
        ]);
    }
    

    
    #[Route('/{id}', name: 'app_trajets_delete', methods: ['POST'])]
    //#[Route('/', name: 'app_trajets_index', methods: ['GET'])]
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

        // si le trajet est terminé ou que son départ a eu lieu il y a plus de 24h
        // conditions à écrire, avec update de $trajet.etat
        // blocage de la suppression via effacement du bouton dans trajets/index
        
        if ($this->isCsrfTokenValid('delete'.$trajet->getId(), $request->request->get('_token'))) {
            $trajet->setEtat('annulé');
            // si on l'enlève carrément:
            $trajetsRepository->remove($trajet, true);
        }
        $manager->persist($trajet);
        $manager->flush();

        return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
    }

   #[Route('/rechercher-trajet', name: 'app_trajets_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $manager,mixed $recherche): Response
    {
        $current_user = $this->getUser();

        if ($current_user) {
            $villes = $manager->getRepository(Villes::class)->findAll();

            $villeDepart = null;
            $villeArrivee = null;
            $jourDepart = null;

            if($recherche != null){
                $villeDepart = $recherche->getDemarreA();
                $villeArrivee = $recherche->getArriveA();
                $jourDepart = $recherche->getTDepart();
            }

            if ($villeDepart != null || $villeArrivee !=null){
                $trajets = $manager->getRepository(Trajets::class)->findByCritere($current_user, $villeDepart, $villeArrivee,  $jourDepart);
            }else{
                $dateActuelle = new \DateTime();//Récupération de la date actuelle
                $trajets = $manager->getRepository(Trajets::class)->createQueryBuilder('t')
                    ->where('t.T_depart >= :dateActuelle')
                    ->setParameter('dateActuelle', $dateActuelle)
                    ->getQuery()
                    ->getResult();
            }
            $estAccepteRepository = $manager->getRepository(EstAccepte::class);
            $estAccepte = $estAccepteRepository->findAll();

            $dateA = DateTime::createFromFormat('Y-m-d', $jourDepart);

            $dateDepart = null;

            if ($dateA instanceof DateTime) {
                $dateDepart = $dateA->format('d-m-Y');
            } else {
                // handle the case where the date string is invalid
            }

            $form = $this->createForm(SearchTrajetType::class);
            $form->handleRequest($request);

            return $this->render('trajets/search.html.twig', [
                'user' => $current_user,
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
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/historique', name: 'app_trajets_history', methods: ['GET'])]
    public function historique(Request $request, EntityManagerInterface $manager): Response{
        
        $current_user = $this->getUser();

        if ($current_user) {//Utilisateur connecté
            $trajetsChauffeur = $manager->getRepository(Trajets::class)->findBy(['publie'=> $this->getUser()]);
            $trajetsPassager = $manager->getRepository(EstAccepte::class)->findBy(['utilisateur'=> $this->getUser()]);

            return $this->render('trajets/history.html.twig', [
                'trajetsChauffeur' => $trajetsChauffeur,
                'trajetsPassager' => $trajetsPassager,
            ]);
        }else{//Utilisateur non connecté -> redirigé
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/{id}/terminer', name: 'app_trajets_terminer', methods: ['POST'])]
    public function terminer(Request $request, EntityManagerInterface $manager, Trajets $trajet): Response
    {
        if (!$trajet) {
            throw $this->createNotFoundException('The Trajets object was not found.');
        }
        $this->addFlash(
            'warning',
            'Vos passagers et vous pouvez vous évaluer.'
        );
        $trajet->setEtat('terminé');

        $manager->persist($trajet);
        $manager->flush();

        return $this->redirectToRoute('app_trajets_index');

    }

    
}
