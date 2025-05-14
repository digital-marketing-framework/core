<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Controller\BackendController;
use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\HtmlResponse;
use DigitalMarketingFramework\Core\Backend\Response\RedirectResponse;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineAwareInterface;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineAwareTrait;

abstract class SectionController extends BackendController implements SectionControllerInterface, TemplateEngineAwareInterface
{
    use TemplateEngineAwareTrait;

    protected array $viewData = [
        'scripts' => [
            'menu' => 'PKG:digital-marketing-framework/core/res/assets/scripts/backend/menu.js',
        ],
        'styles' => [
            'backend' => 'EXT:backend/Resources/Public/Css/backend.css',
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

    protected function render(Request $request): HtmlResponse
    {
        $templateName = $request->getInternalRoute();
        $templatePath = sprintf('section/%s/%s.html.twig', $this->section, $templateName);
        $config = ['templateName' => $templatePath];

        $rendered = $this->templateEngine->render($config, $this->viewData, false);

        return new HtmlResponse($rendered);
    }

    protected function redirect(string $route, array $arguments = []): RedirectResponse
    {
        $uri = $this->registry->getBackendUriBuilder()->build($route, $arguments);

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
        $this->addScript('PKG:digital-marketing-framework/core/res/assets/config-editor/scripts/index.js');
        $this->copyAsset('PKG:digital-marketing-framework/core/res/assets/config-editor/scripts/index.js.map');
        $this->copyAsset('PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-Bold.ttf');
        $this->copyAsset('PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-Medium.ttf');
        $this->copyAsset('PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-Regular.ttf');
        $this->copyAsset('PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-SemiBold.ttf');
        $this->addStyles('PKG:digital-marketing-framework/core/res/assets/config-editor/styles/index.css');
        $this->addStyles('PKG:digital-marketing-framework/core/res/assets/config-editor/styles/type.css');
    }

    public function getResponse(Request $request): Response
    {
        $this->viewData['menu'] = $this->registry->getBackendManager()->getSectionMenu($request);
        $this->viewData['section'] = $this->registry->getBackendManager()->getSection($this->getSection());

        if (!$this->matchRequest($request)) {
            throw new DigitalMarketingFrameworkException(sprintf('Route "%s" is not supported.', $request->getRoute()));
        }

        return $this->callActionMethod($request);
    }
}
