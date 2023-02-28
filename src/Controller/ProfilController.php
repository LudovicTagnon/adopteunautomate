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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class ProfilController extends AbstractController
{
    #[Security("is_granted('ROLE_USER') and user === client")]
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

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil modifié avec succès !');

            return $this->redirectToRoute('profil');
        }

        return $this->render('profil/index.html.twig', ['form' => $form->createView(),]);
    }


   
}
