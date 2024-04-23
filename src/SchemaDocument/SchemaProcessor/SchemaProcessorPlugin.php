<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor;

use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class SchemaProcessorPlugin extends Plugin
{
    protected SchemaProcessorInterface $schemaProcessor;

    public function __construct(
        string $keyword,
        RegistryInterface $registry,
    ) {
        parent::__construct($keyword);
        $this->schemaProcessor = $registry->getSchemaProcessor();
    }
}
