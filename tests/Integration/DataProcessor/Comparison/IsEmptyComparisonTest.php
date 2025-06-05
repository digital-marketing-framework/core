<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsEmptyComparison;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(IsEmptyComparison::class)]
class IsEmptyComparisonTest extends ComparisonTestBase
{
    protected const KEYWORD = 'isEmpty';

    protected function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = '';
        $this->data['field3'] = '0';

        $this->data['field5'] = new MultiValue(['value1']);
        $this->data['field6'] = new MultiValue([]);
        $this->data['field7'] = new MultiValue(['']);
        $this->data['field8'] = new MultiValue(['0']);
    }

    /**
     * @return array<array{0:bool,1:array<string,mixed>}>
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
                false,
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
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field7'], 'field'),
            ],
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field8'], 'field'),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $firstOperand
     */
    #[Test]
    #[DataProvider('comparisonDataProvider')]
    public function isEmptyTest(bool $expectedResult, array $firstOperand): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand);
    }
}
