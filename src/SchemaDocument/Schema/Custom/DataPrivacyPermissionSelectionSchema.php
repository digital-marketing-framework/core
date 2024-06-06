<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class DataPrivacyPermissionSelectionSchema extends StringSchema
{
    public const TYPE = 'DATA_PRIVACY_PERMISSION_SELECTION';

    public const VALUE_SET_PERMISSION_ALL = 'permission/all';

    /**
     * @param array<string,string> $permissions
     */
    public function __construct(array $permissions)
    {
        parent::__construct();

        foreach ($permissions as $permission => $label) {
            $this->addValueToValueSet(static::VALUE_SET_PERMISSION_ALL, $permission, $label);
        }

        $this->getAllowedValues()->addValueSet(static::VALUE_SET_PERMISSION_ALL);
        $this->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
    }
}
