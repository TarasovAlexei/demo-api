<?php

namespace App\Validator;

use App\ApiResource\BlogPostApi;
use App\ApiResource\UserApi;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PostsAllowedAuthorChangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PostsAllowedAuthorChange) {
            throw new UnexpectedTypeException($constraint, PostsAllowedAuthorChange::class);
        }

        if (null === $value || !$value instanceof UserApi) {
            return;
        }

        $newAuthorId = $value->id;

        foreach ($value->blogPosts as $blogPostApi) {
            if (!$blogPostApi instanceof BlogPostApi) {
                continue; 
            }
            
            $originalAuthorId = $blogPostApi->author?->id;

            if (null === $originalAuthorId) {
                continue; 
            }

            if ($originalAuthorId !== $newAuthorId) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('blogPosts') 
                    ->addViolation();
                    
                return; 
            }
        }
    }
}
