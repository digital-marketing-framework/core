<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;

class ValueSchema extends ContainerSchema
{
    public const TYPE = 'VALUE';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);

        $valueSource = new CustomSchema(ValueSourceSchema::TYPE);
        $valueSource->getRenderingDefinition()->setLabel('Value');
        $this->addProperty(DataProcessor::KEY_DATA, $valueSource);

        $valueModifier = new CustomSchema(ValueModifierSchema::TYPE);
        $valueModifiers = new ListSchema($valueModifier);
        $valueModifiers->getRenderingDefinition()->addVisibilityConditionByValue('../' . DataProcessor::KEY_DATA . '/' . SwitchSchema::KEY_TYPE)->addValueSet('valueSource/modifiable');
        $this->addProperty(DataProcessor::KEY_MODIFIERS, $valueModifiers);
    }

    /**
     * @param array<string,mixed> $config
     *
     * @return array{data:array{type:string,config?:array<string,array<string,mixed>>}}
     */
    public static function createStandardValueConfiguration(string $type, array $config = []): array
    {
        $valueConfiguration = [
            DataProcessor::KEY_DATA => [
                SwitchSchema::KEY_TYPE => $type,
            ],
        ];
        if ($config !== []) {
            $valueConfiguration[DataProcessor::KEY_DATA][SwitchSchema::KEY_CONFIG][$type] = $config;
        }

        return $valueConfiguration;
    }
}
