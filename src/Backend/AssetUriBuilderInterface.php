<?php

namespace DigitalMarketingFramework\Core\Backend;

interface AssetUriBuilderInterface
{
    public function build(string $path): string;
}
