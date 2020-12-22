<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Entity\Fichier;
use App\Form\DossierType;
use App\Form\FichierType;
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
        $fichiers= $dossier->getFichiers();
        $choixformule= $user->getChoixformules()[0];
        $fichier = new Fichier();

        //formulaire des dossiers
        $form1 = $this->createForm(DossierType::class, $newdossier);
        $form1->handleRequest($request);
        //formulaire des fichiers
        $form2 = $this->createForm(FichierType::class, $fichier);
        $form2->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) {
            $newdossier->setUser($user);
            $newdossier->setIdDossier($dossier);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newdossier);
            $entityManager->flush();

            return $this->render('dossier/show.html.twig', [
                'dossier' => $dossier,
                'fichiers'=>$fichiers,
                'dossiers'=>$dossier->getDossiers(),
                'form1' => $form1->createView(),
                'form2' => $form2->createView(),

            ]);

        }

        if ($form2->isSubmitted() && $form2->isValid()) {
            /** @var UploadedFile $document */
            $document = $form2['libelle']->getData();

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
                $fichier->setDossier($dossier);
                $choixformule->setTailleDisponible($nouvelle_taille_disponible);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($fichier,$choixformule);
                $entityManager->flush();

                return $this->render('dossier/show.html.twig', [
                    'dossier' => $dossier,
                    'fichiers'=>$fichiers,
                    'dossiers'=>$dossier->getDossiers(),
                    'form1' => $form1->createView(),
                    'form2' => $form2->createView(),

                ]);
            }
            return $this->render('dossier/show.html.twig', [
                'dossier' => $dossier,
                'fichiers'=>$fichiers,
                'dossiers'=>$dossier->getDossiers(),
                'form1' => $form1->createView(),
                'form2' => $form2->createView(),

            ]);

        }

        return $this->render('dossier/show.html.twig', [
            'dossier' => $dossier,
            'fichiers'=>$fichiers,
            'dossiers'=>$dossier->getDossiers(),
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),

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
        //on récupère l'utilisateur connecté
        $user= $this->getUser();
        //on récupère la formule de l'utilisateur
        $choixformule= $user->getChoixformules()[0];
        //on récupère l'id du repertoire parent
        $dossier_parent= $dossier->getIdDossier()->getId();
        //on supprime tout les fichiers dans le dossier
        //on parcourt tous nos fichiers
        if ($dossier->getFichiers()[0]!=null)
        {
            for ($i=0;$i<= count($dossier->getFichiers());$i++)
            {

                $choixformule->setTailleDisponible($dossier->getFichiers()[$i]->getTaille()+ $choixformule->getTailleDisponible());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($choixformule);
                $entityManager->remove($dossier->getFichiers()[$i]);
                $entityManager->flush();
            }
        }

        if ($this->isCsrfTokenValid('delete'.$dossier->getId(), $request->request->get('_token')))
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dossier);
            $entityManager->flush();
        }
        return $this->redirectToRoute('dossier_show',['id'=>$dossier_parent]);

    }


    function convertisseur($octet)
    {
//        // Array contenant les differents unités
//        $unite = array('octet','ko','Mo','go');
        $mo = round($octet/(1024*1024),2);
        return $mo;

    }
}
