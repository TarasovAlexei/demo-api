<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class PostsAllowedAuthorChange extends Constraint
{
    
    public string $message = 'You cannot assign a post to another user.';
    public string $mode;

    public function __construct(
        string $mode = 'strict',
        ?array $groups = null,
        mixed $payload = null,
        ?array $options = null
    ) {
        $this->mode = $options['mode'] ?? $mode;

        parent::__construct($options, $groups, $payload);
    }
}
