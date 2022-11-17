<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

abstract class AbstractIsEvaluationTest extends AbstractEvaluationTest
{
    protected const KEYWORD = '';

    abstract public function isProvider(): array;
    abstract public function isMultiValueProvider(): array;
    abstract public function anyIsMultiValueProvider(): array;
    abstract public function allIsMultiValueProvider(): array;

    /**
     * @dataProvider isProvider
     * @test
     */
    public function is(mixed $value, bool $is, bool $expected)
    {
        if ($value !== null) {
            $this->data['field1'] = $value;
        }
        $config = [
            'field1' => [
                static::KEYWORD => $is,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param array $value
     * @param bool $is
     * @param bool $expected
     * @dataProvider isMultiValueProvider
     * @test
     */
    public function isMultiValue(array $value, bool $is, bool $expected)
    {
        $this->data['field1'] = new MultiValue($value);
        $config = [
            'field1' => [
                static::KEYWORD => $is,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param array $value
     * @param bool $is
     * @param bool $expected
     * @dataProvider anyIsMultiValueProvider
     * @test
     */
    public function anyIsMultiValue(array $value, bool $is, bool $expected)
    {
        $this->data['field1'] = new MultiValue($value);
        $config = [
            'field1' => [
                'any' => [
                    static::KEYWORD => $is,
                ]
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param array $value
     * @param bool $is
     * @param bool $expected
     * @dataProvider allIsMultiValueProvider
     * @test
     */
    public function allIsMultiValue(array $value, bool $is, bool $expected)
    {
        $this->data['field1'] = new MultiValue($value);
        $config = [
            'field1' => [
                'all' => [
                    static::KEYWORD => $is,
                ]
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
