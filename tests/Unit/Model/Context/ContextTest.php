<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Context;

use DigitalMarketingFramework\Core\Model\Context\Context;
use DigitalMarketingFramework\Core\Model\Context\ContextInterface;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    protected Context $subject;

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
        $this->subject = new Context($values);
        $result = $this->subject->toArray();
        $this->assertEquals($values, $result);
    }

    /** @test */
    public function setGetOffset()
    {
        $this->subject = new Context();
        $this->subject['key1'] = 'value1';
        $this->subject['key2']['key2.1'] = 'value2.1';
        $this->assertEquals('value1', $this->subject['key1']);
        $this->assertEquals('value2.1', $this->subject['key2']['key2.1']);
    }

    /** @test */
    public function setInNamespace()
    {
        $this->subject = new Context();
        $this->subject->setInNamespace('namespace1', 'key1', 'value1');
        $result = $this->subject->toArray();
        $this->assertEquals(['namespace1' => ['key1' => 'value1']], $result);
    }

    /** @test */
    public function addToNamespace()
    {
        $this->subject = new Context();
        $this->subject->setInNamespace('namespace1', 'key1', 'value1');
        $this->subject->addToNamespace('namespace1', ['key2' => 'value2']);
        $result = $this->subject->toArray();
        $this->assertEquals(['namespace1' => ['key1' => 'value1', 'key2' => 'value2']], $result);
    }

    /** @test */
    public function getFromNamespace()
    {
        $this->subject = new Context();
        $this->subject->setInNamespace('namespace1', 'key1', 'value1');
        $this->assertEquals('value1', $this->subject->getFromNamespace('namespace1', 'key1'));
        $this->assertEquals('value1', $this->subject->getFromNamespace('namespace1', 'key1', 'default1'));
        $this->assertNull($this->subject->getFromNamespace('namespace1', 'key2'));
        $this->assertEquals('default1', $this->subject->getFromNamespace('namespace1', 'key2', 'default1'));
    }

    /** @test */
    public function getNamespaceData()
    {
        $this->subject = new Context();
        $this->subject->setInNamespace('namespace1', 'key1', 'value1');
        $this->assertEquals(['key1' => 'value1'], $this->subject->getNamespaceData('namespace1'));
        $this->assertEquals([], $this->subject->getNamespaceData('namespace2'));
    }

    /** @test */
    public function removeFromNamespace()
    {
        $this->subject = new Context();
        $this->subject->setInNamespace('namespace1', 'key1', 'value1');
        $this->subject->removeFromNamespace('namespace1', 'key1');
        $this->assertNull($this->subject->getFromNamespace('namespace1', 'key1'));
    }

    /** @test */
    public function clearNamespace()
    {
        $this->subject = new Context();
        $this->subject->setInNamespace('namespace1', 'key1', 'value1');
        $this->subject->setInNamespace('namespace1', 'key2', 'value2');
        $this->subject->clearNamespace('namespace1');
        $this->assertEmpty($this->subject->getNamespaceData('namespace1'));
    }

    /** @test */
    public function setCookie()
    {
        $this->subject = new Context();
        $this->subject->setCookie('key1', 'value1');
        $result = $this->subject->toArray();
        $this->assertEquals([ContextInterface::NAMESPACE_COOKIES => ['key1' => 'value1']], $result);
    }

    /** @test */
    public function addCookies()
    {
        $this->subject = new Context();
        $this->subject->setCookie('key1', 'value1');
        $this->subject->addCookies(['key2' => 'value2']);
        $result = $this->subject->toArray();
        $this->assertEquals([ContextInterface::NAMESPACE_COOKIES => ['key1' => 'value1', 'key2' => 'value2']], $result);
    }

    /** @test */
    public function getCookie()
    {
        $this->subject = new Context();
        $this->subject->setCookie('key1', 'value1');
        $this->assertEquals('value1', $this->subject->getCookie('key1'));
        $this->assertEquals('value1', $this->subject->getCookie('key1', 'default1'));
        $this->assertNull($this->subject->getCookie('key2'));
        $this->assertEquals('default1', $this->subject->getCookie('key2', 'default1'));
    }

    /** @test */
    public function getCookies()
    {
        $this->subject = new Context();
        $this->subject->setCookie('key1', 'value1');
        $this->assertEquals(['key1' => 'value1'], $this->subject->getCookies());
    }

    /** @test */
    public function removeCookie()
    {
        $this->subject = new Context();
        $this->subject->setCookie('key1', 'value1');
        $this->subject->removeCookie('key1');
        $this->assertNull($this->subject->getCookie('key1'));
    }

    /** @test */
    public function clearCookies()
    {
        $this->subject = new Context();
        $this->subject->setCookie('key1', 'value1');
        $this->subject->setCookie('key2', 'value2');
        $this->subject->clearCookies();
        $this->assertEmpty($this->subject->getCookies());
    }

    /** @test */
    public function setRequestVariable()
    {
        $this->subject = new Context();
        $this->subject->setRequestVariable('key1', 'value1');
        $result = $this->subject->toArray();
        $this->assertEquals([ContextInterface::NAMESPACE_REQUEST_VARIABLES => ['key1' => 'value1']], $result);
    }

    /** @test */
    public function addRequestVariables()
    {
        $this->subject = new Context();
        $this->subject->setRequestVariable('key1', 'value1');
        $this->subject->addRequestVariables(['key2' => 'value2']);
        $result = $this->subject->toArray();
        $this->assertEquals([ContextInterface::NAMESPACE_REQUEST_VARIABLES => ['key1' => 'value1', 'key2' => 'value2']], $result);
    }

    /** @test */
    public function getRequestVariable()
    {
        $this->subject = new Context();
        $this->subject->setRequestVariable('key1', 'value1');
        $this->assertEquals('value1', $this->subject->getRequestVariable('key1'));
        $this->assertEquals('value1', $this->subject->getRequestVariable('key1', 'default1'));
        $this->assertNull($this->subject->getRequestVariable('key2'));
        $this->assertEquals('default1', $this->subject->getRequestVariable('key2', 'default1'));
    }

    /** @test */
    public function getRequestVariables()
    {
        $this->subject = new Context();
        $this->subject->setRequestVariable('key1', 'value1');
        $this->assertEquals(['key1' => 'value1'], $this->subject->getRequestVariables());
    }

    /** @test */
    public function removeRequestVariable()
    {
        $this->subject = new Context();
        $this->subject->setRequestVariable('key1', 'value1');
        $this->subject->removeRequestVariable('key1');
        $this->assertNull($this->subject->getRequestVariable('key1'));
    }

    /** @test */
    public function clearRequestVariables()
    {
        $this->subject = new Context();
        $this->subject->setRequestVariable('key1', 'value1');
        $this->subject->setRequestVariable('key2', 'value2');
        $this->subject->clearRequestVariables();
        $this->assertEmpty($this->subject->getRequestVariables());
    }
}
