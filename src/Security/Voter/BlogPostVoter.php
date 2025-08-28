<?php

namespace App\Security\Voter;

use App\Entity\BlogPost;
use App\Entity\User;
use App\ApiResource\BlogPostApi;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BlogPostVoter extends Voter
{
    public const EDIT = 'EDIT';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT])
            && $subject instanceof BlogPostApi;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        assert($subject instanceof BlogPostApi);

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                if (!$this->security->isGranted('ROLE_POST_EDIT')) {
                    return false;
                }

                if ($subject->author?->id === $user->getId()) {
                    return true;
                }

                break;
        }

        return false;
    }
}