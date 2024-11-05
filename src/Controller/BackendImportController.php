<?php

declare(strict_types=1);

namespace lindesbs\pageyaml\Controller;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Input;
use Contao\System;
use Doctrine\DBAL\Connection;
use lindesbs\pageyaml\Service\DCAManage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class BackendImportController
{

    public function __construct(
        private readonly ContaoFramework     $contaoFramework,
        private readonly Connection          $connection,
        private readonly RequestStack        $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly DCAManage $DCAManage
    ) {
        $GLOBALS['TL_CSS'][] = 'bundles/pageyaml/pageyaml.css|static';
    }

    /**
     * @Route("/_contao/pageyaml/setup", name="backendPageYamlSetup")
     */
    public function renderForm(): string
    {
        if (Input::get('key') != 'pageyaml') {
            return '';
        }

        $container = System::getContainer();
        $this->contaoFramework->initialize();
        $request = $this->requestStack->getCurrentRequest();

        if ($this->handlePOSTData($request)) {
            Controller::redirect('contao?do=page');
        }

        $strFileSelections = $this->getYamlFiles();

        $twig = $container->get('twig');
        $strReturn = '';

        if ($twig) {
            $strReturn = $twig->render(
                '@PageYaml\Backend\settings.html.twig',
                [
                    'request_token' => REQUEST_TOKEN,
                    'optionsArray' => $strFileSelections
                ]
            );
        }

        return $strReturn;
    }


    protected function walk($pageKey, $pageData, Request $request, $pid = 0): void
    {
        $alias = null;

        if (str_contains($pageKey, "~~")) {
            list($title, $alias) = explode("~~", $pageKey);
        } else {
            $title = $pageKey;
        }

        $pageModel = $this->DCAManage->addPage(
            $title,
            pid: $pid
        );

        if ($pid == 0) {
            $pageModel->type = "root";
        }

        // Ist die Bezeichnung numerisch, wird dies als Errorpage gewertet
        if (is_int($pageKey)) {
            $pageModel->type  = 'error_'.$pageKey;
        }

        $nodes = [];

        if (is_array($pageData)) {
            foreach ($pageData as $arrayKey => $arrayValue) {

                if (str_starts_with($arrayKey, '~')) {
                    $key = ltrim($arrayKey, '~');
                    $pageModel->$key = $arrayValue;

                    continue;
                }

                $nodes[$arrayKey] = $arrayValue;
            }
        }

        if (str_starts_with($pageKey, '_')) {
            $pageModel->hide = true;
        }

        $pageModel->save();

        $jobs = $request->get('additionaljobs');

        if (str_contains($jobs, 'on')) {
            $objArticle = $this->DCAManage->addArticle($pageModel);
        }

        foreach ($nodes as $nodeKey => $nodeValue) {
            $this->walk($nodeKey, $nodeValue, $request, $pageModel->id);
        }
    }

    /**
     * @return array
     */
    public function getYamlFiles(): array
    {
        $strUploadPath = System::getContainer()->getParameter('contao.upload_path');
        $projectDir = System::getContainer()->getParameter('kernel.project_dir');

        $finder = new Finder();
        $finder->files()
            ->in(sprintf("%s/%s", $projectDir, $strUploadPath))
            ->files()
            ->name("*.yaml")
            ->sortByName(true);

        $strFileSelections = [];

        foreach ($finder as $file) {
            $absoluteFilePath = $file->getRealPath();
            $fileNameWithExtension = $file->getRelativePathname();
            $strFileSelections[$absoluteFilePath] = $fileNameWithExtension;
        }
        return $strFileSelections;
    }

    /**
     * @param  Request|null $request
     * @return bool
     */
    public function handlePOSTData(?Request $request): bool
    {
        if (($request->getMethod() === 'POST') 
            && ($request->get('FORM_ID') === 'PAGEYAML_UPLOAD')
        ) {

            try {
                $fileData = Yaml::parseFile($request->get('pageyaml_files'));
            } catch (\Exception $ex) {
                return false;
            }

            $array = array_values($fileData);
            $this->walk(key($fileData), array_pop($array), $request);

            return true;
        }

        return false;
    }


}
