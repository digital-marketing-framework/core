<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\RegexpEvaluation;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers RegexpEvaluation
 */
class RegexpEvaluationTest extends AbstractEvaluationTest
{
    public function regexpProvider(): array
    {
        return [
            // value, regexp, match
            ['value1', 'value1',      true],
            ['value1', 'value2',      false],
            ['value1', 'alu',         true],
            ['value1', '^alu',        false],
            ['value1', '^val',        true],
            ['value1', 'val$',        false],
            ['value1', 'ue1$',        true],
            ['value1', '^[a-z0-9]+$', true],
        ];
    }

    public function regexpMultiValueProvider(): array
    {
        return [
            // value, regexp, match
            [['value1'],          'value1',     true],
            [['value1'],          'value[123]', true],
            [['value1','value2'], 'value1',     true],
            [['value1','value2'], 'abc',        false],
        ];
    }

    /**
     * @dataProvider regexpProvider
     * @test
     */
    public function regexp(string $value, string $regexp, bool $match): void
    {
        $this->data['field1'] = $value;
        $config = [
            'field1' => [
                'regexp' => $regexp,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($match) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @dataProvider regexpMultiValueProvider
     * @test
     */
    public function regexpMultiValue(array $value, string $regexp, bool $match): void
    {
        $this->data['field1'] = new MultiValue($value);
        $config = [
            'field1' => [
                'regexp' => $regexp,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($match) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
