<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource
 */
class FieldValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'field';

    /** @test */
    public function emptyConfigurationThrowsException(): void
    {
        $this->data['field1'] = 'value1';
        $config = $this->getValueSourceConfiguration([]);
        $this->expectExceptionMessage('Field value source: field name not provided.');
        $this->processValueSource($config);
    }

    /** @test */
    public function nonExistentFieldWillReturnNull(): void
    {
        $this->data['field1'] = 'value1';
        $config = $this->getValueSourceConfiguration([
            FieldValueSource::KEY_FIELD_NAME => 'field2',
        ]);
        $output = $this->processValueSource($config);
        $this->assertNull($output);
    }

    /** @test */
    public function existentFieldWillReturnItsValue(): void
    {
        $this->data['field1'] = 'value1';
        $config = $this->getValueSourceConfiguration([
            FieldValueSource::KEY_FIELD_NAME => 'field1',
        ]);
        $output = $this->processValueSource($config);
        $this->assertEquals('value1', $output);
    }
}
