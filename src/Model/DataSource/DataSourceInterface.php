<?php

namespace DigitalMarketingFramework\Core\Model\DataSource;

use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldListDefinition;

interface DataSourceInterface
{
    public function getType(): string;
    public function getIdentifier(): string;
    public function getName(): string;
    public function getConfigurationDocument(): string;
    public function getFieldListDefinition(): FieldListDefinition;
    public function getHash(): string;
}
