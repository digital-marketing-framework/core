<?php

namespace DigitalMarketingFramework\Core\Log;

interface LoggerAwareInterface
{
    public function setLogger(LoggerInterface $logger): void;
}
