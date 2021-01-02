<?php

namespace App\Controller;

use App\Entity\Choixformule;
use App\Entity\Dossier;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\FormuleRepository;
use App\Security\EmailVerifier;
use App\Security\SecurityAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FormuleRepository $formuleRepository
     * @param GuardAuthenticatorHandler $guardHandler
     * @param SecurityAuthenticator $authenticator
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, FormuleRepository $formuleRepository,
                             GuardAuthenticatorHandler $guardHandler, SecurityAuthenticator $authenticator): Response
    {
        $user = new User();
        //on crée le dossier racine du projet
        $dossier= new Dossier();
        //on récupère notre tableau de captcha
        $captcha=$user->captcha();
        //on retourne à l'utilisateur un captcha aléatoire de notre liste
        $captcha_afficher= $captcha[random_int(0,3)];
        //notre message d'erreur pour le captcha
        $error= "";
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            //si le captcha est valide
            if(in_array($form['captcha']->getData(),$captcha,true))
            {

                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                //on récupere la formule en fanction de l'id
                $formule= $formuleRepository->findOneBy(['id'=>$form['formule']->getData()]);
                //On ajoute le choix de la formule
                $choixformule= new Choixformule($user,$formule);
                $choixformule->setTailleDisponible($formule->getTaille());
                //on attribut le role à l'utilisateur
                $user->setRoles(["ROLE_ADMIN"]);
                //on affecte le dossier racine à l'utilisateur
                $dossier->setLibelle("Disk");
                $dossier->setUser($user);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->persist($choixformule);
                $entityManager->persist($dossier);
                $entityManager->flush();

                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('support@stockage.com', 'admin'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );
                // do anything else you need here, like send an email

                return $this->redirectToRoute('attente_email');
            }
            //si le captcha est différent
            if(!in_array($form['captcha']->getData(),$captcha)){
                $error="le captcha ne correspond pas";

                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'error'=>$error
                ]);
            }
        }


        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'captcha'=>$captcha_afficher,
            'error'=>$error

        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $user=$this->getUser();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try
        {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        }
        catch (VerifyEmailExceptionInterface $exception)
        {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        return $this->redirectToRoute('app_register');

    }

    /**
     * @Route("/attente_email", name="attente_email", methods={"GET","POST"})
     */
    public function attente_email(): Response
    {
        return $this->render('registration/attente_email.html.twig', [
        ]);
    }

}
