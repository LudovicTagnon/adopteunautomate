<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestFormType;

use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\DisabledException;
use App\Service\SendMailService;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }



    #[Route('/oubli-pass' , name:'forgot_password')]
    public function forgotPassword(Request $request , UtilisateursRepository $utilisateursRepository , TokenGeneratorInterface $tokenGenerator,
                                   EntityManagerInterface $entityManager, SendMailService $mail): Response
    {

        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $utilisateursRepository->findOneByEmail($form->get('email')->getData());

            if($user){
                // generer un token
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                 // le lien
                $url = $this->generateUrl('reset_pass' , ['token'=> $token],
                UrlGeneratorInterface::ABSOLUTE_URL);

                // donne mail
                $context = compact('url','user');

                // envoie mail
                $mail->send(
                    'no-reply@adopteunautomate.com',
                    $user->getEmail(),
                    'réinitialisation dde mot de passe',
                    'password_reset',
                    $context
                );
                $this->addFlash('success' , 'email envoyé avec succes');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('danger', 'Email not found');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot_password.html.twig' , [
                'requestPassForm' => $form->createView()
            ]);
    }


    #[Route('/oublie-pass/{token}',name:'reset_pass')]
    public function resetPass(string $token , Request $request , UtilisateursRepository $utilisateursRepository ,
                              EntityManagerInterface $entityManager , UserPasswordHasherInterface $passwordHasher ): Response
    {


    }
}
