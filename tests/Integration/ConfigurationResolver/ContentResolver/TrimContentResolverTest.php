<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;

/**
 * @covers TrimContentResolver
 */
class TrimContentResolverTest extends AbstractModifierContentResolverTest
{
    protected const KEYWORD = 'trim';

    public function modifyProvider(): array
    {
        return [
            [null,          null],
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
            [[], []],
            [['', ' ', ' value3 ', 'value4'], ['', '', 'value3', 'value4']],
        ];
    }
}
