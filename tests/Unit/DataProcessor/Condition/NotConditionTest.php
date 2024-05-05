<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\NotCondition;

class NotConditionTest extends ConditionTest
{
    protected const CLASS_NAME = NotCondition::class;

    protected const KEYWORD = 'not';

    /** @test */
    public function notTrue(): void
    {
        $config = [
            'configKey1' => 'configValue1',
        ];
        $this->dataProcessor->method('processCondition')->with($config)->willReturn(true);
        $result = $this->processCondition($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notFalse(): void
    {
        $config = [
            'configKey1' => 'configValue1',
        ];
        $this->dataProcessor->method('processCondition')->with($config)->willReturn(false);
        $result = $this->processCondition($config);
        $this->assertTrue($result);
    }
}
