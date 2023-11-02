<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

use DigitalMarketingFramework\Core\Context\ContextAwareInterface;
use DigitalMarketingFramework\Core\Context\ContextAwareTrait;

abstract class DataPrivacyManager implements DataPrivacyManagerInterface, ContextAwareInterface
{
    use ContextAwareTrait;
}
