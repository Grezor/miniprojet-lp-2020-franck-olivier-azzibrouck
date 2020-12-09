<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Entity\Fichier;
use App\Form\FichierType;
use App\Repository\FichierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/fichier")
 */
class FichierController extends AbstractController
{
    /**
     * @Route("/", name="fichier_index", methods={"GET"})
     */
    public function index(FichierRepository $fichierRepository): Response
    {
        return $this->render('fichier/index.html.twig', [
            'fichiers' => $fichierRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="fichier_new", methods={"GET","POST"})
     * @param Request $request
     * @param Dossier $dossier
     * @return Response
     */
    public function new(Request $request,Dossier $dossier): Response
    {
        $fichier = new Fichier();
        $form = $this->createForm(FichierType::class, $fichier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $document */
            $document = $form['libelle']->getData();

            if ($document)
            {
               try
                {
                    $document->move($this->getParameter('document_directory'));
                }
                catch (FileException $e)
                {

                }
                $fichier->setDossier($dossier);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($fichier);
                $entityManager->flush();
                return $this->redirectToRoute('fichier_index');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fichier);
            $entityManager->flush();

            return $this->redirectToRoute('fichier_index');
        }

        return $this->render('fichier/new.html.twig', [
            'fichier' => $fichier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fichier_show", methods={"GET"})
     */
    public function show(Fichier $fichier): Response
    {
        return $this->render('fichier/show.html.twig', [
            'fichier' => $fichier,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="fichier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Fichier $fichier): Response
    {
        $form = $this->createForm(FichierType::class, $fichier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('fichier_index');
        }

        return $this->render('fichier/edit.html.twig', [
            'fichier' => $fichier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fichier_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Fichier $fichier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fichier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fichier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('fichier_index');
    }
}
