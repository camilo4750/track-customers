<?php

namespace Internal\Shared\Exceptions;

class BusinessLogicException extends BaseException
{
    protected $message = 'Inconsistency in business rule.';
    protected $code = 422;
}
