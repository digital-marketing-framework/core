<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\LoopDataContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers LoopDataContentResolver
 */
class LoopDataContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function loopData(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $config = [
            'loopData' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("field1 = value1\nfield2 = value2\nfield3 = value3\n", $result);
    }

    /** @test */
    public function loopDataInsertDataTemplate(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $config = [
            'loopData' => [
                'template' => [
                    ConfigurationResolverInterface::KEY_SELF => '{key}:{value};',
                    'insertData' => true,
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("field1:value1;field2:value2;field3:value3;", $result);
    }

    /** @test */
    public function loopDataWithGlue(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $config = [
            'loopData' => [
                'template' => ['field' => 'value'],
                'glue' => ','
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2,value3', $result);
    }

    /** @test */
    public function loopDataWithCustomVars(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $config = [
            'loopData' => [
                'glue' => ',',
                'asKey' => 'customKey',
                'as' => 'customValue',
                'template' => [
                    ConfigurationResolverInterface::KEY_SELF => '{customKey}={customValue}',
                    'insertData' => true,
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('field1=value1,field2=value2,field3=value3', $result);
    }

    /** @test */
    public function loopDataWithValueCondition(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $config = [
            'loopData' => [
                'glue' => ',',
                'condition' => [
                    'in' => 'value1,value3',
                ],
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value3', $result);
    }

    /** @test */
    public function loopDataWithKeyCondition(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $config = [
            'loopData' => [
                'glue' => ',',
                'condition' => [
                    'key' => [
                        'in' => 'field1,field3',
                    ],
                ],
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value3', $result);
    }

    /** @test */
    public function loopDataWithOtherCondition(): void
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $config = [
            'loopData' => [
                'glue' => ',',
                'condition' => [
                    'field3' => 'value3',
                ],
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2,value3', $result);
    }

    /** @test */
    public function loopDataFieldTemplateMultiValuesWithLoopGlue(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 17]);
        $this->data['field2'] = 's';
        $this->data['field3'] = new MultiValue(['c', 7, 'k']);
        $config = [
            'loopData' => [
                'glue' => ';',
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('5,7,17;s;c,7,k', $result);
    }

    /** @test */
    public function loopDataFieldTemplateOneMultiValue(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 17]);
        $config = [
            'loopData' => [
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([5, 7, 17], $result);
    }

    /** @test */
    public function loopDataFieldTemplateOneMultiValueJoined(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 17]);
        $config = [
            'loopData' => [
                'template' => [
                    'field' => 'value',
                    'join' => true,
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("5\n7\n17", $result);
    }

    /** @test */
    public function loopDataFieldTemplateOneMultiValueJoinedWithGlue(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 17]);
        $config = [
            'loopData' => [
                'template' => [
                    'field' => 'value',
                    'join' => [
                        'glue' => '-',
                    ],
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('5-7-17', $result);
    }
}
