<?php

namespace Ark4ne\LightQueue\Exception;

class LightQueueException extends \Exception
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
