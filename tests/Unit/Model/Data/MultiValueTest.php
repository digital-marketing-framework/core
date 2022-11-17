<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;

class MultiValueTest extends AbstractFieldTest
{
    use MultiValueTestTrait;

    protected const FIELD_CLASS = MultiValue::class;

    protected function createField(...$arguments): MultiValueInterface
    {
        if (empty($arguments)) {
            $arguments = [[5, 7, 17]];
        }
        return parent::createField(...$arguments);
    }


    /** @test */
    public function init(): void
    {
        $this->subject = $this->createField([5, 7, 17]);
        $this->assertMultiValueEquals([5, 7, 17], $this->subject, static::FIELD_CLASS);
    }

    /** @test */
    public function initEmpty(): void
    {
        $this->subject = $this->createField([]);
        $this->assertMultiValueEmpty($this->subject, static::FIELD_CLASS);
    }

    public function castToStringProvider(): array
    {
        return [
            [[[]],         ''],
            [[[5, 7, 17]], '5,7,17'],
            [[['','']],    ','],
        ];
    }

    public function castToStringWithGlueProvider(): array
    {
        return [
            [';', [5, 7, 17], '5;7;17'],
            [';', [],         ''],
            [';', ['', ''],   ';'],
            ['',  [5, 7, 17], '5717'],
            ['',  [],         ''],
            ['',  ['', ''],   ''],
        ];
    }

    /**
     * @dataProvider castToStringWithGlueProvider
     * @test
     */
    public function castToStringWithGlue(string $glue, array $values, string $stringRepresentation): void
    {
        $this->subject = $this->createField($values);
        $this->subject->setGlue($glue);
        $result = (string)$this->subject;
        $this->assertEquals($stringRepresentation, $result);
    }

    /** @test */
    public function castToStringNested(): void
    {
        $this->subject = $this->createField([
            'a',
            $this->createField(['x', 'y', 'z']),
            'c',
        ]);
        $result = (string)$this->subject;
        $this->assertEquals('a,x,y,z,c', $result);
    }

    public function packProvider(): array
    {
        return [
            [[[]], []],
            [[[5, 7 ,17]], [['type' => 'string', 'value' => '5'], ['type' => 'string', 'value' => '7'], [ 'type' => 'string', 'value' => '17']]],
        ];
    }

    // TODO test packUnpackNested
}
