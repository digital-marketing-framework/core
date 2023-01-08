<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

abstract class AbstractModifierContentResolver extends ContentResolver
{
    protected const WEIGHT = 20;

    protected function modifyValue(string|ValueInterface|null &$result): void
    {
    }

    protected function modify(string|ValueInterface|null &$result): void
    {
        if ($result instanceof MultiValueInterface) {
            foreach ($result as $key => $value) {
                $this->modify($result[$key]);
            }
        } else {
            $this->modifyValue($result);
        }
    }

    public function finish(string|ValueInterface|null &$result): bool
    {
        if ($this->configuration && $result !== null) {
            $this->modify($result);
        }
        return false;
    }
}
