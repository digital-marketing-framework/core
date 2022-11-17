<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

abstract class AbstractModifierEvaluationTest extends AbstractEvaluationTest
{
    protected const KEYWORD = '';

    abstract public function modifyProvider(): array;
    abstract public function modifyMultiValueProvider(): array;

    /**
     * @dataProvider modifyProvider
     * @test
     */
    public function modify(mixed $value, mixed $modifiedValue): void
    {
        $this->data['field1'] = $value;
        $config = [
            'field1' => [
                static::KEYWORD => $modifiedValue,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider modifyMultiValueProvider
     * @test
     */
    public function modifyMultiValue(array $value, array $modifiedValue): void
    {
        $this->data['field1'] = new MultiValue($value);
        $config = [
            'field1' => [
                static::KEYWORD => [
                    'equals' => ['multiValue' => $modifiedValue],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function modifyEmptyMultiValue(): void
    {
        $this->data['field1'] = new MultiValue();
        $config = [
            'field1' => [
                static::KEYWORD => [
                    'empty' => true,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }
}
