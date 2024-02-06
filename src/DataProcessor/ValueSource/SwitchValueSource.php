<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;

class SwitchValueSource extends ValueSource
{
    public const WEIGHT = 4;

    public const KEY_SWITCH = 'switch';

    public const KEY_CASES = 'cases';

    public const DEFAULT_CASES_KEY = '';

    public const DEFAULT_CASES_VALUE = '';

    public const KEY_USE_DEFAULT = 'useDefault';

    public const DEFAULT_USE_DEFAULT = false;

    public const KEY_DEFAULT = 'default';

    public const DEFAULT_DEFAULT = '';

    public function build(): string|null
    {
        $switchConfig = $this->getConfig(static::KEY_SWITCH);
        $switchValue = $this->dataProcessor->processValue($switchConfig, $this->context->copy());

        if ($switchValue === null) {
            return null;
        }

        $switchValue = (string)$switchValue;

        /** @var array<string,string> */
        $cases = $this->getMapConfig(static::KEY_CASES);
        if (isset($cases[$switchValue])) {
            return $cases[$switchValue];
        }

        /** @var bool */
        $useDefault = $this->getConfig(static::KEY_USE_DEFAULT);
        if ($useDefault) {
            /** @var string */
            $default = $this->getConfig(static::KEY_DEFAULT);

            return $default;
        }

        return null;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();

        $switchSchema = new CustomSchema(ValueSchema::TYPE);
        $schema->addProperty(static::KEY_SWITCH, $switchSchema);

        $caseSchema = new StringSchema(static::DEFAULT_CASES_KEY);
        $valueSchema = new StringSchema(static::DEFAULT_CASES_VALUE);
        $casesSchema = new MapSchema($valueSchema, $caseSchema);
        $schema->addProperty(static::KEY_CASES, $casesSchema);

        $useDefaultSchema = new BooleanSchema(static::DEFAULT_USE_DEFAULT);
        $schema->addProperty(static::KEY_USE_DEFAULT, $useDefaultSchema);

        $defaultSchema = new StringSchema(static::DEFAULT_DEFAULT);
        $defaultSchema->getRenderingDefinition()->addVisibilityConditionByValue('../' . static::KEY_USE_DEFAULT)->addValue(true);
        $schema->addProperty(static::KEY_DEFAULT, $defaultSchema);

        return $schema;
    }
}
