<?php

namespace App\Validator;

use App\ApiResource\UserApi;
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
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsValidAuthor) {
            throw new UnexpectedTypeException($constraint, IsValidAuthor::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof UserApi) {
            throw new UnexpectedTypeException($value, UserApi::class);
        }

        $currentUser = $this->security->getUser();

        if (!$currentUser instanceof User) {
            $this->context->buildViolation('You must be authenticated.')
                ->addViolation();
            return;
        }

        if ($value->id !== $currentUser->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ user }}', $value->id) 
                ->addViolation();
        }
    }
}

