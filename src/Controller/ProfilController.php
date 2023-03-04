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

    public function editProfile(Request $request,EntityManagerInterface $entityManager, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher):Response
    {
        $user = $this->getUser(); //on obtient user connecté
        $form = $this->createForm(UserProfileFormType::class,$user); //creation formulaire avec données user
        $form_mdp = $this->createForm(PassProfileFormType::class, $user); //creation formulaire modif mdp
        $form->handleRequest($request);
        $form_mdp->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil modifié avec succès !');

            return $this->redirectToRoute('profil');

            
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
        }

        return $this->render('profil/index.html.twig', [
            'form' => $form->createView(),
            'form_mdp' => $form_mdp->createView(),
        ]);
    }

}
