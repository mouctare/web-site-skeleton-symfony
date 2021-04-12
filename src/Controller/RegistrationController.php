<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(EntityManagerInterface $entityManager, Request $request,  Mailer $sendEmail, TokenGeneratorInterface $tokenGenerator, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
               // Si mon formulaire est soumis je vais créer un token
              $registrationToken = $tokenGenerator->generateToken();
             $user->setRegistrationToken($registrationToken);
            // encode  password
             $user->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));

             $entityManager->persist($user);

            $entityManager->flush();
            
            $sendEmail->send([
                'recipient_email' => $user->getEmail(),
                'subject' => "Vérification de votre adresse e-mail pour activer votre compte utilisateur",
                'html_template' => "registration/register_confirmation_email.html.twig",
                'context' => [
                    'userId' => $user->getId(),
                    'registrationToken' => $registrationToken,
                    'tokenLifeTime' => $user->getAccountMustBeVerifiedBefore()->format('d/m/Y à H:i')

                ]
            ]);

            $this->addFlash('success', "Votre compte utilisateur a bien été crée, veuillez consulter vos-emails pour l'activer.");
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id<\d+>}/{token}", name="app_verify_account", methods={"GET"})
     */
    public function verifyUserAccount(EntityManagerInterface $entityManager, USER $user, string $token): Response
    {
        // On fait une bonne vérification avant 
        if(($user->getRegistrationToken() === null) || ($user->getRegistrationToken() !== $token) ($this->isNotRequestedInTime($user->getAccountMustBeVerifiedBefore()))) {
            throw new AccesDeniedException();
        }

       // si c'est bon on fait
       $user->setIsVerified(true);

       $user->setAccountVerifiedAt(new \DateTimeImmutable('now'));

       $user->setRegistrationToken(null);

       $entityManager->flush();

       $this->addFlash('success', "Votre compte utilisateur est déjà présent activer , vous pouvez vous connecter !");

       return $this->redirectToRoute('app_login');
}

    private function isNotRequestedInTime(\DateTimeImmutable $accountMustBeVerifiedBefore):bool
    {
        return (new \DateTimeImmutable('now') > $accountMustBeVerifiedBefore);
    }
}
