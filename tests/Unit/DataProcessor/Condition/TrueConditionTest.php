<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\TrueCondition;
use PHPUnit\Framework\Attributes\Test;

class TrueConditionTest extends ConditionTestBase
{
    protected const CLASS_NAME = TrueCondition::class;

    protected const KEYWORD = 'true';

    #[Test]
    public function true(): void
    {
        $result = $this->processCondition([]);
        $this->assertTrue($result);
    }
}
