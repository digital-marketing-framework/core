<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorPluginInterface;

/**
 * @template SchemaType of SchemaInterface
 *
 * @extends SchemaProcessorPluginInterface<SchemaType>
 */
interface DefaultValueSchemaProcessorInterface extends SchemaProcessorPluginInterface
{
    /**
     * @param SchemaType $schema
     */
    public function getDefaultValue(SchemaInterface $schema): mixed;
}
