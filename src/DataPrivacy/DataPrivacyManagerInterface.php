<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

interface DataPrivacyManagerInterface extends DataPrivacyPluginInterface
{
    /**
     * @return array<string>
     */
    public function getAllDeniedPermissions(): array;

    /**
     * @return array<string,DataPrivacyPluginInterface>
     */
    public function getPlugins(): array;

    public function addPlugin(DataPrivacyPluginInterface $plugin, string $keyword = ''): void;
}
