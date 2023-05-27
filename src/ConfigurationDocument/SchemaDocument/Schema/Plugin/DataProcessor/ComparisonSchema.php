<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;

class ComparisonSchema extends ContainerSchema
{
    public const TYPE = 'COMPARISON';

    public const VALUE_SET_COMPARISONS = 'comparison/all';
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

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);
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

        $this->firstOperandSchema = new CustomSchema(ValueSchema::TYPE);
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

    public function addItem(string $keyword, bool $binaryOperation, bool $multiValueHandlingOperation): void
    {
        $this->typeSchema->getAllowedValues()->addValue($keyword);
        $this->valueSets[static::VALUE_SET_COMPARISONS][] = $keyword;
        if ($binaryOperation) {
            $this->valueSets[static::VALUE_SET_BINARY_OPERATIONS][] = $keyword;
        }
        if ($multiValueHandlingOperation) {
            $this->valueSets[static::VALUE_SET_MULTI_VALUE_HANDLING_OPERATIONS][] = $keyword;
        }
    }
}
