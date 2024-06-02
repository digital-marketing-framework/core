<?php

namespace DigitalMarketingFramework\Core\Api;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use Throwable;

class ApiException extends DigitalMarketingFrameworkException
{
    public function __construct(string $message = '', int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
