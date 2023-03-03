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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\Security\PasswordHasherConfig;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;



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

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $oldpassword = $form->get('oldPassword')->getData();
            if(!$userPasswordHasher->isPasswordValid($user,$oldpassword)){
                $form->get('oldPassword')->addError(new FormError('Le mot de passe actuel est incorrect.'));
            }else {
                $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
    
                $this->addFlash('success', 'Profil modifié avec succès !');
    
                return $this->redirectToRoute('profil');

            }
        }

        return $this->render('profil/modifierProfile.html.twig', ['form' => $form->createView(),]);
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
