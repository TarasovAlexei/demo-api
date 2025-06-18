<?php

namespace App\Validator;

use App\Entity\BlogPostApi;
use App\ApiResource\UserApi;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PostsAllowedAuthorChangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof PostsAllowedOwnerChange);

        if (null === $value || '' === $value) {
            return;
        }

        assert($value instanceof UserApi);

        foreach ($value->blogPosts as $blogPostApi) {
            assert($blogPostApi instanceof BlogPostApi);

            $originalAuthorId = $blogPostApi->author?->id;
            $newAuthorId = $value->id;

            if (!$originalAuthorId || $originalAuthorId === $newAuthorId) {
                return;
            }

            // the author is being changed
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}