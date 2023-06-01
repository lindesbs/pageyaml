<?php

namespace lindesbs\pageyaml\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Input;
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
        private ContaoFramework     $framework,
        private Connection          $connection,
        private RequestStack        $requestStack,
        private TranslatorInterface $translator)
    {
        $this->framework = $framework;
        $this->connection = $connection;
        $this->requestStack = $requestStack;
        $this->translator = $translator;

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

        if (($request->getMethod() === 'POST') &&
            ($request->get('FORM_ID') === 'PAGEYAML_UPLOAD')) {


            $fileData = Yaml::parse(file_get_contents($request->get('pageyaml_files')));


            dd($fileData);
        }

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
}