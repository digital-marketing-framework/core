<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ComparisonSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;

abstract class Comparison extends DataProcessorPlugin implements ComparisonInterface
{
    public const KEY_FIRST_OPERAND = 'firstOperand';

    public const KEY_SECOND_OPERAND = 'secondOperand';

    public const KEY_OPERATION = 'type';

    public const KEY_ANY_ALL = 'anyAll';

    public const VALUE_ANY_ALL_ANY = 'any';

    public const VALUE_ANY_ALL_ALL = 'all';

    abstract public function compare(): bool;

    public static function handleMultiValuesIndividually(): bool
    {
        return true;
    }

    protected function compareAnyEmpty(): bool
    {
        return true;
    }

    protected function compareAllEmpty(): bool
    {
        return true;
    }

    public static function getSchema(): SchemaInterface
    {
        return new ComparisonSchema();
    }
}
