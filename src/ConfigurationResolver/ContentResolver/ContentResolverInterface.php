<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Form\FieldInterface;

interface ContentResolverInterface extends ConfigurationResolverInterface
{
    /**
     * @return string|FieldInterface|null
     */
    public function build();

    /**
     * @param string|FieldInterface|null $result
     * @return bool
     */
    public function finish(&$result): bool;
}
