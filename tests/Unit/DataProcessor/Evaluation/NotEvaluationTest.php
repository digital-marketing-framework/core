<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\NotEvaluation;

class NotEvaluationTest extends EvaluationTest
{
    protected const CLASS_NAME = NotEvaluation::class;

    protected const KEYWORD = 'not';

    /** @test */
    public function notTrue(): void
    {
        $config = [
            'configKey1' => 'configValue1',
        ];
        $this->dataProcessor->method('processEvaluation')->with($config)->willReturn(true);
        $result = $this->processEvaluation($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notFalse(): void
    {
        $config = [
            'configKey1' => 'configValue1',
        ];
        $this->dataProcessor->method('processEvaluation')->with($config)->willReturn(false);
        $result = $this->processEvaluation($config);
        $this->assertTrue($result);
    }
}
