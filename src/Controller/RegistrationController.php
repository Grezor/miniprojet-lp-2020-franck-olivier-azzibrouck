<?php

namespace App\Controller;

use App\Entity\Choixformule;
use App\Entity\Dossier;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\FormuleRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\SecurityAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
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
                             GuardAuthenticatorHandler $guardHandler, SecurityAuthenticator $authenticator,MailerInterface $mailer): Response
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
                $user->setRoles(["ROLE_USER"]);
                // On génère un token et on l'enregistre
                $user->setActivationToken(md5(uniqid()));
                //on affecte le dossier racine à l'utilisateur
                $dossier->setLibelle("Disk");
                $dossier->setUser($user);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->persist($choixformule);
                $entityManager->persist($dossier);
                $entityManager->flush();
//
//                // generate a signed url and email it to the user
//                        $email= (new TemplatedEmail())
//                        ->from(new Address('support@stockage.com', 'admin'))
//                        ->to($user->getEmail())
//                        ->subject('Please Confirm your Email')
//                        ->htmlTemplate('registration/confirmation_email.html.twig')
//                        ->context([
//                            'token' => $user->getActivationToken(),
//                        ])
//                ;
//                try {
//                    $mailer->send($email);
//                } catch (TransportExceptionInterface $e) {
//
//                }
                //l'utilisateur se connecte
                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
            //si le captcha est différent
            if(!in_array($form['captcha']->getData(),$captcha)){
                $this->addFlash('error_captcha', 'le captcha ne correspond pas.');

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
     * @Route("/attente_email", name="attente_email", methods={"GET","POST"})
     */
    public function attente_email(): Response
    {
        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->render('registration/attente_email.html.twig', [
        ]);


    }

    /**
     * @Route("/activation/{token}", name="activation_compte")
     * @param $token
     * @param UserRepository $users
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FormuleRepository $formuleRepository
     * @param GuardAuthenticatorHandler $guardHandler
     * @param SecurityAuthenticator $authenticator
     * @return Response|null
     */
    public function activation($token, UserRepository $users, Request $request,
                               GuardAuthenticatorHandler $guardHandler, SecurityAuthenticator $authenticator)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $users->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associé à ce token
//        if(!$user){
            // On renvoie une erreur 404
//            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
//        }

        // On supprime le token
        $user->setActivationToken('');
        $user->setIsVerified(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // On génère un message
        $this->addFlash('message', 'Utilisateur activé avec succès');
        //l'utilisateur se connecte
        return $guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $authenticator,
            'main' // firewall name in security.yaml
        );

    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {

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
        return $this->redirectToRoute('home');

    }

}
