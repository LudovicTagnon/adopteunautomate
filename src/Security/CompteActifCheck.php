<?php
namespace App\Security;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CompteActifCheck implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user->getCompteActif()) {
            throw new DisabledException('Your account is disabled.');
        }
        else{
            return;
        }
    }
    
    public function checkPostAuth(UserInterface $user)
    {
        // Do nothing
    }
}

?>
