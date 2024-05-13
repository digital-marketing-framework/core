<?php

namespace DigitalMarketingFramework\Core\Api\Route;

abstract class Route implements RouteInterface
{
    /**
     * @param array<string,string> $constants
     * @param array<string> $methods
     * @param array<string,array<string,mixed>> $formats
     */
    public function __construct(
        protected string $id,
        protected array $constants = [],
        protected array $methods = ['GET'],
        protected array $formats = ['application/json' => []],
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getConstants(): array
    {
        return $this->constants;
    }

    public function getFormats(): array
    {
        return $this->formats;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function toArray(): array
    {
        $result = [];

        if ($this->methods !== []) {
            $result['hints']['allow'] = $this->methods;
        }

        if ($this->formats !== []) {
            $result['hints']['formats'] = array_map(static function ($formatConfig) {
                return $formatConfig === [] ? (object)[] : $formatConfig;
            }, $this->formats);
        }

        return $result;
    }
}
