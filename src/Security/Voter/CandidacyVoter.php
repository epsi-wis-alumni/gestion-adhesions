<?php

namespace App\Security\Voter;

use App\Entity\Candidacy;
use App\Entity\User;
use COM;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CandidacyVoter extends Voter
{
    public const DELETE = 'CANDIDACY_DELETE';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELETE])) {
            return false;
        }

        // only on `Candidacy` objects
        if (!$subject instanceof Candidacy) {
            return false;
        }

        return true;   
    }

    /**
     * @param Candidacy $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny access
        if (!$user instanceof User) {
            return false;
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $candidacy = $subject;

        return match($attribute) {
            self::DELETE => $this->canDelete($candidacy, $user),
        };

        return true;
    }

    private function canDelete(Candidacy $candidacy, User $user): bool
    {
        $candidate = $candidacy->getCandidate();
        $election = $candidacy->getElection();

        if ($candidate === $user and $election->getVoteStartAt() > new \DateTimeImmutable()) {
            return true;
        }

        return false;
    }
}
