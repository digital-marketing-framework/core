<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerInterface;

interface DataPrivacyManagerRegistryInterface
{
    public function setDataPrivacyManager(DataPrivacyManagerInterface $dataPrivacyManager): void;

    public function getDataPrivacyManager(): DataPrivacyManagerInterface;
}
