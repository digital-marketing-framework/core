<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

interface DataPrivacyManagerInterface extends DataPrivacyPluginInterface
{
    /**
     * @return array<string>
     */
    public function getAllDeniedPermissions(): array;

    public function addPlugin(DataPrivacyPluginInterface $plugin, string $keyword = ''): void;
}
