<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\UpperCaseEvaluation;

/**
 * @covers UpperCaseEvaluation
 */
class UpperCaseEvaluationTest extends AbstractModifierEvaluationTest
{
    protected const KEYWORD = 'upperCase';

    public function modifyProvider(): array
    {
        return [
            ['value1', 'VALUE1'],
            ['VALUE1', 'VALUE1'],
            ['1_2_3',  '1_2_3'],
        ];
    }

    public function modifyMultiValueProvider(): array
    {
        return [
            [['Value1', 'VALUE2', 'value3'], ['VALUE1', 'VALUE2', 'VALUE3']],
        ];
    }
}
