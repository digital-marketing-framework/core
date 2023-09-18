<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;

interface LoggerFactoryRegistryInterface
{
    public function getLoggerFactory(): LoggerFactoryInterface;

    public function setLoggerFactory(LoggerFactoryInterface $loggerFactory): void;
}
