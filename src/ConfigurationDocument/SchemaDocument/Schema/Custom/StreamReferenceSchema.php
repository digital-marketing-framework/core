<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

class StreamReferenceSchema extends ReferenceSchema
{
    public const TYPE = 'STREAM_REFERENCE';

    public function __construct(
        ?string $defaultValue = null,
        bool $required = true,
        ?string $firstEmptyOptionLabel = null
    ) {
        parent::__construct($defaultValue, $required, $firstEmptyOptionLabel);
        $this->getRenderingDefinition()->setLabel('Stream');
    }

    protected function getReferencePath(): string
    {
        return sprintf('/%s/*', ConfigurationInterface::KEY_STREAMS);
    }

    protected function getLabel(): string
    {
        return '{key}';
    }
}
