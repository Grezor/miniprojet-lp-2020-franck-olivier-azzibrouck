<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Form\DossierType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home/", name="home",  methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request ): Response
    {
        $user=$this->getUser();
        $userDossier= $user->getDossiers()[0]->getDossiers();
        $dossier = new Dossier();
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dossier->setUser($user);
            $dossier->setIdDossier($user->getDossiers()[0]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dossier);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'dossiers'=>$userDossier,
        ]);
    }


}
