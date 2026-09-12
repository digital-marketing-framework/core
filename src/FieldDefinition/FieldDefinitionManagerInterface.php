<?php

namespace DigitalMarketingFramework\Core\FieldDefinition;

use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldListDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface FieldDefinitionManagerInterface
{
    /**
     * Top-level key of a field definition document.
     */
    public const KEY_FIELDS = 'fields';

    /**
     * The schema a field definition file is edited against.
     */
    public function getSchemaDocument(): SchemaDocument;

    public function parse(string $contextIdentifier, string $content): FieldListDefinition;

    /**
     * Every stored layer of this context, folded together, or null when nothing is stored.
     */
    public function getFieldListDefinition(string $contextIdentifier): ?FieldListDefinition;

    /**
     * Folds the stored definitions into the field contexts a schema document already carries.
     */
    public function applyToSchemaDocument(SchemaDocument $schemaDocument): void;

    /**
     * How many fields a context carried before any stored definition was folded in, or null
     * for a context that was not there at all.
     *
     * Only meaningful once applyToSchemaDocument() has run, which is the case for any schema
     * document obtained from the registry collection.
     */
    public function getCodeDeclaredFieldCount(string $contextIdentifier): ?int;
}
