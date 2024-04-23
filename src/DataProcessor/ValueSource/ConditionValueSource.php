<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor\ConditionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class ConditionValueSource extends ValueSource
{
    public const WEIGHT = 4;

    public const KEY_IF = 'if';

    public const DEFAULT_IF = null;

    public const KEY_THEN = 'then';

    public const DEFAULT_THEN = null;

    public const KEY_ELSE = 'else';

    public const DEFAULT_ELSE = null;

    public function build(): string|ValueInterface|null
    {
        $if = $this->getConfig(static::KEY_IF);
        $then = $this->getConfig(static::KEY_THEN);
        $else = $this->getConfig(static::KEY_ELSE);

        if ($if === null) {
            throw new DigitalMarketingFrameworkException('Condition value source - no condition given.');
        }

        $evalResult = $this->dataProcessor->processCondition($if, $this->context->copy());
        $thenResult = $then === null ? null : $this->dataProcessor->processValue($then, $this->context->copy());
        $elseResult = $else === null ? null : $this->dataProcessor->processValue($else, $this->context->copy());

        return $evalResult ? $thenResult : $elseResult;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();

        $ifSchema = new CustomSchema(ConditionSchema::TYPE);
        $ifSchema->getRenderingDefinition()->setLabel('if');
        $schema->addProperty(static::KEY_IF, $ifSchema);

        $schema->addProperty(static::KEY_THEN, new CustomSchema(ValueSchema::TYPE));
        $schema->addProperty(static::KEY_ELSE, new CustomSchema(ValueSchema::TYPE));

        return $schema;
    }
}
