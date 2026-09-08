<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\SchemaDocument\FieldDefinition;

use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldListDefinition;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FieldListDefinitionTest extends TestCase
{
    protected function list(FieldDefinition ...$fields): FieldListDefinition
    {
        $list = new FieldListDefinition('test.context');
        foreach ($fields as $field) {
            $list->addField($field);
        }

        return $list;
    }

    #[Test]
    public function addFieldCombinesDefinitionsOfTheSameField(): void
    {
        $list = $this->list(
            new FieldDefinition('email', FieldDefinition::TYPE_STRING, 'Email', required: true),
            new FieldDefinition('email', FieldDefinition::TYPE_INTEGER, 'E-Mail', required: false),
        );

        $email = $list->getField('email');
        $this->assertNotNull($email);
        $this->assertSame(FieldDefinition::TYPE_UNKNOWN, $email->getType());
        $this->assertNull($email->isRequired());
    }

    #[Test]
    public function fieldsAreKeyedByName(): void
    {
        $list = $this->list(new FieldDefinition('email'), new FieldDefinition('phone'));

        $this->assertSame(['email', 'phone'], array_keys($list->getFields()));
    }

    #[Test]
    public function unknownFieldsResolveToNull(): void
    {
        $list = $this->list(new FieldDefinition('email'));

        $this->assertNull($list->getField('nope'));
        $this->assertFalse($list->fieldExists('nope'));
    }

    #[Test]
    public function removeFieldIsTolerantOfUnknownNames(): void
    {
        $list = $this->list(new FieldDefinition('email'));
        $list->removeField('nope');
        $list->removeField('email');

        $this->assertSame([], $list->getFields());
    }

    #[Test]
    public function mergeCombinesEveryFieldOfTheOtherList(): void
    {
        $list = $this->list(
            new FieldDefinition('email', FieldDefinition::TYPE_STRING),
            new FieldDefinition('phone', FieldDefinition::TYPE_STRING),
        );
        $list->merge($this->list(
            new FieldDefinition('phone', FieldDefinition::TYPE_INTEGER),
            new FieldDefinition('company', FieldDefinition::TYPE_STRING),
        ));

        $this->assertSame(['email', 'phone', 'company'], array_keys($list->getFields()));
        $this->assertSame(FieldDefinition::TYPE_STRING, $list->getField('email')?->getType());
        $this->assertSame(FieldDefinition::TYPE_UNKNOWN, $list->getField('phone')?->getType());
    }

    /**
     * Regression: merging lists reached FieldDefinition::merge() with a null value list on
     * either side, which was a TypeError. This is the shape the field definition store
     * produces when a stored list supplies values a code-declared one does not have.
     */
    #[Test]
    public function mergeAcceptsListsWhoseFieldsHaveNoValues(): void
    {
        $list = $this->list(new FieldDefinition('salutation'));
        $list->merge($this->list(new FieldDefinition('salutation', values: ['mr', 'mrs'])));

        $this->assertSame(['mr', 'mrs'], $list->getField('salutation')?->getValues());
    }

    #[Test]
    public function toArrayReturnsEveryFieldKeyedByName(): void
    {
        $list = $this->list(new FieldDefinition('email', FieldDefinition::TYPE_STRING, 'Email'));

        $this->assertSame([
            'email' => ['name' => 'email', 'type' => FieldDefinition::TYPE_STRING, 'label' => 'Email'],
        ], $list->toArray());
    }
}
