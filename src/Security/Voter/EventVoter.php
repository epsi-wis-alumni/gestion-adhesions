<?php

namespace App\Security\Voter;

use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class EventVoter extends Voter
{
    public const VIEW = 'EVENT_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW])
            && $subject instanceof \App\Entity\Event;
    }

    /**
     * @param Event $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject->isPrivate()) {
            return true;
        }
        
        $user = $token->getUser();
        if (!$user) {
            return false;
        }

        return true;
    }
}
