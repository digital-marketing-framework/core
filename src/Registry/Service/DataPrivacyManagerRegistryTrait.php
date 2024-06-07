<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManager;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerInterface;

trait DataPrivacyManagerRegistryTrait
{
    protected DataPrivacyManagerInterface $dataPrivacyManager;

    /**
     * @template ClassName of object
     *
     * @param class-string<ClassName> $class
     *
     * @return ClassName
     */
    abstract public function createObject(string $class): object;

    public function setDataPrivacyManager(DataPrivacyManagerInterface $dataPrivacyManager): void
    {
        $this->dataPrivacyManager = $dataPrivacyManager;
    }

    public function getDataPrivacyManager(): DataPrivacyManagerInterface
    {
        if (!isset($this->dataPrivacyManager)) {
            $this->dataPrivacyManager = $this->createObject(DataPrivacyManager::class);
        }

        return $this->dataPrivacyManager;
    }
}
