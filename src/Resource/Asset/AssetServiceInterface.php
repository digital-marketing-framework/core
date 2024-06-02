<?php

namespace DigitalMarketingFramework\Core\Resource\Asset;

interface AssetServiceInterface
{
    /**
     * @param array{tempBasePath?:string,publicTempBasePath?:string,salt?:string} $config
     */
    public function setAssetConfig(array $config): void;

    public function makeAssetPublic(string $identifier): ?string;
}
