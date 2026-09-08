<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\SchemaDocument\FieldDefinition;

use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldDefinition;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FieldDefinitionTest extends TestCase
{
    #[Test]
    public function labelFallsBackToName(): void
    {
        $this->assertSame('first_name', (new FieldDefinition('first_name'))->getLabel());
        $this->assertSame('First Name', (new FieldDefinition('first_name', label: 'First Name'))->getLabel());
    }

    #[Test]
    public function mergeKeepsPropertiesTheSourcesAgreeOn(): void
    {
        $field = new FieldDefinition('email', FieldDefinition::TYPE_STRING, 'Email', multiValue: false, required: true);
        $field->merge(new FieldDefinition('email', FieldDefinition::TYPE_STRING, 'Email', multiValue: false, required: true));

        $this->assertSame(FieldDefinition::TYPE_STRING, $field->getType());
        $this->assertFalse($field->isMultiValue());
        $this->assertTrue($field->isRequired());
    }

    #[Test]
    public function mergeDegradesPropertiesTheSourcesDisagreeOn(): void
    {
        $field = new FieldDefinition('email', FieldDefinition::TYPE_STRING, multiValue: false, dedicated: 'A', required: true);
        $field->merge(new FieldDefinition('email', FieldDefinition::TYPE_INTEGER, multiValue: true, dedicated: 'B', required: false));

        $this->assertSame(FieldDefinition::TYPE_UNKNOWN, $field->getType());
        $this->assertNull($field->isMultiValue());
        $this->assertNull($field->isRequired());
        $this->assertNull($field->dedicatedField());
    }

    #[Test]
    public function mergeUnitesValueLists(): void
    {
        $field = new FieldDefinition('salutation', values: ['mr', 'mrs']);
        $field->merge(new FieldDefinition('salutation', values: ['mrs', 'mx']));

        $this->assertSame(['mr', 'mrs', 'mx'], $field->getValues());
    }

    #[Test]
    public function mergeLeavesValuesUnsetWhenNeitherSideHasAny(): void
    {
        $field = new FieldDefinition('email');
        $field->merge(new FieldDefinition('email'));

        $this->assertNull($field->getValues());
        $this->assertArrayNotHasKey('values', $field->toArray());
    }

    /**
     * Regression: the incoming list was iterated without a null check.
     */
    #[Test]
    public function mergeAcceptsAnIncomingDefinitionWithoutValues(): void
    {
        $field = new FieldDefinition('salutation', values: ['mr', 'mrs']);
        $field->merge(new FieldDefinition('salutation'));

        $this->assertSame(['mr', 'mrs'], $field->getValues());
    }

    /**
     * Regression: in_array() received null as its haystack, which is a TypeError in PHP 8.
     * This is the combination the field definition store hits, where a definition declared in
     * code carries no values and a stored one supplies them.
     */
    #[Test]
    public function mergeAcceptsAnExistingDefinitionWithoutValues(): void
    {
        $field = new FieldDefinition('salutation');
        $field->merge(new FieldDefinition('salutation', values: ['mr', 'mrs']));

        $this->assertSame(['mr', 'mrs'], $field->getValues());
    }

    #[Test]
    public function toArrayOmitsPropertiesThatWereNeverSet(): void
    {
        $array = (new FieldDefinition('email'))->toArray();

        $this->assertSame(['name' => 'email', 'type' => FieldDefinition::TYPE_UNKNOWN, 'label' => 'email'], $array);
    }
}
