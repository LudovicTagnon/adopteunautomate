<?php

namespace App\Controller;

use App\Entity\Groupes;
use App\Form\GroupesType;
use App\Entity\Utilisateurs;
use App\Repository\GroupesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupesController extends AbstractController
{
    #[Route('/groupes', name: 'app_groupes.index')]
    public function index(GroupesRepository $repository): Response
    {
        $groupes = $repository->findAll();

        return $this->render('groupes/index.html.twig', [
            'groupes' => $groupes
        ]);
    }

    #[Route('/groupes/nouveau', name: 'app_groupes.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $groupe = new Groupes();
        $form = $this->createForm(GroupesType::class, $groupe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $groupe = $form->getData();
            $manager->persist($groupe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre groupe a été créé avec succès!'
            );

            return $this->redirectToRoute('security_homepage');
        }

        return $this->render('groupes/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/groupes/edition/{id}', name: 'app_groupes.edit', methods: ['GET', 'POST'])]
    public function edit(Groupes $groupe, Request $request, EntityManagerInterface $manager): Response
    {
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

            return $this->redirectToRoute('app_groupes.index');
        }

        return $this->render('groupes/edit.html.twig', [
            'form' => $form->createView(),
            'groupe' => $groupe
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

            return $this->redirectToRoute('app_groupes.index');
        }

        $manager->remove($groupe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Ce groupe a été supprimé avec succès.'
        );

        return $this->redirectToRoute('app_groupes.index');
    }
}
