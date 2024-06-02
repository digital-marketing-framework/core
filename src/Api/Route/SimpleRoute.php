<?php

namespace DigitalMarketingFramework\Core\Api\Route;

class SimpleRoute extends Route implements SimpleRouteInterface
{
    public function __construct(
        string $id,
        protected string $path,
        array $constants = [],
        array $methods = ['GET'],
        array $formats = ['application/json' => []],
    ) {
        parent::__construct($id, $constants, $methods, $formats);
    }

    public function matchPath(string $path): bool|array
    {
        if (trim($path, '/') === trim($this->getPath(), '/')) {
            return $this->constants;
        }

        return false;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function toArray(): array
    {
        $result = [
            'href' => '/' . $this->path,
        ];

        return $result + parent::toArray();
    }
}
