<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\FalseCondition;
use PHPUnit\Framework\Attributes\Test;

class FalseConditionTest extends ConditionTestBase
{
    protected const CLASS_NAME = FalseCondition::class;

    protected const KEYWORD = 'false';

    #[Test]
    public function false(): void
    {
        $result = $this->processCondition([]);
        $this->assertFalse($result);
    }
}
