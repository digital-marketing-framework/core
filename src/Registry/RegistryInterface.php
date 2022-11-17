<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\RequestRegistryInterface;

interface RegistryInterface extends LoggerFactoryRegistryInterface, RequestRegistryInterface
{
}
