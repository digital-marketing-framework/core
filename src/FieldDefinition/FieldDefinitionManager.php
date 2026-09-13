<?php

namespace DigitalMarketingFramework\Core\FieldDefinition;

use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareTrait;
use DigitalMarketingFramework\Core\FieldDefinition\Storage\FieldDefinitionStorageInterface;
use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldListDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\NullableBooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Utility\ListUtility;
use DigitalMarketingFramework\Core\Utility\MapUtility;

/**
 * Reads stored field definitions and folds them into the field contexts that plugins already
 * declare in code.
 *
 * A definition file is a configuration document in structure — the map shape the editor reads
 * and writes — so parsing and producing reuse the configuration document parser and only the
 * translation to FieldDefinition objects lives here.
 */
class FieldDefinitionManager implements FieldDefinitionManagerInterface, ConfigurationDocumentParserAwareInterface
{
    use ConfigurationDocumentParserAwareTrait;

    public const KEY_LABEL = 'label';

    public const KEY_TYPE = 'type';

    public const KEY_REQUIRED = 'required';

    public const KEY_MULTI_VALUE = 'multiValue';

    public const KEY_VALUES = 'values';

    protected SchemaDocument $schemaDocument;

    /** @var array<string,int> */
    protected array $codeDeclaredFieldCounts = [];

    public function __construct(
        protected FieldDefinitionStorageInterface $storage,
    ) {
    }

    public function getSchemaDocument(): SchemaDocument
    {
        if (!isset($this->schemaDocument)) {
            $mainSchema = new ContainerSchema();
            $mainSchema->getRenderingDefinition()->setLabel('Field Definitions');
            $mainSchema->addProperty(static::KEY_FIELDS, $this->getFieldMapSchema());

            $this->schemaDocument = new SchemaDocument($mainSchema);
        }

        return $this->schemaDocument;
    }

    protected function getFieldMapSchema(): MapSchema
    {
        $fieldNameSchema = new StringSchema();
        $fieldNameSchema->setRequired();
        $fieldNameSchema->getRenderingDefinition()->setLabel('Field Name');

        $fieldMapSchema = new MapSchema($this->getFieldSchema(), $fieldNameSchema);
        $fieldMapSchema->getRenderingDefinition()->setLabel('Fields');
        // The name is the identifier and rarely tells a person much on its own, so the entry
        // is titled by its label where one is given.
        $fieldMapSchema->getValueSchema()->getRenderingDefinition()->setLabel(
            sprintf('{%s|pretty(../%s)}', static::KEY_LABEL, MapUtility::KEY_KEY)
        );

        return $fieldMapSchema;
    }

    protected function getFieldSchema(): ContainerSchema
    {
        $schema = new ContainerSchema();

        $labelSchema = new StringSchema();
        $labelSchema->getRenderingDefinition()->setGeneralDescription('How this field is shown wherever it is offered. Without one the field name is used.');
        $schema->addProperty(static::KEY_LABEL, $labelSchema);

        $typeSchema = new StringSchema(FieldDefinition::TYPE_UNKNOWN);
        $typeSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        // The constants are upper case, which is not what a reader should be shown.
        $typeSchema->getAllowedValues()->addValue(FieldDefinition::TYPE_UNKNOWN, 'Unknown');
        $typeSchema->getAllowedValues()->addValue(FieldDefinition::TYPE_STRING, 'String');
        $typeSchema->getAllowedValues()->addValue(FieldDefinition::TYPE_INTEGER, 'Integer');
        $typeSchema->getAllowedValues()->addValue(FieldDefinition::TYPE_BOOLEAN, 'Boolean');
        $schema->addProperty(static::KEY_TYPE, $typeSchema);

        $requiredSchema = new NullableBooleanSchema();
        $requiredSchema->getRenderingDefinition()->setGeneralDescription('Whether the system this context describes treats the field as mandatory.');
        $schema->addProperty(static::KEY_REQUIRED, $requiredSchema);

        $multiValueSchema = new NullableBooleanSchema();
        $multiValueSchema->getRenderingDefinition()->setGeneralDescription('Whether this field can hold more than one value.');
        $schema->addProperty(static::KEY_MULTI_VALUE, $multiValueSchema);

        $valuesSchema = new ListSchema(new StringSchema());
        $valuesSchema->getRenderingDefinition()->setLabel('Allowed Values');
        // The navigation lists the fields of a context. A field's own properties are not
        // places to navigate to, so the only container among them stays out of it.
        $valuesSchema->getRenderingDefinition()->setNavigationItem(false);
        $valuesSchema->getRenderingDefinition()->setGeneralDescription('Leave empty when the field is not restricted to a set of values.');
        $schema->addProperty(static::KEY_VALUES, $valuesSchema);

        return $schema;
    }

    public function parse(string $contextIdentifier, string $content): FieldListDefinition
    {
        $document = $this->configurationDocumentParser->parseDocument($content);

        $fieldListDefinition = new FieldListDefinition($contextIdentifier);
        // PHP turns a numeric field name into an integer array key, hence the cast below.
        /** @var array<int|string,array<string,mixed>> */
        $fields = MapUtility::flatten($document[static::KEY_FIELDS] ?? []);
        foreach ($fields as $name => $field) {
            $fieldListDefinition->setField($this->buildFieldDefinition((string)$name, $field));
        }

        return $fieldListDefinition;
    }

    /**
     * @param array<string,mixed> $field
     */
    protected function buildFieldDefinition(string $name, array $field): FieldDefinition
    {
        /** @var array<string|int|bool|array<mixed>> */
        $values = ListUtility::flatten($field[static::KEY_VALUES] ?? []);

        return new FieldDefinition(
            name: $name,
            type: (string)($field[static::KEY_TYPE] ?? FieldDefinition::TYPE_UNKNOWN),
            label: (string)($field[static::KEY_LABEL] ?? ''),
            multiValue: NullableBooleanSchema::convert((string)($field[static::KEY_MULTI_VALUE] ?? '')),
            values: $values === [] ? null : array_values($values),
            required: NullableBooleanSchema::convert((string)($field[static::KEY_REQUIRED] ?? '')),
        );
    }

    public function getFieldListDefinition(string $contextIdentifier): ?FieldListDefinition
    {
        $folders = $this->storage->getFolders($contextIdentifier);
        if ($folders === []) {
            return null;
        }

        // Folders come lowest precedence first, and setField() replaces, so a later layer
        // wins for the fields it names while leaving the rest of the earlier one in place.
        $fieldListDefinition = new FieldListDefinition($contextIdentifier);
        foreach ($folders as $folder) {
            $content = $this->storage->getContent($contextIdentifier, $folder);
            if ($content === null) {
                continue;
            }

            foreach ($this->parse($contextIdentifier, $content)->getFields() as $field) {
                $fieldListDefinition->setField($field);
            }
        }

        return $fieldListDefinition;
    }

    public function getCodeDeclaredFieldCount(string $contextIdentifier): ?int
    {
        return $this->codeDeclaredFieldCounts[$contextIdentifier] ?? null;
    }

    public function applyToSchemaDocument(SchemaDocument $schemaDocument): void
    {
        // Before anything is folded in, because the fold writes into the very objects the
        // registries declared. Every context, not just the ones with a stored file, since a
        // context declared only in code never enters the loop below.
        foreach ($schemaDocument->getFieldContexts() as $contextIdentifier => $fieldContext) {
            $this->codeDeclaredFieldCounts[$contextIdentifier] = count($fieldContext->getFields());
        }

        foreach ($this->storage->getContextIdentifiers() as $contextIdentifier) {
            $storedFields = $this->getFieldListDefinition($contextIdentifier);
            if (!$storedFields instanceof FieldListDefinition) {
                continue;
            }

            $existing = $schemaDocument->getFieldContext($contextIdentifier);
            if (!$existing instanceof FieldListDefinition) {
                $schemaDocument->addFieldContext($contextIdentifier, $storedFields);

                continue;
            }

            // A stored definition corrects what a plugin declared, so it replaces that field
            // outright rather than being merged into it: merging degrades disagreements to
            // "unknown" instead of letting the more informed source win.
            foreach ($storedFields->getFields() as $field) {
                $existing->setField($field);
            }
        }
    }
}
