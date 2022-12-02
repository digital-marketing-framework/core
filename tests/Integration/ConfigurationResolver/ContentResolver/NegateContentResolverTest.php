<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers NegateContentResolver
 */
class NegateContentResolverTest extends AbstractContentResolverTest
{
    public function provider(): array
    {
        return [
            // value, true, false, expected
            [null,     null,     null,     /* => */ null],
            [null,     '1',      '0',      /* => */ null],
            [null,     '0',      '1',      /* => */ null],
            [null,     'value1', null,     /* => */ null],
            [null,     null,     'value2', /* => */ null],
            [null,     'value1', 'value2', /* => */ null],

            ['',       null,     null,     /* => */ '1'],
            ['',       '1',      '0',      /* => */ '1'],
            ['',       '0',      '1',      /* => */ '0'],
            ['',       'value1', null,     /* => */ 'value1'],
            ['',       null,     'value2', /* => */ '1'],
            ['',       'value1', 'value2', /* => */ 'value1'],

            ['0',      null,     null,     /* => */ '1'],
            ['0',      '1',      '0',      /* => */ '1'],
            ['0',      '0',      '1',      /* => */ '1'],
            ['0',      'value1', null,     /* => */ 'value1'],
            ['0',      null,     'value2', /* => */ '1'],
            ['0',      'value1', 'value2', /* => */ 'value1'],

            ['1',      null,     null,     /* => */ '0'],
            ['1',      '1',      '0',      /* => */ '0'],
            ['1',      '0',      '1',      /* => */ '0'],
            ['1',      'value1', null,     /* => */ '0'],
            ['1',      null,     'value2', /* => */ 'value2'],
            ['1',      'value1', 'value2', /* => */ 'value2'],

            ['value1', null,     null,     /* => */ '0'],
            ['value1', '1',      '0',      /* => */ '0'],
            ['value1', '0',      '1',      /* => */ '1'],
            ['value1', 'value1', null,     /* => */ '0'],
            ['value1', null,     'value2', /* => */ 'value2'],
            ['value1', 'value1', 'value2', /* => */ 'value2'],

            ['value2', null,     null,     /* => */ '0'],
            ['value2', '1',      '0',      /* => */ '0'],
            ['value2', '0',      '1',      /* => */ '1'],
            ['value2', 'value1', null,     /* => */ '0'],
            ['value2', null,     'value2', /* => */ '1'],
            ['value2', 'value1', 'value2', /* => */ 'value1'],

            ['value3', null,     null,     /* => */ '0'],
            ['value3', '1',      '0',      /* => */ '0'],
            ['value3', '0',      '1',      /* => */ '1'],
            ['value3', 'value1', null,     /* => */ '0'],
            ['value3', null,     'value2', /* => */ 'value2'],
            ['value3', 'value1', 'value2', /* => */ 'value2'],
        ];
    }

    protected function runNegate(mixed $value, mixed $true, mixed $false, mixed $negate, mixed $expected, bool $useNullOnTrue, bool $useNullOnFalse): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => $value,
        ];
        if ($true !== null || $false !== null || $useNullOnTrue || $useNullOnFalse) {
            $config['negate'] = [
                ConfigurationResolverInterface::KEY_SELF => $negate,
            ];
        } else {
            $config['negate'] = $negate;
        }
        if ($true !== null || $useNullOnTrue) {
            $config['negate']['true'] = $true;
        }
        if ($false !== null || $useNullOnFalse) {
            $config['negate']['false'] = $false;
        }
        $result = $this->runResolverProcess($config);
        if ($expected === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function negateEnabled(mixed $value, mixed $true, mixed $false, mixed $expected): void
    {
        $this->runNegate($value, $true, $false, true, $expected, false, false);
        if ($true === null) {
            $this->runNegate($value, $true, $false, true, $expected, true, false);
        }
        if ($false === null) {
            $this->runNegate($value, $true, $false, true, $expected, false, true);
        }
        if ($false === null && $true === null) {
            $this->runNegate($value, $true, $false, true, $expected, true, true);
        }
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function negateDisabled(mixed $value, mixed $true, mixed $false, mixed $expected): void
    {
        $this->runNegate($value, $true, $false, false, $value, false, false);
        if ($true === null) {
            $this->runNegate($value, $true, $false, false, $value, true, false);
        }
        if ($false === null) {
            $this->runNegate($value, $true, $false, false, $value, false, true);
        }
        if ($false === null && $true === null) {
            $this->runNegate($value, $true, $false, false, $value, true, true);
        }
    }

    protected function runNegateMultiValue(mixed $value, mixed $true, mixed $false, mixed $negate, mixed $expected, bool $useNullOnTrue, bool $useNullOnFalse): void
    {
        $expected = $expected === null ? [] : [$expected];
        $config = [
            'multiValue' => $value === null ? [] : [$value],
        ];
        if ($true !== null || $false !== null || $useNullOnTrue || $useNullOnFalse) {
            $config['negate'] = [
                ConfigurationResolverInterface::KEY_SELF => $negate,
            ];
        } else {
            $config['negate'] = $negate;
        }
        if ($true !== null || $useNullOnTrue) {
            $config['negate']['true'] = $true;
        }
        if ($false !== null || $useNullOnFalse) {
            $config['negate']['false'] = $false;
        }
        $result = $this->runResolverProcess($config);
        if (empty($expected)) {
            $this->assertMultiValueEmpty($result);
        } else {
            $this->assertMultiValueEquals($expected, $result);
        }
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function negateMultiValueEnabled(mixed $value, mixed $true, mixed $false, mixed $expected): void
    {
        $this->runNegateMultiValue($value, $true, $false, true, $expected, false, false);
        if ($true === null) {
            $this->runNegateMultiValue($value, $true, $false, true, $expected, true, false);
        }
        if ($false === null) {
            $this->runNegateMultiValue($value, $true, $false, true, $expected, false, true);
        }
        if ($false === null && $true === null) {
            $this->runNegateMultiValue($value, $true, $false, true, $expected, true, true);
        }
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function negateMultiValueDisabled(mixed $value, mixed $true, mixed $false, mixed $expected): void
    {
        $this->runNegateMultiValue($value, $true, $false, false, $value, false, false);
        if ($true === null) {
            $this->runNegateMultiValue($value, $true, $false, false, $value, true, false);
        }
        if ($false === null) {
            $this->runNegateMultiValue($value, $true, $false, false, $value, false, true);
        }
        if ($false === null && $true === null) {
            $this->runNegateMultiValue($value, $true, $false, false, $value, true, true);
        }
    }
}
