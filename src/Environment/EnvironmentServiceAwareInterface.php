<?php

namespace DigitalMarketingFramework\Core\Environment;

interface EnvironmentServiceAwareInterface
{
    public function setEnvironmentService(EnvironmentServiceInterface $environmentService): void;
}
