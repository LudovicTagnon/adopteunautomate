<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $user = new Utilisateurs();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes inscrits ! Vous pouvez désormais vous connecter!');

            return $this->redirectToRoute('app_home');
        } 

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
