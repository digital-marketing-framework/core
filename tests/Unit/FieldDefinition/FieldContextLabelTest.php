<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\FieldDefinition;

use DigitalMarketingFramework\Core\FieldDefinition\FieldContextLabel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FieldContextLabelTest extends TestCase
{
    /**
     * The expectations are what the config editor shows for the same identifiers, so that the
     * two implementations can be compared by reading this table.
     *
     * @return array<string,array{string,string}>
     */
    public static function labelProvider(): array
    {
        return [
            'outbound route' => ['distributor.out.defaults.salesforce.salesforce', 'Distributor: Salesforce'],
            'outbound route with a compound keyword' => ['distributor.out.defaults.salesforce.salesforceCase', 'Distributor: Salesforce Case'],
            'inbound route' => ['collector.in.defaults.pardot.pardot', 'Collector: Pardot'],
            'data mapper group' => ['dataMapperGroup.out.someGroup', 'Data Mapper: Some Group'],
            'fixed identifier' => ['distributor.in.defaults.current', 'Distributor: Current Form'],
            'other fixed identifier' => ['collector.out.all', 'Collector: All Fields'],
            'freely chosen identifier' => ['shop.in.cart', 'Custom: Shop In Cart'],
            'prefix with nothing after it' => ['distributor.out.defaults.', 'Distributor'],
        ];
    }

    #[Test]
    #[DataProvider('labelProvider')]
    public function identifiersAreLabelled(string $contextIdentifier, string $expected): void
    {
        $this->assertSame($expected, FieldContextLabel::get($contextIdentifier));
    }

    /**
     * @return array<string,array{string,?string,string,?string}>
     */
    public static function partsProvider(): array
    {
        return [
            'outbound route' => ['distributor.out.defaults.salesforce.salesforceCase', 'Salesforce', 'Distributor', 'Salesforce Case'],
            'inbound route' => ['collector.in.defaults.pardot.pardot', 'Pardot', 'Collector', 'Pardot'],
            // Nothing groups these, so they carry no integration and stand alone in the list.
            'data mapper group' => ['dataMapperGroup.out.someGroup', null, 'Data Mapper', 'Some Group'],
            'freely chosen identifier' => ['shop.in.cart', null, 'Custom', 'Shop In Cart'],
        ];
    }

    #[Test]
    #[DataProvider('partsProvider')]
    public function identifiersAreReadAsParts(string $contextIdentifier, ?string $integration, string $type, ?string $route): void
    {
        $this->assertSame(
            ['integration' => $integration, 'type' => $type, 'route' => $route],
            FieldContextLabel::parse($contextIdentifier)
        );
    }
}
