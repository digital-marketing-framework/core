<?php

namespace DigitalMarketingFramework\Core\Log;

interface LoggerFactoryInterface
{
    public function getLogger(string $forClass): LoggerInterface;
}
