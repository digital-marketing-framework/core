<?php

namespace DigitalMarketingFramework\Core\Alert;

use DigitalMarketingFramework\Core\Model\Alert\AlertInterface;
use DigitalMarketingFramework\Core\Utility\WebServerUtility;

/**
 * Reports the two things that can be wrong with a storage folder: it cannot be written to, or
 * its contents can be fetched over the web.
 *
 * Also the place where a folder whose access file went missing gets it back. A folder is
 * protected as it is created and as it is written to, so losing the file takes someone
 * deleting it — and this handler runs where that is reported, on the backend overview.
 * Restoring it from here costs one file check when someone looks, rather than one on every
 * request the system serves.
 */
class ConfigurationStorageAlertHandler extends AlertHandler
{
    public function getAlerts(): array
    {
        $alerts = [];

        $configurationStorage = $this->registry->getConfigurationDocumentStorage();
        $configurationStorage->protectStorageFolder();
        if (!$configurationStorage->isStorageReady()) {
            $alerts[] = $this->unwriteable('Configuration Storage', 'Configuration documents');
        } elseif (!$configurationStorage->isStorageProtected()) {
            $alerts[] = $this->exposed('Configuration Storage', 'Configuration documents');
        }

        $fieldDefinitionStorage = $this->registry->getFieldDefinitionStorage();

        // Unconfigured is not broken: definitions shipped by packages still work, only editing
        // them does not.
        if ($fieldDefinitionStorage->getStorageFolder() === '') {
            return $alerts;
        }

        $fieldDefinitionStorage->protectStorageFolder();
        if (!$fieldDefinitionStorage->isStorageReady()) {
            $alerts[] = $this->unwriteable('Field Definition Storage', 'Field definitions');
        } elseif (!$fieldDefinitionStorage->isStorageProtected()) {
            $alerts[] = $this->exposed('Field Definition Storage', 'Field definitions');
        }

        return $alerts;
    }

    protected function unwriteable(string $title, string $subject): AlertInterface
    {
        return $this->createAlert(
            sprintf('%s cannot be stored: the configured folder is not writeable. Please check your file system configuration.', $subject),
            $title,
            AlertInterface::TYPE_ERROR
        );
    }

    /**
     * The advice differs by server, because on anything but Apache there is no file to write
     * and telling someone to add one would send them after something that cannot work.
     */
    protected function exposed(string $title, string $subject): AlertInterface
    {
        $advice = WebServerUtility::supportsAccessFile()
            ? 'Move them to a file storage that is not public.'
            : 'This web server ignores access files, so they can only be protected by moving them to a file storage that is not public, or by denying access to the folder in the server configuration.';

        return $this->createAlert(
            sprintf('%s are stored in a location that can be fetched over the web. %s', $subject, $advice),
            $title,
            AlertInterface::TYPE_WARNING
        );
    }
}
