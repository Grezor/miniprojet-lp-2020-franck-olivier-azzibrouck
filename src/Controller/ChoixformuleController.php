<?php

namespace App\Controller;

use App\Entity\Choixformule;
use App\Form\ChoixformuleType;
use App\Repository\ChoixformuleRepository;
use App\Repository\FormuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/choixformule")
 */
class ChoixformuleController extends AbstractController
{
    /**
     * @Route("/", name="choixformule_index", methods={"GET","POST"})
     */
    public function index(ChoixformuleRepository $choixformuleRepository): Response
    {
        return $this->render('choixformule/index.html.twig', [
            'choixformules' => $choixformuleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="choixformule_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $choixformule = new Choixformule();
        $form = $this->createForm(ChoixformuleType::class, $choixformule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($choixformule);
            $entityManager->flush();

            return $this->redirectToRoute('choixformule_index');
        }

        return $this->render('choixformule/new.html.twig', [
            'choixformule' => $choixformule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="choixformule_show", methods={"GET"})
     */
    public function show(Choixformule $choixformule): Response
    {
        return $this->render('choixformule/show.html.twig', [
            'choixformule' => $choixformule,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="choixformule_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Choixformule $choixformule): Response
    {
        $form = $this->createFormBuilder($choixformule)
            ->add('formule')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('choixformule_index');
        }

        return $this->render('choixformule/edit.html.twig', [
            'choixformule' => $choixformule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/", name="modifier_formule", methods={"GET","POST"})
     * @param Request $request
     * @param Choixformule $choixformule
     * @param FormuleRepository $formuleRepository
     * @return Response
     */
    public function modifier_formule(Request $request, Choixformule $choixformule, FormuleRepository $formuleRepository): Response
    {
        $nouvelleTaille=0;
        //l'ancienne taille disonble
        $tailleDisponible = $choixformule->getTailleDisponible();
        //taille de la formule
        $tailleFormule=$choixformule->getFormule()->getTaille();

        //On récupère la formule qui correspond
        $formule= $formuleRepository->findOneBy(['libelle'=>$request->request->get('formule')]);

        //on vérifie si il y'a des ficiers ou pas
        if($tailleDisponible!=$tailleFormule)
        {
            //on s'assure que la formule choisie possède plus de memoire
            if($tailleDisponible>$formule->getTaille())
            {
                $this->addFlash('info', "vous devez attribuer une formule superieur à ".$formule);
                return $this->redirectToRoute('user_index');
            }
           $nouvelleTaille=$formule->getTaille()-$tailleDisponible;

        }
        if($tailleDisponible==$tailleFormule)
        {
            $nouvelleTaille= $formule->getTaille();
        }
        $choixformule->setFormule($formule);
        $choixformule->setTailleDisponible($nouvelleTaille);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($choixformule);
        $entityManager->flush();
        return $this->redirectToRoute('user_index');

    }

    /**
     * @Route("/{id}", name="choixformule_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Choixformule $choixformule): Response
    {
        if ($this->isCsrfTokenValid('delete'.$choixformule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($choixformule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('choixformule_index');
    }
}
