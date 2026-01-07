<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class IsValidAuthorValidator extends ConstraintValidator
{
    public function __construct(
        private readonly Security $security 
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsValidAuthor) {
            throw new UnexpectedTypeException($constraint, IsValidAuthor::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof User) {
            throw new UnexpectedTypeException($value, User::class);
        }

        $currentUser = $this->security->getUser();

        if (!$currentUser instanceof UserInterface) {
            throw new \LogicException('IsValidAuthorValidator requires an authenticated user.');
        }


        if ($value !== $currentUser) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ user }}', $value->getUserIdentifier())
                ->addViolation();
        }
    }
}
