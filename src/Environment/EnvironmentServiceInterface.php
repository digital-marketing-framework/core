<?php

namespace DigitalMarketingFramework\Core\Environment;

interface EnvironmentServiceInterface
{
    public function environmentVariableExists(string $name): bool;

    public function getEnvironmentVariable(string $name): string;

    public function insertEnvironmentVariables(mixed $value): mixed;
}
