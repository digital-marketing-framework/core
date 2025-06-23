<?php

namespace DigitalMarketingFramework\Core\Api\EndPoint;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class EndPointSchema extends ContainerSchema
{
    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);

        $this->addProperty('name', new StringSchema(''));

        $this->addProperty('enabled', new BooleanSchema(false));
        $this->addProperty('push_enabled', new BooleanSchema(false));
        $this->addProperty('pull_enabled', new BooleanSchema(false));

        $this->addProperty('disable_context', new BooleanSchema(false));
        $this->addProperty('allow_context_override', new BooleanSchema(false));

        $this->addProperty('expose_to_frontend', new BooleanSchema(false));

        $configurationDocumentSchema = new StringSchema('');
        $configurationDocumentSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_TEXT);
        $this->addProperty('configuration_document', $configurationDocumentSchema);
    }
}
