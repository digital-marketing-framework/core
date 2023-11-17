<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

interface DataPrivacyManagerAwareInterface
{
    public function setDataPrivacyManager(DataPrivacyManagerInterface $dataPrivacyManager): void;
}
