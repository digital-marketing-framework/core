<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\FieldDefinition;

use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\YamlConfigurationDocumentParser;
use DigitalMarketingFramework\Core\FieldDefinition\FieldDefinitionManager;
use DigitalMarketingFramework\Core\FieldDefinition\Storage\FieldDefinitionStorageInterface;
use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldListDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FieldDefinitionManagerTest extends TestCase
{
    protected const CONTEXT = 'distributor.out.defaults.salesforce.salesforce';

    protected FieldDefinitionStorageInterface&MockObject $storage;

    protected FieldDefinitionManager $subject;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(FieldDefinitionStorageInterface::class);
        $this->subject = new FieldDefinitionManager($this->storage);
        $this->subject->setConfigurationDocumentParser(new YamlConfigurationDocumentParser());
    }

    /**
     * The map shape the config editor reads and writes, which is what a definition file holds.
     */
    protected function document(string $key, string $label, string $required = 'undefined', string $values = '{  }'): string
    {
        return <<<YAML
            fields:
              uuid-1:
                uuid: uuid-1
                weight: 10000
                key: {$key}
                value:
                  label: '{$label}'
                  type: STRING
                  required: '{$required}'
                  multiValue: 'no'
                  values: {$values}
            YAML;
    }

    #[Test]
    public function aStoredFieldBecomesAFieldDefinition(): void
    {
        $fields = $this->subject->parse(self::CONTEXT, $this->document('00N58000006Sucg', 'Contact Owner', 'no'));

        $field = $fields->getField('00N58000006Sucg');
        $this->assertNotNull($field);
        $this->assertSame('Contact Owner', $field->getLabel());
        $this->assertSame(FieldDefinition::TYPE_STRING, $field->getType());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->isMultiValue());
        $this->assertNull($field->getValues());
    }

    #[Test]
    public function anUndefinedBooleanStaysUnknown(): void
    {
        $fields = $this->subject->parse(self::CONTEXT, $this->document('00N58000006Tabc', 'Lead Rating'));

        $this->assertNull($fields->getField('00N58000006Tabc')?->isRequired());
    }

    #[Test]
    public function allowedValuesAreReadFromTheirList(): void
    {
        $values = <<<'YAML'

                    value-1:
                      uuid: value-1
                      weight: 10000
                      value: hot
                    value-2:
                      uuid: value-2
                      weight: 10100
                      value: cold
        YAML;
        $fields = $this->subject->parse(self::CONTEXT, $this->document('rating', 'Lead Rating', 'no', $values));

        $this->assertSame(['hot', 'cold'], $fields->getField('rating')?->getValues());
    }

    #[Test]
    public function anEmptyDocumentYieldsNoFields(): void
    {
        $this->assertSame([], $this->subject->parse(self::CONTEXT, '')->getFields());
    }

    #[Test]
    public function layersAreFoldedWithTheHigherFolderWinningPerField(): void
    {
        $this->storage->method('getFolders')->willReturn(['PKG:vendor/package/res/fields', '1:/fields']);
        $this->storage->method('getContent')->willReturnCallback(fn (string $context, ?string $folder): string => $folder === '1:/fields'
            ? $this->document('email', 'Work Email', 'yes')
            : $this->document('email', 'Email', 'no'));

        $fields = $this->subject->getFieldListDefinition(self::CONTEXT);

        $this->assertNotNull($fields);
        $email = $fields->getField('email');
        $this->assertNotNull($email);
        $this->assertSame('Work Email', $email->getLabel());
        $this->assertTrue($email->isRequired());
    }

    #[Test]
    public function aContextWithNoStoredFileIsNull(): void
    {
        $this->storage->method('getFolders')->willReturn([]);

        $this->assertNull($this->subject->getFieldListDefinition(self::CONTEXT));
    }

    #[Test]
    public function aStoredContextThatCodeDoesNotDeclareIsAddedWhole(): void
    {
        $this->storage->method('getContextIdentifiers')->willReturn([self::CONTEXT]);
        $this->storage->method('getFolders')->willReturn(['1:/fields']);
        $this->storage->method('getContent')->willReturn($this->document('00N58000006Sucg', 'Contact Owner'));

        $schemaDocument = new SchemaDocument();
        $this->subject->applyToSchemaDocument($schemaDocument);

        $this->assertSame('Contact Owner', $schemaDocument->getFieldContext(self::CONTEXT)?->getField('00N58000006Sucg')?->getLabel());
    }

    #[Test]
    public function aStoredFieldReplacesTheOneDeclaredInCode(): void
    {
        $this->storage->method('getContextIdentifiers')->willReturn([self::CONTEXT]);
        $this->storage->method('getFolders')->willReturn(['1:/fields']);
        $this->storage->method('getContent')->willReturn($this->document('company', 'Company Name (overridden)', 'yes'));

        $declaredInCode = new FieldListDefinition(self::CONTEXT);
        $declaredInCode->addField(new FieldDefinition('company', FieldDefinition::TYPE_STRING, 'Company', required: false));
        $declaredInCode->addField(new FieldDefinition('email', FieldDefinition::TYPE_STRING, 'Email', required: true));

        $schemaDocument = new SchemaDocument();
        $schemaDocument->addFieldContext(self::CONTEXT, $declaredInCode);

        $this->subject->applyToSchemaDocument($schemaDocument);

        $context = $schemaDocument->getFieldContext(self::CONTEXT);
        $this->assertNotNull($context);
        // Replaced outright rather than merged: merging would have degraded the disagreeing
        // "required" to null instead of letting the stored definition win.
        $company = $context->getField('company');
        $this->assertNotNull($company);
        $this->assertSame('Company Name (overridden)', $company->getLabel());
        $this->assertTrue($company->isRequired());
        // Fields the file says nothing about are left alone.
        $this->assertSame('Email', $context->getField('email')?->getLabel());
    }

    #[Test]
    public function theFieldCountDeclaredInCodeIsTakenBeforeAnythingIsFoldedIn(): void
    {
        $this->storage->method('getContextIdentifiers')->willReturn([self::CONTEXT]);
        $this->storage->method('getFolders')->willReturn(['1:/fields']);
        $this->storage->method('getContent')->willReturn($this->document('company', 'Company Name (overridden)', 'yes'));

        $declaredInCode = new FieldListDefinition(self::CONTEXT);
        $declaredInCode->addField(new FieldDefinition('company', FieldDefinition::TYPE_STRING, 'Company'));
        $declaredInCode->addField(new FieldDefinition('email', FieldDefinition::TYPE_STRING, 'Email'));

        $schemaDocument = new SchemaDocument();
        $schemaDocument->addFieldContext(self::CONTEXT, $declaredInCode);

        $this->subject->applyToSchemaDocument($schemaDocument);

        // The fold writes into the very object the registry declared, so a count taken
        // afterwards would already include the stored field.
        $this->assertSame(2, $this->subject->getCodeDeclaredFieldCount(self::CONTEXT));
        $this->assertCount(2, $schemaDocument->getFieldContext(self::CONTEXT)?->getFields() ?? []);
    }

    #[Test]
    public function aContextWithoutAStoredFileIsStillCounted(): void
    {
        // Nothing stored means the fold skips this context entirely, which is exactly the case
        // the count is needed for: a list row that shows fields but no file.
        $this->storage->method('getContextIdentifiers')->willReturn([]);

        $declaredInCode = new FieldListDefinition(self::CONTEXT);
        $declaredInCode->addField(new FieldDefinition('company', FieldDefinition::TYPE_STRING, 'Company'));

        $schemaDocument = new SchemaDocument();
        $schemaDocument->addFieldContext(self::CONTEXT, $declaredInCode);

        $this->subject->applyToSchemaDocument($schemaDocument);

        $this->assertSame(1, $this->subject->getCodeDeclaredFieldCount(self::CONTEXT));
    }

    #[Test]
    public function aContextThatCodeNeverDeclaredHasNoCount(): void
    {
        $this->storage->method('getContextIdentifiers')->willReturn([self::CONTEXT]);
        $this->storage->method('getFolders')->willReturn(['1:/fields']);
        $this->storage->method('getContent')->willReturn($this->document('company', 'Company'));

        $schemaDocument = new SchemaDocument();
        $this->subject->applyToSchemaDocument($schemaDocument);

        $this->assertNull($this->subject->getCodeDeclaredFieldCount(self::CONTEXT));
    }

    #[Test]
    public function theSchemaDescribesAMapOfFields(): void
    {
        $schema = $this->subject->getSchemaDocument()->toArray()['schema'];

        $this->assertSame('CONTAINER', $schema['type']);
        $fields = null;
        foreach ($schema['values'] as $property) {
            if ($property['key'] === FieldDefinitionManager::KEY_FIELDS) {
                $fields = $property;
            }
        }

        $this->assertNotNull($fields);
        $this->assertSame('MAP', $fields['type']);
    }
}
