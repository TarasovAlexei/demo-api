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
        if (!$constraint instanceof PostsAllowedAuthorChange) {
            throw new \InvalidArgumentException(sprintf('Expected type PostsAllowedAuthorChange, received %s', get_debug_type($constraint)));
        }
        if (null === $value || '' === $value) {
            return;
        }

        // meant to be used above a Collection field
        if (!$value instanceof Collection) {
            throw new \TypeError(sprintf('Expected Collection, received %s', get_debug_type($value)));
        }
        $unitOfWork = $this->entityManager->getUnitOfWork();

        foreach ($value as $blogPost) {

            if (!$blogPost instanceof BlogPost) {
                continue; 
            }

            if (!$unitOfWork->isInIdentityMap($blogPost)) {
                continue;
            }

            $originalData = $unitOfWork->getOriginalEntityData($blogPost);
            
            $originalAuthor = $originalData['author'] ?? null;
            $currentAuthor = $blogPost->getAuthor();

            if ($originalAuthor !== null && $originalAuthor !== $currentAuthor) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('[' . $value->indexOf($blogPost) . ']')
                    ->addViolation();
            }
        }
    }
}