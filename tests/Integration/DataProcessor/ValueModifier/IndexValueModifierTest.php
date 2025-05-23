<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\IndexValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\IndexValueModifierTest as IndexValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(IndexValueModifier::class)]
class IndexValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'index';

    /**
     * @return array<array{0:mixed,1:string,2:string}>
     */
    public static function errorValuesDataProvider(): array
    {
        return IndexValueModifierUnitTest::MODIFY_ERROR_TEST_CASES;
    }

    #[Test]
    #[DataProvider('errorValuesDataProvider')]
    public function errorValues(mixed $value, string $index, string $message): void
    {
        $this->expectExceptionMessage($message);
        $this->processValueModifier([
            ValueModifier::KEY_ENABLED => true,
            IndexValueModifier::KEY_INDEX => $index,
        ], $this->convertMultiValues($value));
    }

    public static function modifyProvider(): array
    {
        return IndexValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
