<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use ArrayObject;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use InvalidArgumentException;

class MultiValue extends ArrayObject implements MultiValueInterface
{
    public function __construct(
        array $a = [],
        protected string $glue = ',',
    ) {
        parent::__construct($a);
    }

    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function count(): int
    {
        return count($this->toArray());
    }

    public function empty(): bool
    {
        return $this->count() === 0;
    }

    public function setGlue(string $glue): void
    {
        $this->glue = $glue;
    }

    public function getGlue(): string
    {
        return $this->glue;
    }

    public function __toString(): string
    {
        return implode($this->glue, $this->toArray());
    }

    public function pack(): array
    {
        $data = $this->toArray();
        $packed = [];
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                if ($value instanceof ValueInterface) {
                    $type = get_class($value);
                    $packedValue = $value->pack();
                } else {
                    throw new InvalidArgumentException('Invalid field class "' . get_class($value) . '"');
                }
            } else {
                $type = 'string';
                $packedValue = (string)$value;
            }
            $packed[$key] = [
                'type' => $type,
                'value' => $packedValue,
            ];
        }
        return $packed;
    }

    public static function unpack(array $packed): MultiValueInterface
    {
        $data = [];
        foreach ($packed as $key => $packedValue) {
            if ($packedValue['type'] === 'string') {
                $data[$key] = $packedValue['value'];
            } else {
                $class = $packedValue['type'];
                $value = $packedValue['value'];
                if (!class_exists($class)) {
                    throw new DigitalMarketingFrameworkException('Unknown class "' . $class . '"');
                }
                if (!in_array(ValueInterface::class, class_implements($class))) {
                    throw new DigitalMarketingFrameworkException('Invalid value class "' . $class . '"');
                }
                $data[$key] = $class::unpack($value);
            }
        }
        return new static($data);
    }
}
