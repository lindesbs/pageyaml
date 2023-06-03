<?php

namespace lindesbs\pageyaml\Controller;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Input;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Doctrine\DBAL\Connection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class BackendImportController
{

    public function __construct(
        private readonly ContaoFramework     $framework,
        private readonly Connection          $connection,
        private readonly RequestStack        $requestStack,
        private readonly TranslatorInterface $translator)
    {
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
        $this->framework->initialize();
        $request = $this->requestStack->getCurrentRequest();

        if ($this->handlePOSTData($request))
        {
            Controller::redirect('contao?do=page');
        }

        $strFileSelections = $this->getYamlFiles();

        $twig = $container->get('twig');
        $strReturn = '';

        if ($twig) {
            $strReturn = $twig->render('@PageYaml\Backend\settings.html.twig',
                [
                    'request_token' => REQUEST_TOKEN,
                    'optionsArray' => $strFileSelections
                ]);
        }

        return $strReturn;
    }


    protected function walk($pageKey, $pageData, $pid = 0)
    {
        $alias = null;

        if (str_contains($pageKey, "~~")) {
            list($title, $alias) = explode("~~", $pageKey);
        } else {
            $title = $pageKey;
        }

        if (!$alias)
            $alias = StringUtil::generateAlias($title);

        $objPage = PageModel::findByAlias($alias);

        if (!$objPage) {
            $objPage = new PageModel();
            $objPage->tstamp = time();
            $objPage->alias = $alias;
        }

        $objPage->title = $title;
        $objPage->pid = $pid;

        if ($pid == 0) {
            $objPage->type = "root";
        } else {
            $objPage->type = 'regular';
        }
        $objPage->published = true;


        if (is_int($pageKey))
        {
            $objPage->type = 'error_'.$pageKey;
        }


        $nodes = [];


        if (is_array($pageData)) {
            foreach ($pageData as $arrayKey => $arrayValue) {

                if (str_starts_with($arrayKey, '~')) {
                    $key = ltrim($arrayKey, '~');
                    $objPage->$key = $arrayValue;

                    continue;
                }

                if (str_starts_with($arrayKey,'_'))
                {
                    $objPage->visible = true;
                    $objPage->hide = true;
                }

                $nodes[$arrayKey] = $arrayValue;
            }
        }

        $objPage->save();

        foreach ($nodes as $nodeKey => $nodeValue) {
            $this->walk($nodeKey, $nodeValue, $objPage->id);
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
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     * @return void
     */
    public function handlePOSTData(?\Symfony\Component\HttpFoundation\Request $request): bool
    {
        if (($request->getMethod() === 'POST') &&
            ($request->get('FORM_ID') === 'PAGEYAML_UPLOAD')) {

            try {
                $fileData = Yaml::parseFile($request->get('pageyaml_files'));
            } catch (\Exception $ex) {
                return false;
            }

            $this->walk(key($fileData), array_pop(array_values($fileData)));

            return true;

        }

        return false;
    }


}