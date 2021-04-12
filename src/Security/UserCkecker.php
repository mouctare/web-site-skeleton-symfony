<?php


namespace App\Security;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;


class UserCkecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
       if(!$user instanceof User) {
           return;

       }
      
    }

    public function checkPostAuth(UserInterface $user)
    {
        if(!$user instanceof User) {
            return;
           
     }
         /* Warnig, if you enter a wrong password, the exception will be displayed.  Là on empèche tout t'utilisateur n'ayant pas validé son compte de se connecter*/
     if(!$user->getIsVerified()) {
         throw new CustomUserMessageAccountStatusException("Votre compte n'est pas actif, 
         veuillez consulter vos e-mail pour l'activer avant le 
         {$user->getAccountMustBeVerifiedBefore()->format('d/m/Y à H\hi')}");

        }
    }
}