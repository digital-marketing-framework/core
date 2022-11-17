<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\TrimEvaluation;

/**
 * @covers TrimEvaluation
 */
class TrimEvaluationTest extends AbstractModifierEvaluationTest
{
    protected const KEYWORD = 'trim';

    public function modifyProvider(): array
    {
        return [
            ["",            ""],
            [" ",           ""],
            ["\t",          ""],
            ["\n",          ""],
            [" value1 ",    "value1"],
            ["val ue1",     "val ue1"],
            [" val ue1 ",   "val ue1"],
            ["value1",      "value1"],
            ["\t value1\n", "value1"],
        ];
    }

    public function modifyMultiValueProvider(): array
    {
        return [
            [[' value3 ', 'value4'], ['value3', 'value4']],
        ];
    }
}
