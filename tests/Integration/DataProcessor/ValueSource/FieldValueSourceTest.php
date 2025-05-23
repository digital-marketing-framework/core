<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(FieldValueSource::class)]
class FieldValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'field';

    #[Test]
    public function emptyConfigurationThrowsException(): void
    {
        $this->data['field1'] = 'value1';
        $config = $this->getValueSourceConfiguration([]);
        $this->expectExceptionMessage('Field value source: field name not provided.');
        $this->processValueSource($config);
    }

    #[Test]
    public function nonExistentFieldWillReturnNull(): void
    {
        $this->data['field1'] = 'value1';
        $config = $this->getValueSourceConfiguration([
            FieldValueSource::KEY_FIELD_NAME => 'field2',
        ]);
        $output = $this->processValueSource($config);
        $this->assertNull($output);
    }

    #[Test]
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
