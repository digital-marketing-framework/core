<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\SprintfValueModifier;

class SprintfValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'sprintf';

    protected const CLASS_NAME = SprintfValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null, null, [SprintfValueModifier::KEY_FORMAT => 'format1']],
        [null, null, [SprintfValueModifier::KEY_FORMAT => '']],
        ['value1', '', [SprintfValueModifier::KEY_FORMAT => '']],
        ['value1', 'format1', [SprintfValueModifier::KEY_FORMAT => 'format1']],
        ['value1', 'value1', [SprintfValueModifier::KEY_FORMAT => '%s']],
        ['value1', 'format:value1', [SprintfValueModifier::KEY_FORMAT => 'format:%s']],

        ['1.2', '1.20', [SprintfValueModifier::KEY_FORMAT => '%01.2f']],
        [1.2, '1.20', [SprintfValueModifier::KEY_FORMAT => '%01.2f']],
        [34.567, '34.57', [SprintfValueModifier::KEY_FORMAT => '%01.2f']],

        [['value1', 'value2'], 'format1', [SprintfValueModifier::KEY_FORMAT => 'format1']],
        [['value1', 'value2'], 'value1', [SprintfValueModifier::KEY_FORMAT => '%s']],
        [['value1', 'value2'], 'value1:value2', [SprintfValueModifier::KEY_FORMAT => '%s:%s']],
        [[1.2, 34.567], '1.20 - 34.57', [SprintfValueModifier::KEY_FORMAT => '%01.2f - %01.2f']],
    ];

    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
