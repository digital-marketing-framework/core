<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    protected Data $subject;

    /**
     * @return array<array{0:array<string,mixed>}>
     */
    public static function toArrayProvider(): array
    {
        return [
            [[]],
            [[
                'key1' => 'value1',
                'key2' => 'value2',
            ]],
            [[
                'key1' => [
                    'key1.1' => 'value1.1',
                    'key1.2' => 'value1.2',
                ],
            ]],
        ];
    }

    /**
     * @param array<string,mixed> $values
     */
    #[Test]
    #[DataProvider('toArrayProvider')]
    public function toArray(array $values): void
    {
        $this->subject = new Data($values);
        $result = $this->subject->toArray();
        $this->assertEquals($values, $result);
    }

    #[Test]
    public function setGetOffset(): void
    {
        $this->subject = new Data();
        $this->subject['key1'] = 'value1';
        $this->subject['key2']['key2.1'] = 'value2.1';
        $this->assertEquals('value1', $this->subject['key1']);
        $this->assertEquals('value2.1', $this->subject['key2']['key2.1']);
    }

    /**
     * @return array<array{0:string|ValueInterface|null}>
     */
    public static function fieldExistsProvider(): array
    {
        return [
            [null],
            [''],
            ['0'],
            ['1'],
            ['value1'],
        ];
    }

    #[Test]
    #[DataProvider('fieldExistsProvider')]
    public function fieldExists(string|ValueInterface|null $value): void
    {
        $this->subject = new Data(['field1' => $value]);
        $this->assertTrue($this->subject->fieldExists('field1'));
    }

    #[Test]
    public function fieldDoesNotExist(): void
    {
        $this->subject = new Data(['field1' => 'value1']);
        $this->assertTrue($this->subject->fieldExists('field1'));
        $this->assertFalse($this->subject->fieldExists('field2'));
    }

    #[Test]
    public function fieldDoesNotExistIsEmpty(): void
    {
        $this->subject = new Data();
        $this->assertTrue($this->subject->fieldEmpty('field1'));
    }

    /**
     * @return array<array{0:string|ValueInterface|null,1:bool}>
     */
    public static function fieldEmptyProvider(): array
    {
        return [
            [null, true],
            ['', true],
            ['0', false],
            ['value1', false],
        ];
    }

    #[Test]
    #[DataProvider('fieldEmptyProvider')]
    public function fieldEmpty(string|ValueInterface|null $value, bool $empty): void
    {
        $this->subject = new Data(['field1' => $value]);
        if ($empty) {
            $this->assertTrue($this->subject->fieldEmpty('field1'));
        } else {
            $this->assertFalse($this->subject->fieldEmpty('field1'));
        }
    }

    // TODO test pack and unpack, extend MultiValueTest
}
