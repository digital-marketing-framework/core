<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Environment\EnvironmentServiceInterface;

interface EnvironmentRegistryInterface
{
    public function getEnvironmentService(): EnvironmentServiceInterface;

    public function setEnvironmentService(EnvironmentServiceInterface $environmentService): void;
}
