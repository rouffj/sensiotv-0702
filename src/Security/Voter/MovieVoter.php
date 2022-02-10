<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MovieVoter extends Voter
{
    const BLACKLISTED_WORDS = ['shit', 'fuck', 'red'];

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['MOVIE_SHOW'])
            && $subject instanceof \App\Entity\Movie;
    }

    protected function voteOnAttribute(string $attribute, $movie, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // if (in_array($user->getConnectedCoutry(), $movie->getCountries()) {
        //     return true;
        // }

        foreach (self::BLACKLISTED_WORDS as $word) {
            // On refuse l'accès uniquement lorsque un mot-clé interdit est trouvé dans le titre du film
            if (false !== strpos(strtolower($movie->getTitle()), $word)) {
                return false;
            }
        }

        return true;
    }
}
