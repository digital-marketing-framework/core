<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

trait DataPrivacyManagerAwareTrait
{
    protected DataPrivacyManagerInterface $dataPrivacyManager;

    public function setDataPrivacyManager(DataPrivacyManagerInterface $dataPrivacyManager): void
    {
        $this->dataPrivacyManager = $dataPrivacyManager;
    }
}
