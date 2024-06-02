<?php

namespace DigitalMarketingFramework\Core\Route;

use DigitalMarketingFramework\Core\Plugin\IntegrationPluginInterface;
use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldDefinition;

interface RouteInterface extends IntegrationPluginInterface
{
    /**
     * @return array<string|FieldDefinition>
     */
    public static function getDefaultFields(): array;
}
