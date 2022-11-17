<?php

namespace DigitalMarketingFramework\Core\Log;

trait LoggerAwareTrait
{
    protected LoggerInterface $logger;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
