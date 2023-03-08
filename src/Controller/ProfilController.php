<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Error;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormError;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use App\Entity\Utilisateurs;
use App\Form\PassProfileFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\Security\PasswordHasherConfig;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\AsciiSlugger;



class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil')]
    public function index(): Response
    {
        //recup les infos 
        $user = $this->getUser();

        return $this->render('profil/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profil/modifier', name: 'modif_profil')]
    public function editProfile(Request $request,EntityManagerInterface $entityManager, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher):Response
    {
        $user = $this->getUser(); //on obtient user connecté
        $form = $this->createForm(UserProfileFormType::class,$user); //creation formulaire avec données user
        $form_mdp = $this->createForm(PassProfileFormType::class, $user); //creation formulaire modif mdp
        $form->handleRequest($request);
        $form_mdp->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $form['imageFile']->getData();
            if($uploadedFile){ //si un fichier est chargé alors on va l'enregistrer
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $slugger = new AsciiSlugger();
            $newFilename = $slugger->slug($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
            $user->setFichierPhoto($newFilename);
        }
            $user = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil modifié avec succès !');

            return $this->redirectToRoute('profil');

            
        }else {
            // affichage des erreurs
            $errors = $form->getErrors(true, false);
            foreach ($errors as $error) {
                foreach ($error as $e) {
                    echo $e->getMessage() . "<br>";
                }
            }
        }
        if($form_mdp->isSubmitted() && $form_mdp->isValid()){
            $user = $form_mdp->getData();
            $oldpassword = $form_mdp->get('oldPassword')->getData();
            if(!$userPasswordHasher->isPasswordValid($user,$oldpassword)){
                $form_mdp->get('oldPassword')->addError(new FormError('Le mot de passe actuel est incorrect.'));
            }else {
                $user->setPassword($userPasswordHasher->hashPassword($user, $form_mdp->get('plainPassword')->getData()));
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
    
                $this->addFlash('success', 'Mot de passe modifié avec succès !');
    
                return $this->redirectToRoute('profil');

            }
        }else{
            //TODO : afficher les messages d'erreurs du form mot de passe
            $errors = $form->getErrors(true, false);
            foreach ($errors as $error) {
                foreach ($error as $e) {
                    echo $e->getMessage() . "<br>";
                }
            }
        }
        

        return $this->render('profil/modifierProfile.html.twig', [
            'form' => $form->createView(),
            'form_mdp' => $form_mdp->createView(),
        ]);
    }

    #[Route('/profil/supprimer', name: 'supprimer_profil')]
    public function desactivateUser(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher): Response 
    { 
        // Obtenir l'utilisateur connecté 
        $user = $this->getUser();
        $user->setCompteActif(false); //on passe à faux pour désactiver le compte
        $entityManager->flush();

        $this->addFlash('success', 'Profil désactivé avec succès !'); 
        $request->getSession()->invalidate();
        $this->container->get('security.token_storage')->setToken(null);
        return $this->redirectToRoute('app_home'); // Redirect to homepage
    }
    
}
