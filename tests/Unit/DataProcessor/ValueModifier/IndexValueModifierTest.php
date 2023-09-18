<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\IndexValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;

class IndexValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'index';

    protected const CLASS_NAME = IndexValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [['a', 'b', 'c'], 'b', [IndexValueModifier::KEY_INDEX => '1']],
        [['a', 'b', 'c'], null, [IndexValueModifier::KEY_INDEX => '5']],
        [['a', ['b1', 'b2'], 'c'], 'b1', [IndexValueModifier::KEY_INDEX => '1,0']],
    ];

    public const MODIFY_ERROR_TEST_CASES = [
        [null, '1', 'Value is not a multi value and does not have an index "1".'],
        ['foo', '2', 'Value is not a multi value and does not have an index "2".'],
        [['foo'], '0,1', 'Value is not a multi value and does not have an index "1".'],
    ];

    /**
     * @return array<array{0:mixed,1:string,2:string}>
     */
    public function errorValuesDataProvider(): array
    {
        return static::MODIFY_ERROR_TEST_CASES;
    }

    /**
     * @test
     *
     * @dataProvider errorValuesDataProvider
     */
    public function errorValues(mixed $value, string $index, string $message): void
    {
        $this->expectExceptionMessage($message);
        $this->processValueModifier([
            ValueModifier::KEY_ENABLED => true,
            IndexValueModifier::KEY_INDEX => $index,
        ], $this->convertMultiValues($value));
    }

    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
