<?php

namespace Internal\Shared\Exceptions;

use Throwable;

class BaseException extends \Exception
{
    protected array $errors = [];

    public function __construct(
        array $errors = [],
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->errors = $errors;
        parent::__construct($message ?? $this->message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
