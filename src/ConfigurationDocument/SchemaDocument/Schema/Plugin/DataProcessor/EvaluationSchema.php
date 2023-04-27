<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\PluginSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\EvaluationInterface;

class EvaluationSchema extends PluginSchema
{
    public const TYPE = 'EVALUATION';

    protected StringSchema $typeSchema;
    protected ContainerSchema $configSchema;

    protected function init(): void
    {
        $this->typeSchema = new StringSchema();
        $this->configSchema = new ContainerSchema();
        $this->addProperty('type', $this->typeSchema);
        $this->addProperty('config', $this->configSchema);
    }

    public function addEvaluation(string $keyword, SchemaInterface $schema): void
    {
        $this->typeSchema->getAllowedValues()->addValue($keyword);
        $this->configSchema->addProperty($keyword, $schema);
        $schema->getRenderingDefinition()->setVisibilityConditionByString('../type', $keyword);
    }

    public function processPlugin(string $keyword, string $class): void
    {
        $this->addEvaluation($keyword, $class::getSchema());
    }

    protected function getPluginInterface(): string
    {
        return EvaluationInterface::class;
    }
}
