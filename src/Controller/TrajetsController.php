<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Entity\Villes;
use App\Entity\Trajets;
use App\Entity\Groupes;
use App\Form\TrajetsType;
use App\Entity\EstAccepte;
use App\Entity\Notification;
use App\Entity\Utilisateurs;
use App\Form\SearchTrajetType;
use App\Controller\AdopteController;
use App\Entity\Adopte;
use App\Service\NotificationService;
use App\Repository\TrajetsRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\VarDumper\VarDumper;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/trajets')]
class TrajetsController extends AbstractController
{
    

    #[Route('/mes-propositions', name: 'app_trajets_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(TrajetsRepository $trajetsRepository): Response
    {
        
        // restriction à l'utilisateur logué en cours
        $trajets = $trajetsRepository->findBy(['publie'=> $this->getUser()]);
       
        return $this->render('trajets/index.html.twig', [
            'trajets' => $trajets
        ]);
      
    }

    
    #[Route('/créer-un-trajet', name: 'app_trajets_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager ): Response
    {
        
        $user = $this->getUser();
        $trajet = new Trajets();
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);

        // si contraintes temporelles pas bonnes: refus et message d'erreur
        // à améliorer : limiter aux trajets futurs !
        
        $listeTrajets = [];
        // récupération des trajets créés -
        $trajetpresent = new Trajets();
        //$trajetStockes = $manager->getRepository(Trajets::class)->findBy(['depart' => new \DateTime('now'), Criteria::GREATER_THAN]);
        $trajetStockes = $manager->getRepository(Trajets::class)->findAll();
        foreach ($trajetStockes as $stocke) {
            $listeTrajets[] = $stocke  /*->getTrajet() */;
        }
        // ajout arbitraire d'une heure d'arrivée pour comparer le trajet courant
        $bientot = new DateInterval('PT12H');
        foreach ($listeTrajets as $comparaison){
            // choix arbitraire: trajet de 12h maximum
            if ($comparaison->getTArrivee() === null)
                {
                    $comparaison->setTArrivee = clone $comparaison->getTDepart();
                    $comparaison->setTArrivee->add( $bientot)   ; 
                }  
        }

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

            // verification des contraintes temporelles
            // départ pendant un autre trajet
            foreach ($listeTrajets as $comparaison){
               
                if (($trajet->getTDepart() <= $comparaison->getTArrivee() && $trajet->getTDepart() >= $comparaison->getTDepart()) )
                   {
                       $creaneau = false;
                       $form->get('T_depart')->addError(new FormError('Vous avez déjà un trajet prévu. Veuillez modifier votre départ. '));
                $this->addFlash(
                    'warning',
                    'Vous avez un autre trajet en cours à cette heure !'
                );
                return $this->render('trajets/new.html.twig', [
                    'form' => $form->createView(),
                    'trajet' => $trajet,
                    'user' => $user,
                ]);
                    }  
            }
            // arrivee prévue pendant un autre trajet
            if ($trajet->getTArrivee() != null){
                foreach ($listeTrajets as $comparaison){
                    //$difArrivee= $comparaison->getTArrivee()->diff($trajet->getTArrivee());
                    //$difDepart = $comparaison->getTDepart()->diff($trajet->getTArrivee());
                    if (($trajet->getTArrivee() <= $comparaison->getTArrivee() && $trajet->getTArrivee() >= $comparaison->getTDepart()) )
                    
                    //if ($difArrivee>0 and $difDepart <0)
                    //if ( ($comparaison->getTArrivee() - $trajet->getTArrivee() >0 ) and ($comparaison->getTDepart() - $trajet->getTArrivee() <0 )  )
                        {
                           $creaneau = false;
                           $form->get('T_arrivee')->addError(new FormError('Vous avez déjà un trajet prévu. Veuillez modifier votre arrivee. '));
                    $this->addFlash(
                        'warning',
                        'Vous avez un autre trajet en cours à cette heure !'
                    );
                    return $this->render('trajets/new.html.twig', [
                        'form' => $form->createView(),
                        'trajet' => $trajet,
                        'user' => $user,
                    ]);
                        }  
                }
            }
            // trajet enjambant un autre
            if ($trajet->getTArrivee() != null){
                foreach ($listeTrajets as $comparaison){
                    //$difArrivee= $comparaison->getTArrivee()->diff($trajet->getTArrivee());
                    //$difDepart = $comparaison->getTDepart()->diff($trajet->getTArrivee());
                    if (($trajet->getTArrivee() >= $comparaison->getTArrivee() && $trajet->getTDepart() <= $comparaison->getTDepart()) )
                    
                    //if ($difArrivee>0 and $difDepart <0)
                    //if ( ($comparaison->getTArrivee() - $trajet->getTArrivee() >0 ) and ($comparaison->getTDepart() - $trajet->getTArrivee() <0 )  )
                        {
                           $creaneau = false;
                           $form->get('T_arrivee')->addError(new FormError('Vous avez déjà un trajet prévu. Veuillez modifier votre arrivee. '));
                    $this->addFlash(
                        'warning',
                        'Vous avez un autre trajet pendant celui-ci !'
                    );
                    return $this->render('trajets/new.html.twig', [
                        'form' => $form->createView(),
                        'trajet' => $trajet,
                        'user' => $user,
                    ]);
                        }  
                }
            }
            // départ très près d'un autre départ; objectif: éviter un usage professionnel




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



            /*// Check if there is already a trip on the same day
            $dateDepart = $trajet->getTDepart();
            $dateArrivee= $trajet->getTArrivee();
            $existingTrips = $manager->getRepository(Trajets::class)->findBy([
                'publie' => $user,
                'T_depart' => $dateDepart
            ]);
            if (count($existingTrips) > 0) {
                $this->addFlash(
                    'error',
                    'Vous avez déjà créé un trajet pour cette date. Veuillez choisir une autre date.'
                );
                return $this->redirectToRoute('app_trajets_new');
            }
            $existingvoyage = $manager->getRepository(Trajets::class)->findBy([
                'publie' => $user,
            ]);
            foreach($existingvoyage as $trip){
                if($dateDepart->getTimestamp() <= $trip->getTArrivee()->getTimestamp()){
                    $this->addFlash(
                        'errordate',
                        'Vous avez déjà un trajet prévu avant la date de départ'
                    );
                    return $this->redirectToRoute('app_trajets_new');
                }
            }*/
            //verification si le trajet est possible
            $refus = false;
            $existingvoyage = $manager->getRepository(Trajets::class)->findBy([
                'publie' => $user,
            ]);
            foreach($existingvoyage as $voyage){
                //si heure arrivée du trajet en BDD = null on le set à HDepart +24heures
                if($voyage->getTArrivee() == 'null'){
                    $voyage->setTArrive($voyage->getTDepart()+'24 hours');
                }
                //si heure arrivée du trajet crée = null on le set à HDepart +24heures
                if($trajet->getTArrivee() == 'null'){
                    $trajet->setTArrivee($trajet->getTDepart()+'24 hours');
                }
                //verification sur les contraintes de dates
                if(($trajet->getTArrivee() < $voyage->getTDepart()) && ($voyage->getTDepart() < $voyage->getTArrivee())){
                    $refus = true;
                    $this->addFlash(
                        'errordate',
                        'Vous avez déjà un trajet prévu à cette date'
                    );
                    return $this->redirectToRoute('app_trajets_new');
                }
                
            }


            



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
        $user = $this->getUser();

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
            'user' => $user,
        ]);
    }

    
    #[Route('/{id}/modifier', name: 'app_trajets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trajets $trajet, TrajetsRepository $trajetsRepository, EntityManagerInterface $manager, NotificationService $notificationService): Response
    {
        $trajetAncien = $trajet->__toString();
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);
        $trajet = $form->getData();
        $user = $this->getUser();
        $ancienDepart = $trajet->getTDepart();
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

        $form->handleRequest($request);
        $trajet = $form->getData();
        
        //nouvelle date à moins de 24h de maintenant
        
        
       
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $trajetsRepository->save($trajet, true);
            //verification si le trajet est possible
            $refus = false;
            $existingvoyage = $manager->getRepository(Trajets::class)->findBy([
                'publie' => $user,
            ]);

            $nouveauDepart = $form['T_depart']->getData();
            if ($nouveauDepart <$demain ){
                $this->addFlash(
                    'warning',
                    'Cette date est trop tôt.'
                );
                $trajet->setTDepart($ancienDepart)  ;
                return $this->redirectToRoute('app_trajets_index');
            }

            foreach($existingvoyage as $voyage){
                //si heure arrivée du trajet en BDD = null on le set à HDepart +24heures
                if($voyage->getTArrivee() == 'null'){
                    $voyage->setTArrive($voyage->getTDepart()+'24 hours');
                }
                //si heure arrivée du trajet crée = null on le set à HDepart +24heures
                if($trajet->getTArrivee() == 'null'){
                    $trajet->setTArrivee($trajet->getTDepart()+'24 hours');
                }
                /*//verification sur les contraintes de dates (inutile pour la modification) 
                if(($trajet->getTArrivee() < $voyage->getTDepart()) && ($voyage->getTDepart() < $voyage->getTArrivee())){
                    $refus = true;
                    $this->addFlash(
                        'errordate',
                        'Vous avez déjà un trajet prévu à cette date'
                    );
                    return $this->redirectToRoute('app_trajets_index');
                }*/
                
            }

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
    

    
    #[Route('/{id}/supprimer', name: 'app_trajets_delete', methods:  ['GET', 'POST'])]
    //#[Route('/', name: 'app_trajets_index', methods: ['GET'])]
    public function delete(Request $request, Trajets $trajet, TrajetsRepository $trajetsRepository, EntityManagerInterface $manager,NotificationService $notificationService): Response
    {
        $demain = new DateTime('+24 hours');
        if ($trajet->getTDepart() <$demain ) {
            $trajet->setEtat('bloqué');
            $this->addFlash(
                'warning',
                'Vous ne pouvez plus supprimer ce trajet.'
            );
            $manager->flush();
            return $this->redirectToRoute('app_trajets_index');
        }
        

        // si le trajet est terminé ou que son départ a eu lieu il y a plus de 24h
        // conditions à écrire, avec update de $trajet.etat
        // blocage de la suppression via effacement du bouton dans trajets/index
        /*
        if ($this->isCsrfTokenValid('delete'.$trajet->getId(), $request->request->get('_token'))) {
            $trajet->setEtat('annulé');
            // si on l'enlève carrément:
            $trajetsRepository->remove($trajet, true);
        }
        */
        // COndition non fonctionnelle 23 03


        if ($trajet->getAdopte()!=null)
        {
           // supprimer les passagers en attente - table Adopte
            foreach ($trajet->getAdopte() as $adopte) 
            {//on parcourt les membres de l'entité Adopte que l'on supprime
                $manager->remove($adopte);//on supprime le tuple   

                $manager->flush();
            }

            // supprimer les passagers acceptés - table EstAccepte
            /* double emploi avec du code mis plus bas 
            foreach ($trajet->getEstAccepte() as $estAccepte) 
            {//on parcourt les membres de l'entité estAccepte que l'on supprime
                $manager->remove($estAccepte);//on supprime le tuple   

                //$manager->flush();
            }
            */
        }

        /* SUPPRESSION d'UN TRAJET PRIVE */
        // on supprime les liens entre les groupes concernés et le trajet si il est privé
        $groupes_trajet = $trajet->getGroupes();
        // si le trajet est destiné à au moins un groupe
        if (($groupes_trajet) != null ){
            foreach ($groupes_trajet as $groupe){
                $trajet->removeGroupe($groupe);
            }
        }
        

        //$manager->flush();

        /* SUPPRESSION d'UN TRAJET PUBLIC */
        //on supprime toutes les notifications liées à ce trajet 
        $notifications = $manager->getRepository(Notification::class)->findBy(['TrajetQuiEstDemande' => $trajet]);
        $listeAcceptee = $manager->getRepository(EstAccepte::class)->findBy(['trajet' => $trajet]);
        foreach ($notifications as $notification) {
            $manager->remove($notification);
        }
        $users = null;
        foreach ($listeAcceptee as $estAccepte) {
            $users[] = $estAccepte->getUtilisateur();
        }
        if($users != null){
        foreach ($users as $user) {
            $notificationService->addNotificationDeleteTrajet("Le trajet : ".$trajet->__toString(). " a été supprimé",$user);
        }
        foreach ($listeAcceptee as $estAccepte) {
            $manager->remove($estAccepte);
        }
        
        $manager->flush();
    }
        $manager->remove($trajet);
        $manager->flush();
        

        $this->addFlash(
            'success',
            'Ce trajet a été supprimé avec succès.'
        );

         
        

        return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
    }

   #[Route('/rechercher-trajet', name: 'app_trajets_search', methods: ['GET', 'POST'])]
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

    #[Route('/mes-adoptions', name: 'app_trajets_my_adoptions', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function afficher_mes_adoptions(EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        
        $trajetsEnAttente = $manager->getRepository(Adopte::class)->findBy(['utilisateur'=> $this->getUser()]);
        $trajetsInscrits = $manager->getRepository(EstAccepte::class)->findBy(['utilisateur'=> $this->getUser()]);

        return $this->render('trajets/mesAdoptions.html.twig', [
            'trajetsEnAttente' => $trajetsEnAttente,
            'trajetsInscrits' => $trajetsInscrits,
            'user' => $user,
        ]);
      
    }
}
