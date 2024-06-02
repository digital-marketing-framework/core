<?php

namespace DigitalMarketingFramework\Core\Api\Route;

class TemplateRoute extends Route implements TemplateRouteInterface
{
    /**
     * @param array<string,string> $variables
     * @param array<string,string> $constants
     * @param array<string> $methods
     * @param array<string,array<string,mixed>> $formats
     */
    public function __construct(
        string $id,
        protected string $template,
        protected array $variables,
        array $constants = [],
        array $methods = ['GET'],
        array $formats = ['application/json' => []],
    ) {
        parent::__construct($id, $constants, $methods, $formats);
    }

    public function matchPath(string $path): bool|array
    {
        $template = $this->getTemplate();
        foreach ($this->constants as $name => $value) {
            $template = str_replace('{' . $name . '}', $value, $template);
        }

        $variables = $this->constants;
        $pathSegments = explode('/', $path);
        $matchSegments = explode('/', $template);
        while ($pathSegments !== [] && $matchSegments !== []) {
            $pathSegment = array_shift($pathSegments);
            $matchSegment = array_shift($matchSegments);
            if (str_starts_with($matchSegment, '{')) {
                $name = substr($matchSegment, 1, strlen($matchSegment) - 2);
                $variables[$name] = $pathSegment;
            } elseif ($matchSegment !== $pathSegment) {
                return false;
            }
        }

        if ($pathSegments !== [] || $matchSegments !== []) {
            return false;
        }

        return $variables;
    }

    public function getResourceRoute(string $idAffix, array $variables): SimpleRouteInterface
    {
        $path = $this->getTemplate();
        $constants = $this->getConstants();
        foreach ($variables as $name => $value) {
            $constants[$name] = $value;
        }

        foreach ($constants as $name => $value) {
            $path = str_replace('{' . $name . '}', $value, $path);
        }

        return new SimpleRoute(
            id: $this->getId() . ':' . $idAffix,
            path: $path,
            constants: $constants,
            methods: $this->getMethods(),
            formats: $this->getFormats()
        );
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getConstants(): array
    {
        return $this->constants;
    }

    public function toArray(): array
    {
        $template = $this->getTemplate();
        $variables = $this->getVariables();
        foreach ($this->constants as $name => $value) {
            $template = str_replace('{' . $name . '}', $value, $template);
            unset($variables[$name]);
        }

        $result = [
            'hrefTemplate' => '/' . $template,
            'hrefVariables' => $variables,
        ];

        return $result + parent::toArray();
    }
}
