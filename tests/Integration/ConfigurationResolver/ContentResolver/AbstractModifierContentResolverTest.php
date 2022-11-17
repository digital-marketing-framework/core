<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

abstract class AbstractModifierContentResolverTest extends AbstractContentResolverTest
{
    protected const KEYWORD = '';

    abstract public function modifyProvider(): array;

    abstract public function modifyMultiValueProvider(): array;

    protected function runModify(mixed $value, mixed $expected, $enabled): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => $value,
            static::KEYWORD => $enabled,
        ];
        $result = $this->runResolverProcess($config);
        if ($expected === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @dataProvider modifyProvider
     * @test
     */
    public function modify(mixed $value, mixed $expected): void
    {
        $this->runModify($value, $expected, true);
        $this->runModify($value, $value, false);
    }

    protected function runModifyMultiValue(mixed $value, mixed $expected, bool $enabled): void
    {
        $config = [
            'multiValue' => $value,
            static::KEYWORD => $enabled,
        ];
        $result = $this->runResolverProcess($config);
        if (empty($expected)) {
            $this->assertMultiValueEmpty($result);
        } else {
            $this->assertMultiValueEquals($expected, $result);
        }
    }


    /**
     * @dataProvider modifyMultiValueProvider
     * @test
     */
    public function modifyMultiValue($value, $expected): void
    {
        $this->runModifyMultiValue($value, $expected, true);
        $this->runModifyMultiValue($value, $value, false);
    }

    protected function runModifyNestedMultiValue(mixed $value, mixed $expected, bool $enabled): void
    {
        $config = [
            'multiValue' => [
                ['multiValue' => $value],
            ],
            static::KEYWORD => $enabled,
        ];
        /** @var MultiValue $result */
        $result = $this->runResolverProcess($config);
        $this->assertMultiValue($result);
        $result = $result->toArray()[0];
        $this->assertMultiValueEquals($expected, $result);
    }

    /**
     * @dataProvider modifyMultiValueProvider
     * @test
     */
    public function modifyNestedMultiValue(mixed $value, mixed $expected): void
    {
        $this->runModifyNestedMultiValue($value, $expected, true);
        $this->runModifyNestedMultiValue($value, $value, false);
    }
}
