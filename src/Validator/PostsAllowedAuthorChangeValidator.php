<?php

namespace App\Validator;

use App\Entity\BlogPost;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PostsAllowedAuthorChangeValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof PostsAllowedAuthorChange);

        if (null === $value || '' === $value) {
            return;
        }

        // meant to be used above a Collection field
        assert($value instanceof Collection);

        $unitOfWork = $this->entityManager->getUnitOfWork();
        foreach ($value as $blogPost) {
            assert($blogPost instanceof BlogPost);

            $originalData = $unitOfWork->getOriginalEntityData($blogPost);
            $originalAuthorId = $originalData['author_id'];
            $newAuthorId = $blogPost->getAuthor()->getId();

            if (!$originalAuthorId || $originalAuthorId === $newAuthorId) {
                return;
            }

            // the author is being changed
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}