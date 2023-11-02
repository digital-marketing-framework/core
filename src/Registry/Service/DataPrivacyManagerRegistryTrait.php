<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

trait DataPrivacyManagerRegistryTrait
{
    protected DataPrivacyManagerInterface $dataPrivacyManager;

    public function setDataPrivacyManager(DataPrivacyManagerInterface $dataPrivacyManager): void
    {
        $this->dataPrivacyManager = $dataPrivacyManager;
    }

    public function getDataPrivacyManager(): DataPrivacyManagerInterface
    {
        if (!isset($this->dataPrivacyManager)) {
            throw new DigitalMarketingFrameworkException('No data privacy manager found!');
        }

        return $this->dataPrivacyManager;
    }
}
