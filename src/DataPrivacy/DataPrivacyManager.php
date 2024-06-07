<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DataPrivacyManager extends DataPrivacyPlugin implements DataPrivacyManagerInterface
{
    protected string $keyword = 'main';

    /** @var array<string,DataPrivacyPluginInterface> */
    protected array $plugins = [];

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function addPlugin(DataPrivacyPluginInterface $plugin, string $keyword = ''): void
    {
        if ($keyword === '') {
            $keyword = GeneralUtility::getPluginKeyword($plugin::class, DataPrivacyPluginInterface::class);
        }

        $plugin->setKeyword($keyword);

        $this->plugins[$keyword] = $plugin;
    }

    public function addContext(WriteableContextInterface $context): void
    {
        foreach ($this->plugins as $plugin) {
            $plugin->addContext($context);
        }
    }

    public function getAllPossiblePermissions(): array
    {
        $permissions = [];
        foreach ($this->plugins as $plugin) {
            foreach ($plugin->getAllPossiblePermissions() as $permission) {
                $permissions[] = $plugin->getKeyword() . ':' . $permission;
            }
        }

        return $permissions;
    }

    public function getGrantedPermissions(): array
    {
        $permissions = [];
        foreach ($this->plugins as $plugin) {
            foreach ($plugin->getGrantedPermissions() as $permission) {
                $permissions[] = $plugin->getKeyword() . ':' . $permission;
            }
        }

        return $permissions;
    }

    public function getAllDeniedPermissions(): array
    {
        return array_values(array_diff($this->getAllPossiblePermissions(), $this->getGrantedPermissions()));
    }

    public function getPermissionLabels(): array
    {
        $labels = [];
        foreach ($this->plugins as $keyword => $plugin) {
            foreach ($plugin->getPermissionLabels() as $id => $label) {
                $labels[$keyword . ':' . $id] = $label . ' (' . $plugin->getLabel() . ')';
            }
        }

        return $labels;
    }

    public function getPermissionDefaults(): array
    {
        $permissions = [];
        foreach ($this->plugins as $keyword => $plugin) {
            foreach ($plugin->getPermissionDefaults() as $id => $default) {
                $permissions[$keyword . ':' . $id] = $default;
            }
        }

        return $permissions;
    }

    public function getFrontendSettings(): array
    {
        $settings = [];
        foreach ($this->plugins as $keyword => $plugin) {
            $pluginSettings = $plugin->getFrontendSettings();
            if ($pluginSettings !== []) {
                $settings[$keyword] = $pluginSettings;
            }
        }

        return $settings;
    }
}
