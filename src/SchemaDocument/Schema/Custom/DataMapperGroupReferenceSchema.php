<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

class DataMapperGroupReferenceSchema extends ReferenceSchema
{
    public const TYPE = 'DATA_MAPPER_GROUP_REFERENCE';

    public function __construct(
        ?string $defaultValue = null,
        bool $required = true,
        ?string $firstEmptyOptionLabel = null
    ) {
        parent::__construct($defaultValue, $required, $firstEmptyOptionLabel);
        $this->getRenderingDefinition()->setLabel('Field Mapping');
    }

    protected function getReferencePath(): string
    {
        return sprintf('/%s/%s/*', ConfigurationInterface::KEY_DATA_PROCESSING, ConfigurationInterface::KEY_DATA_MAPPER_GROUPS);
    }

    protected function getLabel(): string
    {
        return '{key}';
    }
}
