<?php

namespace App\Controller;

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
     * @Route("/", name="fichier_index", methods={"GET","POST"})
     */
    public function index(FichierRepository $fichierRepository): Response
    {
        return $this->render('fichier/index.html.twig', [
            'fichiers' => $fichierRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="fichier_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user= $this->getUser();
        $choixformule= $user->getChoixformules()[0];
        $fichier = new Fichier();
        $form = $this->createForm(FichierType::class, $fichier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $document */
            $document = $form['libelle']->getData();

            if ($document)
            {
                //on récupère la taille du fichier et on la convertie en Mo
                $filetaille=$this->convertisseur(filesize($document));
                //On récupère la taille de disponible dans notre memoire
                $taille_disponible= $choixformule->getTailleDisponible();
                $nouvelle_taille_disponible =  $taille_disponible - $filetaille ;

                //on récupere le nom du document uploader
                $originalFilename = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $originalFilename.'.'.$document->guessExtension();

                try
                {
                    //on stock le document dans le repertoire approprié
                    $document->move($this->getParameter('document_directory'),$newFilename );
                }
                catch (FileException $e)
                {

                }

                $fichier->setLibelle($newFilename);
                $fichier->setTaille($filetaille);
                $fichier->setDossier($user->getDossiers()[0]);
                $choixformule->setTailleDisponible($nouvelle_taille_disponible);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($fichier,$choixformule);
                $entityManager->flush();

                return $this->redirectToRoute('home');
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
        $file= $fichier->getLibelle();
        $path= $this->getParameter('document_directory');
        return $this->file("$path/$file");
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


    /**
     * @param $octet
     * @return string
     * cette fonction sert à convertir la taille des fichiers en Mo
     */
    function convertisseur($octet)
    {
//        // Array contenant les differents unités
//        $unite = array('octet','ko','Mo','go');
        $mo = round($octet/(1024*1024),2);
        return $mo;

    }



}
