<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\PluginSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\BinaryComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;

class ComparisonSchema extends PluginSchema
{
    public const TYPE = 'COMPARISON';

    public const VALUE_SET_BINARY_OPERATIONS = 'comparison/binaryOperations';
    public const VALUE_SET_MULTI_VALUE_HANDLING_OPERATIONS = 'comparison/multiValueHandlingOperations';

    protected StringSchema $typeSchema;
    protected StringSchema $anyAllSchema;
    protected CustomSchema $firstOperandSchema;
    protected CustomSchema $secondOperandSchema;
    
    /** @var array<string> $binaryOperations */
    protected array $binaryOperations = [];

    /** @var array<string> $multiValueHandlingOperations */
    protected array $multiValueHandlingOperations = [];

    protected function init(): void
    {
        $this->typeSchema = new StringSchema();
        $this->typeSchema->getRenderingDefinition()->setFormat('select');
        $this->typeSchema->getRenderingDefinition()->hideLabel();
        
        $this->anyAllSchema = new StringSchema();
        $this->anyAllSchema->getAllowedValues()->addValue('any');
        $this->anyAllSchema->getAllowedValues()->addValue('all');
        $this->anyAllSchema->getRenderingDefinition()->setFormat('select');
        $this->anyAllSchema->getRenderingDefinition()->hideLabel();
        $this->anyAllSchema->getRenderingDefinition()->setVisibilityConditionByValueSet('./type', static::VALUE_SET_MULTI_VALUE_HANDLING_OPERATIONS);
        // TODO it is currently not possible to have more than one condition on visibility
        // $this->anyAllSchema->getRenderingDefinition()->setVisibilityConditionByValueSet('../data/type', ValueSourceSchema::VALUE_SET_VALUE_SOURCE_CAN_BE_MULTI_VALUE);
        
        $this->firstOperandSchema = new CustomSchema('DEFAULT_FIELD_' . ValueSchema::TYPE);
        $this->firstOperandSchema->getRenderingDefinition()->hideLabel();
        
        $this->secondOperandSchema = new CustomSchema(ValueSchema::TYPE);
        $this->secondOperandSchema->getRenderingDefinition()->hideLabel();
        $this->secondOperandSchema->getRenderingDefinition()->setVisibilityConditionByValueSet('./type', static::VALUE_SET_BINARY_OPERATIONS);
        
        $this->addProperty('type', $this->typeSchema);
        $this->addProperty('anyAll', $this->anyAllSchema);
        $this->addProperty('firstOperand', $this->firstOperandSchema);
        $this->addProperty('secondOperand', $this->secondOperandSchema);
        $this->getRenderingDefinition()->addAlignment('horizontal', ['firstOperand', 'anyAll', 'type', 'secondOperand']);
    }

    public function addComparison(string $keyword, bool $binaryOperation, bool $multiValueHandlingOperation): void
    {
        $this->typeSchema->getAllowedValues()->addValue($keyword);
        if ($binaryOperation) {
            $this->valueSets[static::VALUE_SET_BINARY_OPERATIONS][] = $keyword;
        }
        if ($multiValueHandlingOperation) {
            $this->valueSets[static::VALUE_SET_MULTI_VALUE_HANDLING_OPERATIONS][] = $keyword;
        }
    }

    protected function processPlugin(string $keyword, string $class): void
    {
        $this->addComparison(
            $keyword,
            is_a($class, BinaryComparison::class, true),
            $class::handleMultiValuesIndividually()
        );
    }

    protected function getPluginInterface(): string
    {
        return ComparisonInterface::class;
    }
}
