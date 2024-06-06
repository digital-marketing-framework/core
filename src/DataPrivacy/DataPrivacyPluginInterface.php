<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

use DigitalMarketingFramework\Core\Context\WriteableContextInterface;

interface DataPrivacyPluginInterface
{
    public function getKeyword(): string;

    public function setKeyword(string $keyword): void;

    public function addContext(WriteableContextInterface $context): void;

    /**
     * @return array<string,mixed>
     */
    public function getFrontendSettings(): array;

    // -- permission list methods --

    /**
     * @return array<string>
     */
    public function getAllPossiblePermissions(): array;

    public function permissionMatches(string $permission): bool;

    /**
     * @return array<string>
     */
    public function getGrantedPermissions(): array;

    /**
     * @return array<string,string>
     */
    public function getPermissionLabels(): array;

    /**
     * @return array<string,bool>
     */
    public function getPermissionDefaults(): array;

    // -- single permission methods --

    public function getPermission(string $permission): bool;

    public function getPermissionDefault(string $permission): bool;

    public function getPermissionLabel(string $permission): string;
}
