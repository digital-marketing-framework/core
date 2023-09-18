<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;
use DigitalMarketingFramework\Core\Log\NullLoggerFactory;

trait LoggerFactoryRegistryTrait
{
    protected LoggerFactoryInterface $loggerFactory;

    public function getLoggerFactory(): LoggerFactoryInterface
    {
        if (!isset($this->loggerFactory)) {
            $this->loggerFactory = new NullLoggerFactory();
        }

        return $this->loggerFactory;
    }

    public function setLoggerFactory(LoggerFactoryInterface $loggerFactory): void
    {
        $this->loggerFactory = $loggerFactory;
    }
}
