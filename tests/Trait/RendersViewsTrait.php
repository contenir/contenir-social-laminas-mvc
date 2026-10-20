<?php

declare(strict_types=1);

namespace Contenir\Social\Laminas\Mvc\Tests\Trait;

use Contenir\Social\Laminas\Mvc\Module;
use Laminas\Http\Request;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Resolver\TemplatePathStack;

/**
 * An application's services with the module's view helpers, and a renderer
 * for the test templates, built fresh for each test.
 */
trait RendersViewsTrait
{
    private ServiceManager $services;

    private PhpRenderer $renderer;

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $services More services, by name
     */
    private function setUpViews(array $config, array $services = []): void
    {
        $this->services = new ServiceManager();
        $this->services->setService('config', $config);

        $request = new Request();
        $request->setUri('https://www.example.com/winners?page=2');
        $this->services->setService('Request', $request);

        foreach ($services as $name => $service) {
            $this->services->setService($name, $service);
        }

        /** @var array{view_helpers: array<string, mixed>} $module */
        $module  = (new Module())->getConfig();
        $helpers = new HelperPluginManager($this->services, $module['view_helpers']);
        $this->services->setService('ViewHelperManager', $helpers);

        $this->renderer = new PhpRenderer();
        $this->renderer->setHelperPluginManager($helpers);
        $this->renderer->setResolver(new TemplatePathStack(['script_paths' => [__DIR__ . '/../TestAsset/view']]));
        $this->renderer->doctype('HTML5');
    }
}
