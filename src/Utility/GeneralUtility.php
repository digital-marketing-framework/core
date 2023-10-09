<?php

namespace DigitalMarketingFramework\Core\Utility;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValue;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValue;
use DigitalMarketingFramework\Core\Model\Data\Value\IntegerValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Model\File\FileInterface;
use InvalidArgumentException;

final class GeneralUtility
{
    protected const CHARACTER_MAP = [
        '\\n' => PHP_EOL,
        '\\s' => ' ',
        '\\t' => "\t",
    ];

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
        if ($value instanceof MultiValueInterface) {
            return (bool)$value->toArray();
        }

        return (bool)$value;
    }

    public static function isFalse(mixed $value): bool
    {
        if ($value instanceof MultiValueInterface) {
            return !$value->toArray();
        }

        return !$value;
    }

    public static function parseSeparatorString(string $str): string
    {
        $str = trim($str);
        foreach (static::CHARACTER_MAP as $key => $value) {
            $str = str_replace($key, $value, $str);
        }

        return $str;
    }

    public static function isList(mixed $value): bool
    {
        return is_array($value) || $value instanceof MultiValueInterface;
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
            $array = static::isEmpty($value) ? [] : explode($token, $value);
        }

        if ($trim) {
            $array = array_map('trim', $array);
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
                $multiValue[$key] = static::castArrayToMultiValue($value);
            } elseif ($value instanceof FileInterface) {
                $multiValue[$key] = new FileValue($value);
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
        static::castArrayToMultiValueStructure($array, $multiValue);

        return $multiValue;
    }

    /**
     * @param array<mixed> $array
     */
    public static function castArrayToData(array $array): DataInterface
    {
        $data = new Data();
        static::castArrayToMultiValueStructure($array, $data);

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
                $array[$key] = static::castMultiValueStructureToArray($value);
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
        return static::castMultiValueStructureToArray($data);
    }

    /**
     * @return array<mixed>
     */
    public static function castMultiValueToArray(MultiValueInterface $multiValue): array
    {
        return static::castMultiValueStructureToArray($multiValue);
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

        return $short ? static::shortenHash($hash) : $hash;
    }

    public static function compareValue(mixed $fieldValue, mixed $compareValue): bool
    {
        return (string)$fieldValue === (string)$compareValue;
    }

    public static function compareLists(mixed $fieldValue, mixed $compareList, bool $strict = false): bool
    {
        $fieldValue = static::castValueToArray($fieldValue);
        $compareList = static::castValueToArray($compareList);

        if (!$strict) {
            sort($fieldValue);
            sort($compareList);
        }

        return $fieldValue === $compareList;
    }

    public static function compare(mixed $fieldValue, mixed $compareValue): bool
    {
        if (static::isList($fieldValue) || static::isList($compareValue)) {
            return static::compareLists($fieldValue, $compareValue);
        }

        return static::compareValue($fieldValue, $compareValue);
    }

    /**
     * @param array<mixed> $list
     */
    public static function findInList(mixed $fieldValue, array $list): string|int|false
    {
        return array_search($fieldValue, $list, false); // TODO should this be a strict search?
    }

    /**
     * @param array<mixed> $list
     */
    public static function isInList(mixed $fieldValue, array $list): bool
    {
        return in_array($fieldValue, $list);
    }

    public static function getPluginKeyword(string $class, string $interface): string
    {
        $keyword = '';
        $interfaceNamespaceParts = explode('\\', $interface);
        $interfaceName = array_pop($interfaceNamespaceParts);

        $classNamespaceParts = explode('\\', $class);
        $className = array_pop($classNamespaceParts);

        if (str_ends_with($className . 'Interface', $interfaceName)) {
            $keyword = lcfirst(substr($className . 'Interface', 0, -strlen($interfaceName)));
        }

        return $keyword;
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
                    $value = static::copyMultiValue($value, $copyValues, $recursive);
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

        if (!in_array(ValueInterface::class, class_implements($class))) {
            throw new DigitalMarketingFrameworkException('Invalid value class "' . $class . '"');
        }

        return $class::unpack($value);
    }

    /**
     * fooBar => Foo Bar
     * foo-bar => Foo Bar
     * foo_bar => Foo Bar
     * fooBAR => Foo BAR
     */
    public static function getLabelFromValue(string $value): string
    {
        $label = $value;
        $label = preg_replace_callback('/[A-Z]+/', static function (array $matches) {
            return ' ' . $matches[0];
        }, $label);
        $label = preg_replace_callback('/[^a-zA-Z0-9]+([a-zA-Z0-9]+)/', static function (array $matches) {
            return ' ' . ucfirst($matches[1]);
        }, $label);
        $label = preg_replace('/[^a-zA-Z0-9]$/', '', $label);

        return ucfirst($label);
    }
}
