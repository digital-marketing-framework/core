<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Data;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    protected Data $subject;

    public function toArrayProvider(): array
    {
        return [
            [[]],
            [[
                'key1' => 'value1',
                'key2' => 'value2'
            ]],
            [[
                'key1' => [
                    'key1.1' => 'value1.1',
                    'key1.2' => 'value1.2',
                ]
            ]],
        ];
    }

    /**
     * @param $values
     * @dataProvider toArrayProvider
     * @test
     */
    public function toArray($values)
    {
        $this->subject = new Data($values);
        $result = $this->subject->toArray();
        $this->assertEquals($values, $result);
    }

    /** @test */
    public function setGetOffset()
    {
        $this->subject = new Data();
        $this->subject['key1'] = 'value1';
        $this->subject['key2']['key2.1'] = 'value2.1';
        $this->assertEquals('value1', $this->subject['key1']);
        $this->assertEquals('value2.1', $this->subject['key2']['key2.1']);
    }

    public function fieldExistsProvider(): array
    {
        return [
            [null],
            [''],
            ['0'],
            ['1'],
            ['value1'],
        ];
    }

    /**
     * @param $value
     * @dataProvider fieldExistsProvider
     * @test
     */
    public function fieldExists($value)
    {
        $this->subject = new Data(['field1' => $value]);
        $this->assertTrue($this->subject->fieldExists('field1'));
    }

    /** @test */
    public function fieldDoesNotExist()
    {
        $this->subject = new Data(['field1' => 'value1']);
        $this->assertTrue($this->subject->fieldExists('field1'));
        $this->assertFalse($this->subject->fieldExists('field2'));
    }

    /** @test */
    public function fieldDoesNotExistIsEmpty()
    {
        $this->subject = new Data();
        $this->assertTrue($this->subject->fieldEmpty('field1'));
    }

    public function fieldEmptyProvider(): array
    {
        return [
            [null, true],
            ['', true],
            ['0', false],
            ['value1', false],
        ];
    }

    /**
     * @param $value
     * @param $empty
     * @dataProvider fieldEmptyProvider
     * @test
     */
    public function fieldEmpty($value, $empty)
    {
        $this->subject = new Data(['field1' => $value]);
        if ($empty) {
            $this->assertTrue($this->subject->fieldEmpty('field1'));
        } else {
            $this->assertFalse($this->subject->fieldEmpty('field1'));
        }
    }

    // TODO test pack and unpack
}
