<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use ArrayObject;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use InvalidArgumentException;

class MultiValue extends ArrayObject implements MultiValueInterface
{
    public function __construct(
        array $a = [],
        protected string $glue = ',',
    ) {
        parent::__construct($a);
    }

    public function getValue(): array
    {
        return $this->toArray();
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
            $packed[$key] = GeneralUtility::packValue($value);
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
