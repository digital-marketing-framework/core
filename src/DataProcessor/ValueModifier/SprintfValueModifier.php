<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class SprintfValueModifier extends ValueModifier
{
    public const KEY_FORMAT = 'format';
    public const DEFAULT_FORMAT= '%s';

    public function modify(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if (!$this->proceed()) {
            return $value;
        }

        if ($value === null) {
            return null;
        }

        $format = $this->getConfig(static::KEY_FORMAT);
        if ($value instanceof MultiValueInterface) {
            $values = $value->toArray();
        } else {
            $values = [$value];
        }
        return sprintf($format, ...$values);
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_FORMAT => static::DEFAULT_FORMAT,
        ];
    }
}
