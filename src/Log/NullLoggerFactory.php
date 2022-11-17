<?php

namespace DigitalMarketingFramework\Core\Log;

class NullLoggerFactory implements LoggerFactoryInterface
{
    public function getLogger(string $forClass): LoggerInterface
    {
        return new NullLogger();
    }
}
