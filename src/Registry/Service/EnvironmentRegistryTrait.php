<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Environment\EnvironmentService;
use DigitalMarketingFramework\Core\Environment\EnvironmentServiceInterface;

trait EnvironmentRegistryTrait
{
    protected EnvironmentServiceInterface $environmentService;

    public function getEnvironmentService(): EnvironmentServiceInterface
    {
        if (!isset($this->environmentService)) {
            $this->environmentService = $this->createObject(EnvironmentService::class);
        }

        return $this->environmentService;
    }

    public function setEnvironmentService(EnvironmentServiceInterface $environmentService): void
    {
        $this->environmentService = $environmentService;
    }
}
