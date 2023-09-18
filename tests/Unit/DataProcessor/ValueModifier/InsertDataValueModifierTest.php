<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\InsertDataValueModifier;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

class InsertDataValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'insertData';

    protected const CLASS_NAME = InsertDataValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null,                    null],
        ['',                      ''],
        ['{field1}',              'value1'],
        ['{field1}-{field2}',     'value1-value2'],
        ['{field4}',              ''],
        ['{multiValue}',          ['a', 'b', 'c']],
        ['-{multiValue}',         '-a,b,c'],
        ['{field2}-{multiValue}', 'value2-a,b,c'],
        ['{field1}{field4}',      'value1'],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $this->data['field3'] = 'value3';
        $this->data['multiValue'] = new MultiValue(['a', 'b', 'c']);
    }

    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
