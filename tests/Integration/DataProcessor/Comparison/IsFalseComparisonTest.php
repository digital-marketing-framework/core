<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsFalseComparison;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(IsFalseComparison::class)]
class IsFalseComparisonTest extends ComparisonTestBase
{
    protected const KEYWORD = 'isFalse';

    protected function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = '';
        $this->data['field3'] = '0';

        $this->data['field5'] = new MultiValue(['value1']);
        $this->data['field6'] = new MultiValue([]);
        $this->data['field7'] = new MultiValue(['']);
        $this->data['field8'] = new MultiValue(['', '1']);
        $this->data['field9'] = new MultiValue(['0']);
        $this->data['field10'] = new MultiValue(['0', '1']);
    }

    /**
     * @return array<array{0:bool,1:array<string,mixed>,2?:?string}>
     */
    public static function comparisonDataProvider(): array
    {
        return [
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field1'], 'field'),
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field2'], 'field'),
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field3'], 'field'),
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field4'], 'field'),
            ],
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field5'], 'field'),
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field6'], 'field'),
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field7'], 'field'),
            ],
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field8'], 'field'),
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field8'], 'field'),
                'any',
            ],
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field8'], 'field'),
                'all',
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field9'], 'field'),
            ],
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field10'], 'field'),
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field10'], 'field'),
                'any',
            ],
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field10'], 'field'),
                'all',
            ],
        ];
    }

    /**
     * @param array<string,mixed> $firstOperand
     */
    #[Test]
    #[DataProvider('comparisonDataProvider')]
    public function isFalseTest(bool $expectedResult, array $firstOperand, ?string $anyAll = null): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand, null, $anyAll);
    }
}
