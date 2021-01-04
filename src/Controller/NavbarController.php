<?php

namespace App\Controller;

use App\Entity\Choixformule;
use App\Repository\FormuleRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class NavbarController extends AbstractController
{
    /**
     * @Route("/sidebar", name="sidebar")
     */
    public function sidebar(): Response
    {
        $user=$this->getUser();
        $userfichier= $user->getDossiers()[0]->getFichiers();
        $tailleDisponible= $user->getChoixformules()[0]->getTailleDisponible();
        $tailleFormule= $user->getChoixformules()[0]->getFormule()->getTaille();
        $userDossier= $user->getDossiers()[0]->getDossiers();
        return $this->render('navbar/sidebar.html.twig', [
            'controller_name' => 'NavbarController',
            'dossiers'=>$userDossier,
            'taille_disponible'=>$tailleDisponible,
            'taille_formule'=>$tailleFormule,
            'fichiers'=>$userfichier,
        ]);
    }

    /**
     * @Route("/topbar", name="topbar", methods={"GET","POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param FormuleRepository $formuleRepository
     * @return Response
     */
    public function topbar(Request $request, UserRepository $userRepository, FormuleRepository $formuleRepository,MailerInterface $mailer): Response
    {
        //on récupère le user connecté
        $user= $this->getUser();
        //on récupère le choix de formule souhaité
        $formule= $request->request->get('formule');
        if($formule!=null)
        {
            // Email au client
            $email1= (new TemplatedEmail())
                ->from(new Address('support@stockage.com', 'admin'))
                ->to($user->getEmail())
                ->subject('Modification de la formule')
                ->htmlTemplate('Email/modification_formule_client.html.twig')
                ->context([
                    'formule' => $formule,
                ])
            ;
            //email à l'administrateur
            $email2= (new TemplatedEmail())
                ->from(new Address($user->getEmail(), $user->getUsername()))
                ->to('support@stockage.com')
                ->subject('Modification de la formule')
                ->htmlTemplate('Email/modification_formule_admin.html.twig')
                ->context([
                    'formule' => $formule,
                    'client'=>$user
                ])
            ;
            try {
                $mailer->send($email1);
                $mailer->send($email2);

            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
            }
            return $this->redirectToRoute('home');
            $this->addFlash('success', 'Votre demande à été prise en compte, et elle sera traitée dans les plus brefs delais.');

        }

        return $this->render('navbar/topbar.html.twig', [
            'controller_name' => 'topbarController',
            'users'=>$userRepository->findAll(),
            'formules'=>$formuleRepository->findAll()
        ]);
    }


}
