<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\RequiredEvaluation;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers RequiredEvaluation
 */
class RequiredEvaluationTest extends AbstractEvaluationTest
{
    public function requiredProvider(): array
    {
        return [
            [['notEmptyField'],                                 true],
            [['emptyField'],                                    false],
            [['notExistingField'],                              false],
            [['notEmptyField','notEmptyField2'],                true],
            [['emptyField','notEmptyField'],                    false],
            [['emptyField','notExistingField'],                 false],
            [['notEmptyField','notExistingField'],              false],
            [['emptyField','notEmptyField','notExistingField'], false],
        ];
    }

    /**
     * @dataProvider requiredProvider
     * @test
     */
    public function required(array $required, bool $expected): void
    {
        $this->data['notEmptyField'] = 'value1';
        $this->data['notEmptyField2'] = 'value2';
        $this->data['emptyField'] = '';
        $this->data['emptyField2'] = '';
        $config = [
            'required' => implode(',', $required),
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @dataProvider requiredProvider
     * @test
     */
    public function requiredList(array $required, bool $expected): void
    {
        $this->data['notEmptyField'] = 'value1';
        $this->data['notEmptyField2'] = 'value2';
        $this->data['emptyField'] = '';
        $this->data['emptyField2'] = '';
        $config = [
            'required' => ['list' => $required],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @dataProvider requiredProvider
     * @test
     */
    public function requiredComplexContentResolver(array $required, bool $expected): void
    {
        $this->data['notEmptyField'] = 'value1';
        $this->data['notEmptyField2'] = 'value2';
        $this->data['emptyField'] = '';
        $this->data['emptyField2'] = '';
        $config = [
            'required' => ['glue' => ','],
        ];
        foreach ($required as $field) {
            $config['required'][] = $field;
        }
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @dataProvider requiredProvider
     * @test
     */
    public function requiredMultiValue(array $required, bool $expected): void
    {
        $this->data['notEmptyField'] = new MultiValue(['value1']);
        $this->data['notEmptyField2'] = new MultiValue(['value2']);
        $this->data['emptyField'] = new MultiValue();
        $this->data['emptyField2'] = new MultiValue();
        $config = [
            'required' => implode(',', $required),
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @dataProvider requiredProvider
     * @test
     */
    public function requiredListMultiValue(array $required, bool $expected): void
    {
        $this->data['notEmptyField'] = new MultiValue(['value1']);
        $this->data['notEmptyField2'] = new MultiValue(['value2']);
        $this->data['emptyField'] = new MultiValue();
        $this->data['emptyField2'] = new MultiValue();
        $config = [
            'required' => ['list' => $required],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
