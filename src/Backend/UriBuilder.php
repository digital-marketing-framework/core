<?php

namespace DigitalMarketingFramework\Core\Backend;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class UriBuilder implements UriBuilderInterface
{
    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    public function build(string $route, array $arguments = []): string
    {
        foreach ($this->registry->getAllBackendUriRouteResolvers() as $resolver) {
            $url = $resolver->resolve($route, $arguments);
            if ($url !== null) {
                return $url;
            }
        }

        throw new DigitalMarketingFrameworkException(sprintf('No URI route resolver found for route "%s".', $route));
    }
}
