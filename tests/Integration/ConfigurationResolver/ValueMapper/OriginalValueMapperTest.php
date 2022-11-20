<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper\OriginalValueMapper;

/**
 * @covers OriginalValueMapper
 */
class OriginalValueMapperTest extends AbstractValueMapperTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
    }

    /** @test */
    public function original(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'originalValue' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function ifOriginal(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value2',
                'then' => [
                    'originalValue' => true,
                ],
                'else' => [
                    'value1' => 'value1b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }
}
