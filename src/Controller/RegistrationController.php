<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\SecurityAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
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
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, SecurityAuthenticator $authenticator): Response
    {
        $user = new User();
        $dossier= new Dossier();
        //on récupère notre tableau de captcha
        $captcha=$this->captcha();
        //on retourne à l'utilisateur un captcha aléatoire de notre liste
        $captcha_afficher= $captcha[random_int(0,3)];
        //notre message d'erreur
        $error= "";
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(in_array($form['captcha']->getData(),$captcha,true)){

                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setRoles(["ROLE_ADMIN"]);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // generate a signed url and email it to the user
//                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
//                    (new TemplatedEmail())
//                        ->from(new Address('service_stockage@support.com', 'service stockage'))
//                        ->to($user->getEmail())
//                        ->subject('Please Confirm your Email')
//                        ->htmlTemplate('registration/confirmation_email.html.twig')
//                );
                // do anything else you need here, like send an email

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
            if(!in_array($form['captcha']->getData(),$captcha)){
               $error="le captcha ne correspond pas";

                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'captcha'=>$captcha_afficher,
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }

    /**
     * @return array
     * notre tableau de captcha
     */
    public function captcha():array
    {
        $captcha=array("Mcd1","hy3A","b7m8","kfY5");
        return $captcha;
    }
}
