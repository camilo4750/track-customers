<?php

namespace Internal\Shared\Exceptions;

class ApplicationLogicException extends BaseException
{
    protected $message = 'Inconsistency in execution.';
    protected $code = 500;
}