<?php

namespace App\Controller;

use App\Entity\Email;
use App\Form\EmailType;
use App\Repository\EmailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/email")
 */
class EmailController extends AbstractController
{
    /**
     * @Route("/", name="email_index", methods={"GET"})
     */
    public function index(EmailRepository $emailRepository): Response
    {
        return $this->render('email/index.html.twig', [
            'emails' => $emailRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="email_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $email = new Email();
        $form = $this->createForm(EmailType::class, $email);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($email);
            $entityManager->flush();

            return $this->redirectToRoute('email_index');
        }

        return $this->render('email/new.html.twig', [
            'email' => $email,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="email_show", methods={"POST","GET"})
     * @param Email $email
     * @return RedirectResponse
     */
    public function show(Email $email):Response
    {
        $user= $this->getUser();
        $email->setStatut(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($email);
        $entityManager->flush();
        if($user->getRoles()[0]=="ROLE_ADMIN")
        {
            return $this->redirectToRoute('user_index');
        }
        return $this->redirectToRoute('home');


    }

    /**
     * @Route("/{id}/edit", name="email_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Email $email): Response
    {
        $form = $this->createForm(EmailType::class, $email);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('email_index');
        }

        return $this->render('email/edit.html.twig', [
            'email' => $email,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="email_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Email $email): Response
    {
        if ($this->isCsrfTokenValid('delete'.$email->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($email);
            $entityManager->flush();
        }

        return $this->redirectToRoute('email_index');
    }
}
