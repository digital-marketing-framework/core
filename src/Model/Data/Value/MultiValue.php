<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use ArrayObject;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

/**
 * @extends ArrayObject<int|string,string|ValueInterface>
 */
class MultiValue extends ArrayObject implements MultiValueInterface
{
    public function __construct(
        array $a = [],
        protected string $glue = ',',
    ) {
        parent::__construct($a);
    }

    /**
     * @return array<int|string,string|ValueInterface>
     */
    public function getValue(): array
    {
        $result = [];
        foreach ($this as $key => $value) {
            if ($value instanceof ValueInterface) {
                $result[$key] = $value->getValue();
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
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
            $packed[(string)$key] = GeneralUtility::packValue($value);
        }

        return $packed;
    }

    public static function unpack(array $packed): MultiValueInterface
    {
        $data = [];
        foreach ($packed as $key => $packedValue) {
            $data[$key] = GeneralUtility::unpackValue($packedValue);
        }

        return new static($data);
    }
}
