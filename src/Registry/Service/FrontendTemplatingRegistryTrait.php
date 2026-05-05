<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Frontend\FrontendUriBuilderInterface;
use DigitalMarketingFramework\Core\Frontend\PassthroughFrontendUriBuilder;

trait FrontendTemplatingRegistryTrait
{
    protected FrontendUriBuilderInterface $frontendUriBuilder;

    public function getFrontendUriBuilder(): FrontendUriBuilderInterface
    {
        if (!isset($this->frontendUriBuilder)) {
            $this->frontendUriBuilder = $this->createObject(PassthroughFrontendUriBuilder::class);
        }

        return $this->frontendUriBuilder;
    }

    public function setFrontendUriBuilder(FrontendUriBuilderInterface $frontendUriBuilder): void
    {
        $this->frontendUriBuilder = $frontendUriBuilder;
    }
}
