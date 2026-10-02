<?php

namespace DigitalMarketingFramework\Core\Environment;

trait EnvironmentServiceAwareTrait
{
    protected EnvironmentServiceInterface $environmentService;

    public function setEnvironmentService(EnvironmentServiceInterface $environmentService): void
    {
        $this->environmentService = $environmentService;
    }
}
