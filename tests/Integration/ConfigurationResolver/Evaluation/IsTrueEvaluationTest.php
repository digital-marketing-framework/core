<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\IsTrueEvaluation;

/**
 * @covers IsTrueEvaluation
 */
class IsTrueEvaluationTest extends IsFalseEvaluationTest
{
    protected const KEYWORD = 'isTrue';

    public function isProvider(): array
    {
        $provided = parent::isProvider();
        foreach ($provided as $index => $arguments) {
            $provided[$index][2] = !$arguments[2];
        }
        return $provided;
    }

    public function isMultiValueProvider(): array
    {
        return [
            // value, is, => expected
            [[],             true,  /* => */ false],
            [[],             false, /* => */ true],
            [[''],           true,  /* => */ true],
            [[''],           false, /* => */ false],
            [['value1'],     true,  /* => */ true],
            [['value1'],     false, /* => */ false],
            [['', 'value2'], true,  /* => */ true],
            [['', 'value2'], false, /* => */ false],
            [['value1', ''], true,  /* => */ true],
            [['value1', ''], false, /* => */ false],
        ];
    }

    public function anyIsMultiValueProvider(): array
    {
        return [
            // value, is, => expected
            [[],                   true,  /* => */ false],
            [[],                   false, /* => */ false],
            [[''],                 true,  /* => */ false],
            [[''],                 false, /* => */ true],
            [['value1'],           true,  /* => */ true],
            [['value1'],           false, /* => */ false],
            [['value1', 'value2'], true,  /* => */ true],
            [['value1', 'value2'], false, /* => */ false],
            [['', 'value2'],       true,  /* => */ true],
            [['', 'value2'],       false, /* => */ true],
            [['value1', ''],       true,  /* => */ true],
            [['value1', ''],       false, /* => */ true],
        ];
    }

    public function allIsMultiValueProvider(): array
    {
        return [
            // value, is, => expected
            [[],                   true,  /* => */ true],
            [[],                   false, /* => */ true],
            [[''],                 true,  /* => */ false],
            [[''],                 false, /* => */ true],
            [['value1'],           true,  /* => */ true],
            [['value1'],           false, /* => */ false],
            [['value1', 'value2'], true,  /* => */ true],
            [['value1', 'value2'], false, /* => */ false],
            [['', 'value2'],       true,  /* => */ false],
            [['', 'value2'],       false, /* => */ false],
            [['value1', ''],       true,  /* => */ false],
            [['value1', ''],       false, /* => */ false],
        ];
    }
}
