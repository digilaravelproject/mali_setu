<?php

namespace App\Exceptions;

use RuntimeException;

class FirebaseMessagingException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?string $firebaseErrorCode = null,
        public readonly ?string $projectId = null,
        public readonly ?int $httpStatus = null,
        public readonly array $firebaseResponse = [],
    ) {
        parent::__construct($message);
    }
}
