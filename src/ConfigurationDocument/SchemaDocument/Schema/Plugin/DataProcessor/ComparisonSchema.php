<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\Comparison;

class ComparisonSchema extends ContainerSchema
{
    public const TYPE = 'COMPARISON';

    public const VALUE_SET_ALL = 'comparison/all';

    public const VALUE_SET_BINARY_OPERATIONS = 'comparison/binaryOperations';

    public const VALUE_SET_MULTI_VALUE_HANDLING_OPERATIONS = 'comparison/multiValueHandlingOperations';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);
        $firstOperand = new CustomSchema(ValueSchema::TYPE);

        $anyAll = new StringSchema();
        $anyAll->getAllowedValues()->addValue(Comparison::VALUE_ANY_ALL_ANY);
        $anyAll->getAllowedValues()->addValue(Comparison::VALUE_ANY_ALL_ALL);
        $anyAll->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $anyAll->getRenderingDefinition()->setSkipHeader(true);
        $anyAll->getRenderingDefinition()->addVisibilityConditionByValue('../' . Comparison::KEY_OPERATION)->addValueSet(static::VALUE_SET_MULTI_VALUE_HANDLING_OPERATIONS);
        // $anyAll->getRenderingDefinition()->addVisibilityConditionByValue('../data/type')->addValueSet(ValueSourceSchema::VALUE_SET_VALUE_SOURCE_CAN_BE_MULTI_VALUE);

        $operation = new StringSchema();
        $operation->getAllowedValues()->addValueSet(static::VALUE_SET_ALL);
        $operation->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $operation->getRenderingDefinition()->setSkipHeader(true);

        $secondOperand = new CustomSchema(ValueSchema::TYPE);
        $secondOperand->getRenderingDefinition()->addVisibilityConditionByValue('../' . Comparison::KEY_OPERATION)->addValueSet(static::VALUE_SET_BINARY_OPERATIONS);

        $this->addProperty(Comparison::KEY_FIRST_OPERAND, $firstOperand);
        $this->addProperty(Comparison::KEY_ANY_ALL, $anyAll);
        $this->addProperty(Comparison::KEY_OPERATION, $operation);
        $this->addProperty(Comparison::KEY_SECOND_OPERAND, $secondOperand);
    }
}
