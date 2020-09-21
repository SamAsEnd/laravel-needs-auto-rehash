<?php

namespace SamAsEnd\NeedsAutoRehash;

use Throwable;
use UnexpectedValueException;

class UnexpectedProviderException extends UnexpectedValueException
{
    public function __construct(string $message, Throwable $e = null)
    {
        parent::__construct($message, 0, $e);
    }
}
