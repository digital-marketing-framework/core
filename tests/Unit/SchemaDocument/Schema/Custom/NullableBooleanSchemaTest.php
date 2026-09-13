<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\InheritableBooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\NullableBooleanSchema;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class NullableBooleanSchemaTest extends TestCase
{
    /**
     * @return array<string,array{0:string,1:?bool}>
     */
    public static function conversionProvider(): array
    {
        return [
            'yes' => [NullableBooleanSchema::VALUE_TRUE, true],
            'no' => [NullableBooleanSchema::VALUE_FALSE, false],
            'undefined' => [NullableBooleanSchema::VALUE_NULL, null],
            'inherit' => [InheritableBooleanSchema::VALUE_INHERIT, null],
            'anything else' => ['nonsense', null],
            'empty' => ['', null],
        ];
    }

    #[Test]
    #[DataProvider('conversionProvider')]
    public function convertReportsTheBooleanOrNullWhenUnspecified(string $value, ?bool $expected): void
    {
        $this->assertSame($expected, NullableBooleanSchema::convert($value));
        $this->assertSame($expected, InheritableBooleanSchema::convert($value));
    }

    #[Test]
    public function theUnspecifiedStateIsOfferedFirst(): void
    {
        $this->assertSame(
            [NullableBooleanSchema::VALUE_NULL, NullableBooleanSchema::VALUE_TRUE, NullableBooleanSchema::VALUE_FALSE],
            array_keys((new NullableBooleanSchema())->getAllowedValues()->toArray()['list'] ?? [])
        );
    }

    #[Test]
    public function valuesAreLabelledFromTheirKeywords(): void
    {
        $values = (new NullableBooleanSchema())->getAllowedValues()->toArray()['list'] ?? [];

        $this->assertSame('Undefined', $values[NullableBooleanSchema::VALUE_NULL]);
        $this->assertSame('Yes', $values[NullableBooleanSchema::VALUE_TRUE]);
        $this->assertSame('No', $values[NullableBooleanSchema::VALUE_FALSE]);
    }

    #[Test]
    public function itRendersAsASelect(): void
    {
        $render = (new NullableBooleanSchema())->getRenderingDefinition()->toArray();

        $this->assertSame(RenderingDefinitionInterface::FORMAT_SELECT, $render['format'] ?? null);
    }

    #[Test]
    public function inheritableNamesTheUnspecifiedStateForItself(): void
    {
        $values = (new InheritableBooleanSchema())->getAllowedValues()->toArray()['list'] ?? [];

        $this->assertSame(
            [InheritableBooleanSchema::VALUE_INHERIT, InheritableBooleanSchema::VALUE_TRUE, InheritableBooleanSchema::VALUE_FALSE],
            array_keys($values)
        );
        $this->assertSame('Inherit', $values[InheritableBooleanSchema::VALUE_INHERIT]);
    }

    /**
     * Regression: PHP resolves self:: in a default parameter value against the class that
     * declares the constructor. Without its own constructor this schema would default to the
     * parent's keyword, which is not among its allowed values, and the select would render
     * "INVALID VALUE".
     */
    #[Test]
    public function inheritableDefaultsToItsOwnUnspecifiedKeyword(): void
    {
        $this->assertSame(InheritableBooleanSchema::VALUE_INHERIT, (new InheritableBooleanSchema())->getDefaultValue());
        $this->assertSame(NullableBooleanSchema::VALUE_NULL, (new NullableBooleanSchema())->getDefaultValue());
    }

    #[Test]
    public function anExplicitDefaultIsKept(): void
    {
        $this->assertSame(
            InheritableBooleanSchema::VALUE_TRUE,
            (new InheritableBooleanSchema(InheritableBooleanSchema::VALUE_TRUE))->getDefaultValue()
        );
    }
}
