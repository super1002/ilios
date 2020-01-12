<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\IngestionExceptionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class IngestionException extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof IngestionExceptionInterface
            && in_array($attribute, [self::VIEW]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                return $user->performsNonLearnerFunction();
                break;
        }

        return false;
    }
}
