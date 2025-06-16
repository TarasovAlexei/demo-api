<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Bundle\SecurityBundle\Security;
use App\ApiResource\UserApi;

final class IsValidAuthorValidator extends ConstraintValidator
{
    public function __construct(private Security $security)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var IsValidAuthor $constraint */

        assert($constraint instanceof IsValidAuthor);


        if (null === $value || '' === $value) {
            return;
        }

        // constraint is only meant to be used above a User property
        assert($value instanceof UserApi);

        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException('IsAuthorValidator should only be used when a user is logged in.');
        }

        // TODO: implement the validation here
        if ($value->id !== $user->getId()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
