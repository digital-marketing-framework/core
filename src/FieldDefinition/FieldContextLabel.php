<?php

namespace DigitalMarketingFramework\Core\FieldDefinition;

use DigitalMarketingFramework\Core\Utility\GeneralUtility;

/**
 * Reads a field context identifier as the three things it is made of: the integration it belongs
 * to, what kind of thing uses it, and which one.
 *
 * The config editor composes the same wording in JavaScript, in
 * `config-editor-app-src/src/composables/fieldContext/fieldContextRepository.js`
 * (`getInputContextNames` / `getOutputContextNames`) — keep the two in step. That side reads the
 * configuration document and can use a configured route's own label; this one has only the
 * identifier, so an unlabelled keyword is prettified with `GeneralUtility::getLabelFromValue()`.
 */
class FieldContextLabel
{
    public const TYPE_CUSTOM = 'Custom';

    /**
     * Identifiers that name one fixed thing rather than a family. They are not registered as
     * field contexts today, but they are part of the identifier space and read badly if they
     * fall through to the custom case.
     */
    public const EXACT = [
        'distributor.in.defaults.current' => ['integration' => null, 'type' => 'Distributor', 'route' => 'Current Form'],
        'collector.out.all' => ['integration' => null, 'type' => 'Collector', 'route' => 'All Fields'],
    ];

    /**
     * Prefixes of "<prefix><integration>.<route>", and what the thing after them is.
     */
    public const PREFIXES = [
        'distributor.out.defaults.' => 'Distributor',
        'collector.in.defaults.' => 'Collector',
    ];

    /**
     * Prefixes of "<prefix><id>", which name no integration.
     */
    public const UNGROUPED_PREFIXES = [
        'dataMapperGroup.out.' => 'Data Mapper',
    ];

    /**
     * @return array{integration:?string,type:string,route:?string}
     */
    public static function parse(string $contextIdentifier): array
    {
        if (isset(static::EXACT[$contextIdentifier])) {
            return static::EXACT[$contextIdentifier];
        }

        foreach (static::PREFIXES as $prefix => $type) {
            if (!str_starts_with($contextIdentifier, $prefix)) {
                continue;
            }

            $rest = substr($contextIdentifier, strlen($prefix));
            $separator = strpos($rest, '.');
            if ($separator === false) {
                return ['integration' => null, 'type' => $type, 'route' => static::prettify($rest)];
            }

            return [
                'integration' => static::prettify(substr($rest, 0, $separator)),
                'type' => $type,
                'route' => static::prettify(substr($rest, $separator + 1)),
            ];
        }

        foreach (static::UNGROUPED_PREFIXES as $prefix => $type) {
            if (str_starts_with($contextIdentifier, $prefix)) {
                return ['integration' => null, 'type' => $type, 'route' => static::prettify(substr($contextIdentifier, strlen($prefix)))];
            }
        }

        // A freely chosen identifier belongs to no integration and names no route, so the whole
        // of it is the name of the thing.
        return ['integration' => null, 'type' => static::TYPE_CUSTOM, 'route' => static::prettify($contextIdentifier)];
    }

    /**
     * The raw integration name an identifier names, for looking up what that integration calls
     * itself. Null when the identifier names none.
     */
    public static function parseIntegrationName(string $contextIdentifier): ?string
    {
        foreach (array_keys(static::PREFIXES) as $prefix) {
            if (!str_starts_with($contextIdentifier, $prefix)) {
                continue;
            }

            $rest = substr($contextIdentifier, strlen($prefix));
            $separator = strpos($rest, '.');

            return $separator === false ? null : substr($rest, 0, $separator);
        }

        return null;
    }

    /**
     * The parts joined into one string, for places with room for only one — the editor title,
     * for instance.
     *
     * The integration is left out: it is almost always the same word as the route ("Salesforce"
     * for distributor.out.defaults.salesforce.salesforce), and where it is not, the route is the
     * telling half. The list has columns and shows all three.
     */
    public static function get(string $contextIdentifier): string
    {
        $parts = static::parse($contextIdentifier);
        $route = $parts['route'] ?? '';

        return $route === '' ? $parts['type'] : $parts['type'] . ': ' . $route;
    }

    protected static function prettify(string $keyword): string
    {
        return $keyword === '' ? '' : GeneralUtility::getLabelFromValue($keyword);
    }
}
