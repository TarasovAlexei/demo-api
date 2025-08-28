<?php

namespace App\Validator;

use App\ApiResource\UserApi;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidAuthorValidator extends ConstraintValidator
{
    public function __construct(private Security $security)
    {
    }

    public function validate($value, Constraint $constraint)
    {
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

        if ($value->id !== $user->getId()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}