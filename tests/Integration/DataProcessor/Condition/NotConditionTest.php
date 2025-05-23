<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\NotCondition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(NotCondition::class)]
class NotConditionTest extends ConditionTestBase
{
    protected const KEYWORD = 'not';

    #[Test]
    public function notTrue(): void
    {
        $config = $this->getConditionConfiguration([], 'true');
        $result = $this->processCondition($config);
        $this->assertFalse($result);
    }

    #[Test]
    public function notFalse(): void
    {
        $config = $this->getConditionConfiguration([], 'false');
        $result = $this->processCondition($config);
        $this->assertTrue($result);
    }
}
