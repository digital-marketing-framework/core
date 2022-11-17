<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;

trait LoggerFactoryRegistryTrait
{
    protected LoggerFactoryInterface $loggerFactory;

    public function getLoggerFactory(): LoggerFactoryInterface
    {
        return $this->loggerFactory;
    }

    public function setLoggerFactory(LoggerFactoryInterface $loggerFactory): void
    {
        $this->loggerFactory = $loggerFactory;
    }
}
