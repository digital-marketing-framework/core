<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;

/**
 * @extends AbstractFieldTest<MultiValue>
 */
class MultiValueTest extends AbstractFieldTest
{
    use MultiValueTestTrait;

    protected const FIELD_CLASS = MultiValue::class;

    protected function createField(mixed ...$arguments): MultiValueInterface
    {
        if ($arguments === []) {
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

    /**
     * @return array<array{0:array<mixed>,1:string}>
     */
    public function castToStringProvider(): array
    {
        return [
            [[[]],         ''],
            [[[5, 7, 17]], '5,7,17'],
            [[['', '']],    ','],
        ];
    }

    /**
     * @return array<array{0:string,1:array<mixed>,2:string}>
     */
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
     * @param array<mixed> $values
     *
     * @dataProvider castToStringWithGlueProvider
     *
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

    /**
     * @return array<array{0:array<array<mixed>>,1:array<array{type:string,value:mixed}>}>
     */
    public function packProvider(): array
    {
        return [
            [[[]], []],
            [[[5, 7, 17]], [['type' => 'string', 'value' => '5'], ['type' => 'string', 'value' => '7'], ['type' => 'string', 'value' => '17']]],
        ];
    }

    // TODO test packUnpackNested
}
