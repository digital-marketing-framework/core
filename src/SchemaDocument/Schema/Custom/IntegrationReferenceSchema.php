<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class IntegrationReferenceSchema extends ContainerSchema
{
    public const KEY_INTEGRATION_REFERENCE = 'integrationReference';

    protected StringSchema $integrationReferenceSchema;

    public function __construct(
        mixed $defaultValue = null,
        bool $required = true,
    ) {
        parent::__construct($defaultValue);

        $this->integrationReferenceSchema = new StringSchema();
        if ($required) {
            $this->integrationReferenceSchema->setRequired();
        }

        $this->integrationReferenceSchema->getAllowedValues()->addValue('', 'Please select');
        $this->integrationReferenceSchema->getAllowedValues()->addReference(
            sprintf('/%s/*', ConfigurationInterface::KEY_INTEGRATIONS),
            ignorePath: $this->getIntegrationIgnorePath()
        );
        $this->integrationReferenceSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $this->integrationReferenceSchema->getRenderingDefinition()->setLabel('Integration');
        $this->addProperty(static::KEY_INTEGRATION_REFERENCE, $this->integrationReferenceSchema);

        $this->getRenderingDefinition()->setLabel('{integrationReference}');
        $this->getRenderingDefinition()->setSkipHeader(true);
    }

    /**
     * @return array<string>
     */
    protected function getIntegrationIgnorePath(): array
    {
        return [
            sprintf('/%s/%s', ConfigurationInterface::KEY_INTEGRATIONS, ConfigurationInterface::KEY_GENERAL_INTEGRATION),
        ];
    }

    /**
     * @param array{integrationReference?:string} $config
     */
    public static function getIntegrationName(array $config): string
    {
        return $config[static::KEY_INTEGRATION_REFERENCE] ?? '';
    }
}
