<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Controller\BackendController;
use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\HtmlResponse;
use DigitalMarketingFramework\Core\Backend\Response\RedirectResponse;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\ConfigurationEditor\MetaData;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineAwareInterface;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineAwareTrait;

abstract class SectionController extends BackendController implements SectionControllerInterface, TemplateEngineAwareInterface
{
    use TemplateEngineAwareTrait;

    /** @var array<string,mixed> */
    protected array $viewData = [
        'scripts' => [
            'menu' => 'PKG:digital-marketing-framework/core/res/assets/scripts/backend/menu.js',
        ],
        'styles' => [
            'backend' => 'PKG:digital-marketing-framework/core/res/assets/styles/backend.css',
        ],
    ];

    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        string $section,
        array $routes,
    ) {
        parent::__construct($keyword, $registry, 'page', $section, $routes);
    }

    protected function render(): HtmlResponse
    {
        $templateName = $this->getInternalRoute();
        $templatePath = sprintf('section/%s/%s.html.twig', $this->section, $templateName);
        $config = ['templateName' => $templatePath];

        $rendered = $this->templateEngine->render($config, $this->viewData, false);

        $response = new HtmlResponse($rendered);

        foreach ($this->viewData['scripts'] ?? [] as $name => $path) {
            $response->setScript($name, $path);
        }

        foreach ($this->viewData['styles'] ?? [] as $name => $path) {
            $response->setStyleSheet($name, $path);
        }

        return $response;
    }

    /**
     * @param array<string,mixed> $arguments
     */
    protected function redirect(string $route, array $arguments = []): RedirectResponse
    {
        $uri = $this->uriBuilder->build($route, $arguments);

        return new RedirectResponse($uri);
    }

    protected function addScript(string $script, string $name = ''): void
    {
        $this->viewData['scripts'][$name ?: $script] = $script;
    }

    protected function addStyles(string $styles, string $name = ''): void
    {
        $this->viewData['styles'][$name ?: $styles] = $styles;
    }

    protected function copyAsset(string $path): void
    {
        $this->registry->getAssetService()->makeAssetPublic($path);
    }

    protected function addConfigurationEditorAssets(): void
    {
        foreach (MetaData::SCRIPTS as $name => $path) {
            $this->addScript($path, $name);
        }

        foreach (MetaData::STYLES as $name => $path) {
            $this->addStyles($path, $name);
        }

        foreach (MetaData::ASSETS as $path) {
            $this->copyAsset($path);
        }
    }

    /**
     * @param array<string,mixed> $arguments
     */
    protected function cleanupArguments(array &$arguments): void
    {
        // TODO can we filter out default values in addition to empty values?
        foreach (array_keys($arguments) as $key) {
            if (is_array($arguments[$key])) {
                $this->cleanupArguments($arguments[$key]);
                if ($arguments[$key] === []) {
                    unset($arguments[$key]);
                }
            } elseif ($arguments[$key] === '') {
                unset($arguments[$key]);
            }
        }
    }

    protected function getCurrentAction(?string $default = null): string
    {
        $default ??= $this->getAction();

        return $this->getParameters()['currentAction'] ?? $default;
    }

    protected function getReturnUrl(?string $default = null): ?string
    {
        return $this->getParameters()['returnUrl'] ?? $default;
    }

    /**
     * @param ?array<string,mixed> $arguments
     * @param array<string,mixed> $additionalArguments
     */
    protected function getPermanentUri(?string $action = null, ?array $arguments = null, array $additionalArguments = []): string
    {
        $action ??= $this->getAction(true);
        $arguments = [
            ...($arguments ?? $this->getParameters()),
            ...$additionalArguments,
        ];

        return $this->uriBuilder->build(
            'page.' . $this->getSection() . '.' . $action,
            $arguments
        );
    }

    /**
     * @param array<string,mixed> $arguments
     */
    protected function assignCurrentRouteData(?array $arguments = null, ?string $defaultReturnRoute = null): void
    {
        $action = $this->getAction(true);
        $this->viewData['current'] = $this->getCurrentAction($action);

        $permanentUri = $this->getPermanentUri($action, $arguments);
        $this->viewData['permanentUri'] = $permanentUri;

        $resetUri = $this->getPermanentUri($action, []);
        $this->viewData['resetUri'] = $resetUri;

        $defaultReturnUri = $defaultReturnRoute === null ? null : $this->uriBuilder->build($defaultReturnRoute);
        $returnUrl = $this->getReturnUrl($defaultReturnUri);
        if ($returnUrl !== null) {
            $this->viewData['returnUrl'] = $returnUrl;
        }
    }

    public function getResponse(Request $request): Response
    {
        $this->request = $request;

        $this->viewData['menu'] = $this->registry->getBackendManager()->getSectionMenu($request);
        $this->viewData['section'] = $this->registry->getBackendManager()->getSection($this->getSection());

        if (!$this->matchRequest($request)) {
            throw new DigitalMarketingFrameworkException(sprintf('Route "%s" is not supported.', $request->getRoute()));
        }

        return $this->callActionMethod();
    }
}
