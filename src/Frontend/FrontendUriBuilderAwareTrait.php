<?php

namespace DigitalMarketingFramework\Core\Frontend;

/** @phpstan-ignore-next-line This trait can be used by other packages, even though it is not used in this one. */
trait FrontendUriBuilderAwareTrait
{
    protected FrontendUriBuilderInterface $frontendUriBuilder;

    public function setFrontendUriBuilder(FrontendUriBuilderInterface $frontendUriBuilder): void
    {
        $this->frontendUriBuilder = $frontendUriBuilder;
    }
}
