<?php

namespace DigitalMarketingFramework\Core\Environment;

class EnvironmentService implements EnvironmentServiceInterface
{
    public function environmentVariableExists(string $name): bool
    {
        $value = getenv($name);

        return $value !== false;
    }

    public function getEnvironmentVariable(string $name): string
    {
        $value = getenv($name);

        if ($value === false) {
            return '';
        }

        return $value;
    }

    public function insertEnvironmentVariables(mixed $value): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $subValue) {
                $value[$key] = $this->insertEnvironmentVariables($subValue);
            }
        } elseif (is_string($value)) {
            $value = preg_replace_callback('/@\\{([^\\}]+)\\}/', fn (array $matches): string => $this->getEnvironmentVariable($matches[1]), $value);
        }

        return $value;
    }
}
