<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Frontend\FrontendUriBuilderInterface;

interface FrontendTemplatingRegistryInterface
{
    public function getFrontendUriBuilder(): FrontendUriBuilderInterface;

    public function setFrontendUriBuilder(FrontendUriBuilderInterface $frontendUriBuilder): void;
}
