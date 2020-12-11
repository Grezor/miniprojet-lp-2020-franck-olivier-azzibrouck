<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavbarController extends AbstractController
{
    /**
     * @Route("/sidebar", name="sidebar")
     */
    public function sidebar(): Response
    {
        $user=$this->getUser();
        $userDossier= $user->getDossiers()[0]->getDossiers();
        return $this->render('navbar/sidebar.html.twig', [
            'controller_name' => 'NavbarController',
            'dossiers'=>$userDossier
        ]);
    }

    /**
     * @Route("/topbar", name="topbar", methods={"GET","POST"})
     */
    public function topbar(): Response
    {
        return $this->render('navbar/topbar.html.twig', [
            'controller_name' => 'topbarController',
        ]);
    }


}
