<?php

namespace DigitalMarketingFramework\Core\Frontend;

interface FrontendUriBuilderAwareInterface
{
    public function setFrontendUriBuilder(FrontendUriBuilderInterface $frontendUriBuilder): void;
}
