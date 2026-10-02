<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Environment\EnvironmentServiceAwareInterface;
use DigitalMarketingFramework\Core\Environment\EnvironmentServiceAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\CoreSettings;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class EnvironmentVariableValueSource extends ValueSource implements GlobalConfigurationAwareInterface, EnvironmentServiceAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use EnvironmentServiceAwareTrait;

    public const WEIGHT = 3;

    public const KEY_VARIABLE_NAME = 'name';

    public const DEFAULT_VARIABLE_NAME = '';

    /**
     * Holds the names allowed on the current system. They are only suggestions,
     * so that a document can still name a variable that is allowed on another system.
     */
    public const VALUE_SET_ALLOWED_VARIABLES = 'environmentVariable/allowed';

    /**
     * Only variables listed in the global settings can be read. Anything else yields null,
     * as does an allowed variable that is not set, so that FirstOfValueSource can fall
     * through to a fallback in both cases.
     */
    public function build(): ?string
    {
        $name = $this->getStringConfig(static::KEY_VARIABLE_NAME);
        if ($name === '') {
            return null;
        }

        $allowedNames = $this->globalConfiguration->getGlobalSettings(CoreSettings::class)->getAllowedEnvironmentVariables();
        if (!in_array($name, $allowedNames, true)) {
            $this->logger->warning(sprintf('Environment variable "%s" is not allowed in configuration documents.', $name));

            return null;
        }

        if (!$this->environmentService->environmentVariableExists($name)) {
            return null;
        }

        return $this->environmentService->getEnvironmentVariable($name);
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();

        $variableNameSchema = new StringSchema(static::DEFAULT_VARIABLE_NAME);
        $variableNameSchema->setRequired();
        $variableNameSchema->getRenderingDefinition()->setLabel('Environment Variable');
        $variableNameSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_COMBOBOX);
        $variableNameSchema->getSuggestedValues()->addValueSet(static::VALUE_SET_ALLOWED_VARIABLES);
        $schema->addProperty(static::KEY_VARIABLE_NAME, $variableNameSchema);

        return $schema;
    }
}
