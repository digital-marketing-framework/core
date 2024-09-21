<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Utility\MapUtility;

class ConditionReferenceSchema extends ReferenceSchema
{
    public const TYPE = 'CONDITION_REFERENCE';

    public function __construct(
        ?string $defaultValue = null,
        bool $required = true,
        ?string $firstEmptyOptionLabel = null,
    ) {
        parent::__construct($defaultValue, $required, $firstEmptyOptionLabel);
        $this->getRenderingDefinition()->setLabel('Condition');
        $this->getRenderingDefinition()->addReference(
            sprintf('/%s/%s/{.}/%s', ConfigurationInterface::KEY_DATA_PROCESSING, ConfigurationInterface::KEY_CONDITIONS, MapUtility::KEY_VALUE),
            sprintf('{../%s}', MapUtility::KEY_KEY)
        );
    }

    protected function getReferencePath(): string
    {
        return sprintf('/%s/%s/*', ConfigurationInterface::KEY_DATA_PROCESSING, ConfigurationInterface::KEY_CONDITIONS);
    }

    protected function getLabel(): string
    {
        return '{key}';
    }
}
