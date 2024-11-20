<?php

declare(strict_types=1);

namespace lindesbs\pageyaml\Tests\Controller;

use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\CoreBundle\Framework\ContaoFramework;
use lindesbs\pageyaml\Classes\DCAManage;
use lindesbs\pageyaml\Controller\BackendImportController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class BackendImportControllerTest extends TestCase
{
    private $backendImportController;
    private $contaoFramework;
    private $csrfTokenManager;
    private $requestStack;
    private $dcaManage;

    protected function setUp(): void
    {
        $this->contaoFramework = $this->createMock(ContaoFramework::class);
        $this->csrfTokenManager = $this->createMock(ContaoCsrfTokenManager::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->dcaManage = $this->createMock(DCAManage::class);

        $this->backendImportController = new BackendImportController(
            $this->contaoFramework,
            $this->csrfTokenManager,
            $this->requestStack,
            $this->dcaManage
        );
    }

    public function testWalkWithRootPage()
    {
        $pageKey = "Root Page";
        $pageData = [];
        $request = new Request();

        $this->dcaManage->expects($this->once())
            ->method('addPage')
            ->with($pageKey, $this->anything(), 0)
            ->willReturn($this->createMock(\Contao\PageModel::class));

        $this->backendImportController->walk($pageKey, $pageData, $request, 0);
    }

    public function testWalkWithChildPage()
    {
        $pageKey = "Child Page";
        $pageData = [];
        $parentId = 1;
        $request = new Request();

        $this->dcaManage->expects($this->once())
            ->method('addPage')
            ->with($pageKey, $this->anything(), $parentId)
            ->willReturn($this->createMock(\Contao\PageModel::class));

        $this->backendImportController->walk($pageKey, $pageData, $request, $parentId);
    }

    public function testWalkWithAlias()
    {
        $pageKey = "Page Title~~page-alias";
        $pageData = [];
        $request = new Request();

        $this->dcaManage->expects($this->once())
            ->method('addPage')
            ->with('Page Title', $this->anything(), 0)
            ->willReturn($this->createMock(\Contao\PageModel::class));

        $this->backendImportController->walk($pageKey, $pageData, $request, 0);
    }

    public function testWalkWithHiddenPage()
    {
        $pageKey = "_Hidden Page";
        $pageData = [];
        $request = new Request();

        $pageModel = $this->createMock(\Contao\PageModel::class);
        $pageModel->expects($this->once())->method('__set')->with('hide', true);

        $this->dcaManage->expects($this->once())
            ->method('addPage')
            ->with('Hidden Page', $this->anything(), 0)
            ->willReturn($pageModel);

        $this->backendImportController->walk($pageKey, $pageData, $request, 0);
    }

    public function testWalkWithAdditionalJobs()
    {
        $pageKey = "Page With Jobs";
        $pageData = [];
        $request = new Request(['additionaljobs' => 'on']);

        $pageModel = $this->createMock(\Contao\PageModel::class);

        $this->dcaManage->expects($this->once())
            ->method('addPage')
            ->with('Page With Jobs', $this->anything(), 0)
            ->willReturn($pageModel);

        $this->dcaManage->expects($this->once())
            ->method('addArticle')
            ->with($pageModel);

        $this->backendImportController->walk($pageKey, $pageData, $request, 0);
    }

    public function testWalkWithErrorPage()
    {
        $pageKey = 404;
        $pageData = [];
        $request = new Request();

        $pageModel = $this->createMock(\Contao\PageModel::class);
        $pageModel->expects($this->once())->method('__set')->with('type', 'error_404');

        $this->dcaManage->expects($this->once())
            ->method('addPage')
            ->with((string)$pageKey, $this->anything(), 0)
            ->willReturn($pageModel);

        $this->backendImportController->walk($pageKey, $pageData, $request, 0);
    }
}
