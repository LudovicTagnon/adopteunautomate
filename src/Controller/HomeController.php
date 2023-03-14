<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Villes;
use App\Entity\Trajets;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $manager,MailerInterface $mailer): Response
    {
        $villes = $manager->getRepository(Villes::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'villes' => $villes,
        ]);

    }
}
