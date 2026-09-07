<?php

namespace DigitalMarketingFramework\Core\Utility;

use DateTime;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValue;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValue;
use DigitalMarketingFramework\Core\Model\Data\Value\IntegerValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use InvalidArgumentException;
use Stringable;

final class GeneralUtility
{
    /**
     * Splits an identifier into words: an acronym before a capitalised word, an optionally
     * capitalised lowercase run, a remaining uppercase run, or a digit run. Anything else
     * (separators, punctuation) is a boundary and is dropped.
     */
    private const LABEL_WORD_PATTERN = '/\p{Lu}+(?=\p{Lu}\p{Ll})|\p{Lu}?\p{Ll}+|\p{Lu}+|\d+/u';

    protected const CHARACTER_MAP = [
        '\\n' => PHP_EOL,
        '\\s' => ' ',
        '\\t' => "\t",
    ];

    /**
     * Converts a raw PHP value into an Anyrel field value.
     *
     * This is the generic entry point for external data (e.g. CMS form submissions)
     * that has not yet been interpreted as a specific field type.
     * Returns null for values that cannot be converted, signalling that the field should be skipped.
     */
    public static function convertToFieldValue(mixed $value): string|ValueInterface|null
    {
        // null gets filtered out
        if ($value === null) {
            return null;
        }

        // stings are the default format
        if (is_string($value)) {
            return $value;
        }

        // integers are recognized, other numbers are not
        if (is_int($value)) {
            return new IntegerValue($value);
        }

        // booleans are recognized
        if (is_bool($value)) {
            return new BooleanValue($value);
        }

        // arrays (and array-like objects) can be processed recursively
        if (is_iterable($value)) {
            $multiValue = new MultiValue();
            foreach ($value as $childKey => $childValue) {
                $formattedChildValue = self::convertToFieldValue($childValue);
                if ($formattedChildValue !== null) {
                    $multiValue[$childKey] = $formattedChildValue;
                }
            }

            return $multiValue;
        }

        // stingable objects become strings
        if ($value instanceof Stringable) {
            return (string)$value;
        }

        // non-integer numbers become strings
        if (is_numeric($value)) {
            return (string)$value;
        }

        // unrecognized values resolve to null
        return null;
    }

    public static function isEmpty(mixed $value): bool
    {
        if (is_array($value)) {
            return $value === [];
        }

        if ($value instanceof MultiValueInterface) {
            return $value->toArray() === [];
        }

        return (string)$value === '';
    }

    public static function isTrue(mixed $value): bool
    {
        if ($value instanceof ValueInterface) {
            return self::isTrue($value->getValue());
        }

        return (bool)$value;
    }

    public static function isFalse(mixed $value): bool
    {
        if ($value instanceof ValueInterface) {
            return self::isFalse($value->getValue());
        }

        return !(bool)$value;
    }

    public static function parseSeparatorString(string $str): string
    {
        $str = trim($str);
        foreach (self::CHARACTER_MAP as $key => $value) {
            $str = str_replace($key, $value, $str);
        }

        return $str;
    }

    public static function isList(mixed $value): bool
    {
        return is_array($value) || $value instanceof MultiValueInterface;
    }

    public static function castValueToDateTimeValue(string|DateTime|ValueInterface|null $value, ?string $format = null, ?string $timezone = null): ?DateTimeValue
    {
        if ($value === null) {
            return null;
        }

        if ($format === '') {
            $format = null;
        }

        if ($timezone === '') {
            $timezone = null;
        }

        try {
            if ($value instanceof DateTimeValue) {
                return new DateTimeValue(
                    (string)$value->getDate()->getTimestamp(),
                    $format ?? $value->getFormat(),
                    $timezone ?? $value->getTimezone(),
                );
            }

            if ($value instanceof DateTime) {
                return new DateTimeValue(
                    (string)$value->getTimestamp(),
                    $format ?? DateTimeValue::DEFAULT_FORMAT,
                    $timezone ?? $value->getTimezone()->getName(),
                );
            }

            return new DateTimeValue(
                (string)$value,
                $format ?? DateTimeValue::DEFAULT_FORMAT,
                $timezone ?? DateTimeValue::DEFAULT_TIMEZONE,
            );
        } catch (DigitalMarketingFrameworkException) {
            return null;
        }
    }

    /**
     * @param non-empty-string $token
     *
     * @return array<mixed>
     */
    public static function castValueToArray(mixed $value, string $token = ',', bool $trim = true): array
    {
        if (is_array($value)) {
            $array = $value;
        } elseif ($value instanceof MultiValueInterface) {
            $array = $value->toArray();
        } else {
            $value = (string)$value;
            $array = self::isEmpty($value) ? [] : explode($token, $value);
        }

        if ($trim) {
            return array_map(fn ($v) => is_string($v) ? trim($v) : $v, $array);
        }

        return $array;
    }

    /**
     * @param array<mixed> $array
     */
    protected static function castArrayToMultiValueStructure(array $array, MultiValueInterface $multiValue): void
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $multiValue[$key] = self::castArrayToMultiValue($value);
            } elseif (is_int($value)) {
                $multiValue[$key] = new IntegerValue($value);
            } elseif (is_bool($value)) {
                $multiValue[$key] = new BooleanValue($value);
            } else {
                $multiValue[$key] = $value;
            }
        }
    }

    /**
     * @param array<mixed> $array
     */
    public static function castArrayToMultiValue(array $array): MultiValueInterface
    {
        $multiValue = new MultiValue();
        self::castArrayToMultiValueStructure($array, $multiValue);

        return $multiValue;
    }

    /**
     * @param array<mixed> $array
     */
    public static function castArrayToData(array $array): DataInterface
    {
        $data = new Data();
        self::castArrayToMultiValueStructure($array, $data);

        return $data;
    }

    /**
     * @return array<mixed>
     */
    protected static function castMultiValueStructureToArray(MultiValueInterface $multiValue): array
    {
        $array = [];
        foreach ($multiValue as $key => $value) {
            if ($value instanceof MultiValueInterface) {
                $array[$key] = self::castMultiValueStructureToArray($value);
            } elseif ($value instanceof ValueInterface) {
                $array[$key] = $value->getValue();
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * @return array<mixed>
     */
    public static function castDataToArray(DataInterface $data): array
    {
        return self::castMultiValueStructureToArray($data);
    }

    /**
     * @return array<mixed>
     */
    public static function castMultiValueToArray(MultiValueInterface $multiValue): array
    {
        return self::castMultiValueStructureToArray($multiValue);
    }

    public static function shortenHash(string $hash): string
    {
        if ($hash === 'undefined') {
            return 'undefined';
        }

        return substr($hash, 0, 5);
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function calculateHash(array $data, bool $short = false): string
    {
        if ($data === []) {
            return 'undefined';
        }

        $serialized = serialize($data);
        if ($serialized === '') {
            return 'undefined';
        }

        $hash = strtoupper(md5($serialized));

        return $short ? self::shortenHash($hash) : $hash;
    }

    public static function compareValue(mixed $fieldValue, mixed $compareValue): bool
    {
        return (string)$fieldValue === (string)$compareValue;
    }

    public static function compareLists(mixed $fieldValue, mixed $compareList, bool $strict = false): bool
    {
        $fieldValue = self::castValueToArray($fieldValue);
        $compareList = self::castValueToArray($compareList);

        if (!$strict) {
            sort($fieldValue);
            sort($compareList);
        }

        return $fieldValue === $compareList;
    }

    public static function compare(mixed $fieldValue, mixed $compareValue): bool
    {
        if (self::isList($fieldValue) || self::isList($compareValue)) {
            return self::compareLists($fieldValue, $compareValue);
        }

        return self::compareValue($fieldValue, $compareValue);
    }

    /**
     * @param array<mixed> $list
     */
    public static function findInList(mixed $fieldValue, array $list): string|int|false
    {
        return array_search($fieldValue, $list, true);
    }

    /**
     * @param array<mixed> $list
     */
    public static function isInList(mixed $fieldValue, array $list): bool
    {
        return in_array($fieldValue, $list, true);
    }

    public static function getPluginKeyword(string $class, string $interface): string
    {
        $keyword = '';
        $interfaceNamespaceParts = explode('\\', $interface);
        $interfaceName = array_pop($interfaceNamespaceParts);

        $classNamespaceParts = explode('\\', $class);
        $className = array_pop($classNamespaceParts);

        if (str_ends_with($className . 'Interface', $interfaceName)) {
            return lcfirst(substr($className . 'Interface', 0, -strlen($interfaceName)));
        }

        return $keyword;
    }

    public static function slugify(string $string): string
    {
        $string = preg_replace_callback('/([A-Z]+)/', static fn ($matches): string => '-' . strtolower($matches[0]), $string);
        $string = preg_replace('/[^a-z0-9]+/', '-', (string)$string);

        return trim((string)$string, '-');
    }

    public static function camelCaseToDashed(string $string): string
    {
        return self::slugify($string);
    }

    public static function dashedToCamelCase(string $string): string
    {
        return preg_replace_callback('/(-[a-z0-9])/', static fn ($matches): string => strtoupper(substr($matches[0], 1)), $string);
    }

    public static function underscoredToCamelCase(string $string): string
    {
        return preg_replace_callback('/(_[a-z0-9])/', static fn ($matches): string => strtoupper(substr($matches[0], 1)), $string);
    }

    public static function maskValue(string $value): string
    {
        if (preg_match('/@/', $value)) {
            $parts = explode('@', $value);

            $firstPart = array_shift($parts);
            $lengthFirstPart = strlen($firstPart);
            $maskedFirstPart = (int)ceil($lengthFirstPart / 2);

            $secondPart = implode('@', $parts);
            $lengthSecondPart = strlen($secondPart);
            $maskedSecondPart = (int)ceil($lengthSecondPart / 2);

            return substr($firstPart, 0, $lengthFirstPart - $maskedFirstPart)
            //    . str_repeat('*', $maskedFirstPart)
            //    . '*'
            //    . str_repeat('*', $maskedSecondPart)
                . '****'
                . substr($secondPart, $maskedSecondPart);
        }

        $length = strlen($value);
        $masked = (int)ceil($length / 2);
        $start = (int)floor($masked / 2);

        return substr($value, 0, $start) . str_repeat('*', 4) . substr($value, $start + $masked);
    }

    public static function copyMultiValue(MultiValueInterface $multiValue, bool $copyValues = true, bool $recursive = true): MultiValueInterface
    {
        $class = $multiValue::class;
        $copy = new $class([]);
        if ($copyValues) {
            foreach ($multiValue as $key => $value) {
                if ($recursive && $value instanceof MultiValueInterface) {
                    $value = self::copyMultiValue($value, $copyValues, $recursive);
                }

                $copy[$key] = $value;
            }
        }

        return $copy;
    }

    /**
     * @return array{type:string,value:mixed}
     */
    public static function packValue(mixed $value): array
    {
        if (is_object($value)) {
            if ($value instanceof ValueInterface) {
                $type = $value::class;
                $packedValue = $value->pack();
            } else {
                throw new InvalidArgumentException('Invalid field class "' . $value::class . '"');
            }
        } elseif (is_array($value)) {
            throw new InvalidArgumentException('Fields cannot be arrays. Only string representations or ValueInterface objects are allowed.');
        } else {
            $type = 'string';
            $packedValue = (string)$value;
        }

        return [
            'type' => $type,
            'value' => $packedValue,
        ];
    }

    /**
     * @param array{type:string,value:mixed} $packedValue
     */
    public static function unpackValue(array $packedValue): string|ValueInterface
    {
        if ($packedValue['type'] === 'string') {
            return (string)$packedValue['value'];
        }

        $class = $packedValue['type'];
        $value = $packedValue['value'];
        if (!class_exists($class)) {
            throw new DigitalMarketingFrameworkException('Unknown class "' . $class . '"');
        }

        if (!in_array(ValueInterface::class, class_implements($class), true)) {
            throw new DigitalMarketingFrameworkException('Invalid value class "' . $class . '"');
        }

        return $class::unpack($value);
    }

    /**
     * Derives a human-readable label from a machine-readable identifier.
     *
     * Word boundaries follow the usual identifier conventions: separators, a lower-to-upper
     * transition, the end of an acronym before a capitalised word, and letter-to-digit
     * transitions. A word is capitalised only when it is entirely lowercase, so acronyms
     * survive intact.
     *
     *     fooBar          => Foo Bar
     *     fooBAR          => Foo BAR
     *     first_name      => First Name
     *     XMLHttpRequest  => XML Http Request
     *     user2Id         => User 2 Id
     *     Straße          => Straße
     *
     * An opaque identifier has no word structure to find and comes out no more readable
     * than it went in (00N58000006Sucg => 00 N 58000006 Sucg). That is a property of the
     * input, not a defect here; a configured label is the answer for those.
     *
     * Must stay behaviourally identical to prettifyLabel() in the config editor app. Both
     * are covered by tests/Fixtures/pretty-label.json — change the fixture first.
     */
    public static function getLabelFromValue(string $value): string
    {
        preg_match_all(self::LABEL_WORD_PATTERN, $value, $matches);

        return implode(' ', array_map(
            static function (string $word): string {
                if ($word !== mb_strtolower($word)) {
                    return $word;
                }

                return mb_strtoupper(mb_substr($word, 0, 1)) . mb_substr($word, 1);
            },
            $matches[0]
        ));
    }
}
