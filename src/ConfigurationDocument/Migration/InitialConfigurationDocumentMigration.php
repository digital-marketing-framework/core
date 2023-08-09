<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Migration;

abstract class InitialConfigurationDocumentMigration extends ConfigurationDocumentMigration
{
    public function getSourceVersion(): string
    {
        return '';
    }
}
