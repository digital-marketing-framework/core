<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\TrueCondition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(TrueCondition::class)]
class TrueConditionTest extends ConditionTestBase
{
    protected const KEYWORD = 'true';

    #[Test]
    public function true(): void
    {
        $result = $this->processCondition([]);
        $this->assertTrue($result);
    }
}
