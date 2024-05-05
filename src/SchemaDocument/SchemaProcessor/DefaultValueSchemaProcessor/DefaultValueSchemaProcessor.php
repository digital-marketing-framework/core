<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorPlugin;

/**
 * @template SchemaType of SchemaInterface
 *
 * @implements DefaultValueSchemaProcessorInterface<SchemaType>
 */
abstract class DefaultValueSchemaProcessor extends SchemaProcessorPlugin implements DefaultValueSchemaProcessorInterface
{
    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        protected SchemaDocument $schemaDocument,
    ) {
        parent::__construct($keyword, $registry);
    }

    public function getDefaultValue(SchemaInterface $schema): mixed
    {
        return $schema->getDefaultValue();
    }
}
