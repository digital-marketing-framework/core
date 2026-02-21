<?php

namespace DigitalMarketingFramework\Core\Alert;

use DigitalMarketingFramework\Core\Model\Alert\AlertInterface;

class ConfigurationStorageAlertHandler extends AlertHandler
{
    public function getAlerts(): array
    {
        $alerts = [];
        $storage = $this->registry->getConfigurationDocumentStorage();
        if (!$storage->isStorageReady()) {
            $alerts[] = $this->createAlert(
                'The configuration storage folder is not available. Please check your file system configuration.',
                'Configuration Storage',
                AlertInterface::TYPE_ERROR
            );
        }

        return $alerts;
    }
}
