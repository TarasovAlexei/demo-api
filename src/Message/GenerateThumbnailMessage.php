<?php

namespace App\Message;

class GenerateThumbnailMessage
{
    public function __construct(private string $path) {}

    public function getPath(): string { return $this->path; }
}
