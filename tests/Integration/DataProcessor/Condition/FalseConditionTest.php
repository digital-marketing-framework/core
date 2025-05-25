<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\FalseCondition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(FalseCondition::class)]
class FalseConditionTest extends ConditionTestBase
{
    protected const KEYWORD = 'false';

    #[Test]
    public function false(): void
    {
        $result = $this->processCondition([]);
        $this->assertFalse($result);
    }
}
