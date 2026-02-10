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

    /**
     * For variant data sources (e.g. form plugin overrides), returns the identifier
     * of the base data source. Returns null if this is already a base data source.
     */
    public function getBaseDataSourceIdentifier(): ?string;

    /**
     * Short description for display in the backend UI.
     * E.g. for form plugin variants: "Content element #42".
     */
    public function getDescription(): string;

    /**
     * Whether this data source type supports variants (e.g. form plugin overrides).
     * Used by the backend maintenance UI to indicate expandable children.
     */
    public function canHaveVariants(): bool;
}
