<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\AamcPcrsInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AamcPcrs extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof AamcPcrsInterface
            && in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }

        if (self::VIEW === $attribute) {
            return true;
        }

        return false;
    }
}
