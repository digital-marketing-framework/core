<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;

class FieldValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'field';

    protected const CLASS_NAME = FieldValueSource::class;

    protected const DEFAULT_CONFIG = [
        FieldValueSource::KEY_FIELD_NAME => FieldValueSource::DEFAULT_FIELD_NAME,
    ];

    /** @test */
    public function emptyConfigurationThrowsException(): void
    {
        $this->data['field1'] = 'value1';
        $this->expectExceptionMessage('Field value source: field name not provided.');
        $this->processValueSource([]);
    }

    /** @test */
    public function nonExistentFieldWillReturnNull(): void
    {
        $this->data['field1'] = 'value1';
        $output = $this->processValueSource([FieldValueSource::KEY_FIELD_NAME => 'field2']);
        $this->assertNull($output);
    }

    /** @test */
    public function existentFieldWillReturnItsValue(): void
    {
        $this->data['field1'] = 'value1';
        $output = $this->processValueSource([FieldValueSource::KEY_FIELD_NAME => 'field1']);
        $this->assertEquals('value1', $output);
    }
}
