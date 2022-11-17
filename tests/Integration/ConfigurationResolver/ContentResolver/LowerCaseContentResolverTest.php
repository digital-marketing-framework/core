<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\LowerCaseContentResolver;

/**
 * @covers LowerCaseContentResolver
 */
class LowerCaseContentResolverTest extends AbstractModifierContentResolverTest
{
    protected const KEYWORD = 'lowerCase';

    public function modifyProvider(): array
    {
        return [
            [null,     null],
            ['VALUE1', 'value1'],
            ['value1', 'value1'],
            ['1_2_3',  '1_2_3'],
        ];
    }

    public function modifyMultiValueProvider(): array
    {
        return [
            [[], []],
            [['Value1', 'VALUE2', 'value3'], ['value1', 'value2', 'value3']],
        ];
    }
}
