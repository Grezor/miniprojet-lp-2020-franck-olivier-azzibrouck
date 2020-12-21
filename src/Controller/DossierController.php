<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Form\DossierType;
use App\Repository\DossierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dossier")
 */
class DossierController extends AbstractController
{
    /**
     * @Route("/", name="dossier_index", methods={"GET","POST"})
     */
    public function index(DossierRepository $dossierRepository): Response
    {
        return $this->render('dossier/index.html.twig', [
            'dossiers' => $dossierRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/", name="dossier_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user=$this->getUser();
        $dossier = new Dossier();
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dossier->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dossier);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('dossier/new.html.twig', [
            'dossier' => $dossier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="dossier_show", methods={"GET","POST"})
     * @param Request $request
     * @param Dossier $dossier
     * @return Response
     */
    public function show(Request $request,Dossier $dossier): Response
    {
        $user=$this->getUser();
        $newdossier = new Dossier();
        $form = $this->createForm(DossierType::class, $newdossier);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newdossier->setUser($user);
            $newdossier->setIdDossier($dossier);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newdossier);
            $entityManager->flush();

        }

        return $this->render('dossier/show.html.twig', [
            'dossier' => $dossier,
            'dossiers'=>$dossier->getDossiers(),
            'form' => $form->createView(),

        ]);
        return $this->render('dossier/show.html.twig', [
            'dossier' => $dossier,
            'dossiers'=>$dossier->getDossiers(),
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/{id}/edit", name="dossier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Dossier $dossier): Response
    {
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dossier_index');
        }

        return $this->render('dossier/edit.html.twig', [
            'dossier' => $dossier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="dossier_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Dossier $dossier)
    {
        $dossier_parent= $dossier->getIdDossier()->getId();
        if ($this->isCsrfTokenValid('delete'.$dossier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dossier);
            $entityManager->flush();
        }
        return $this->redirectToRoute('dossier_show',['id'=>$dossier_parent]);

    }
}
