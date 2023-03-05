<?php

namespace App\Controller;

use App\Entity\Groupes;
use App\Entity\Utilisateurs;
use App\Entity\EstDans;
use App\Repository\EstDansRepository;
use App\Form\GroupesType;
use App\Repository\GroupesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupesController extends AbstractController
{
    #[Route('/groupes', name: 'app_groupes.group')]

    public function index(GroupesRepository $repository): Response
    {
        $groupes = $repository->findBy(
            ['createur'=> $this->getUser()]
        );

        return $this->render('groupes/group.html.twig', [
            'groupes' => $groupes
        ]);
    }

    #[Route('/groupes/nouveau', name: 'app_groupes.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $groupe = new Groupes();
        
        $form = $this->createForm(GroupesType::class, $groupe,);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $groupe = $form->getData();
            $groupe->setCreateur($this->getUser());
            $manager->persist($groupe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre groupe a été créé avec succès!'
            );

            return $this->redirectToRoute('app_groupes.group');
        }

        return $this->render('groupes/new_group.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/groupes/edition/{id}', name: 'app_groupes.edit', methods: ['GET', 'POST'])]
    public function edit(Groupes $groupe, Request $request, EntityManagerInterface $manager): Response
    {
        $current_user = $this->getUser();
        $utilisateurs = $manager->getRepository(Utilisateurs::class)->findAll();
        $utilisateursDejaDedans = $manager->getRepository(EstDans::class)->findAllUtilisateur($groupe);
        $form = $this->createForm(GroupesType::class, $groupe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $groupe = $form->getData();
            $manager->persist($groupe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre groupe a été modifié avec succès!'
            );

            return $this->redirectToRoute('app_groupes.group');
        }

        return $this->render('groupes/edit_group.html.twig', [
            'form' => $form->createView(),
            'groupe' => $groupe,
            'utilisateur_actuel' => $current_user,
            'utilisateurs' => $utilisateurs,
            'utilisateursDejaDedans' => $utilisateursDejaDedans,
        ]);
    }

    #[Route('/groupes/suppression/{id}', name: 'app_groupes.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Groupes $groupe): Response
    {
        if (!$groupe) {
            $this->addFlash(
                'warning',
                'Ce groupe n\'a pas été trouvé.'
            );

            return $this->redirectToRoute('app_groupes.group');
        }

        $manager->remove($groupe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Ce groupe a été supprimé avec succès.'
        );

        return $this->redirectToRoute('app_groupes.group');
    }
}
