<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

abstract class AbstractModifierEvaluationTest extends AbstractEvaluationTest
{
    protected const KEYWORD = '';

    abstract public function modifyProvider(): array;
    abstract public function modifyMultiValueProvider(): array;

    /**
     * @param $value
     * @param $modifiedValue
     * @dataProvider modifyProvider
     * @test
     */
    public function modify($value, $modifiedValue)
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
     * @param array $value
     * @param array $modifiedValue
     * @dataProvider modifyMultiValueProvider
     * @test
     */
    public function modifyMultiValue(array $value, array $modifiedValue)
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
    public function modifyEmptyMultiValue()
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
