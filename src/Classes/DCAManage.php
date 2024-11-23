<?php

declare(strict_types=1);

namespace lindesbs\pageyaml\Classes;


use Contao\PageModel;
use Contao\ArticleModel;
use Contao\StringUtil;
class DCAManage
{

    public function __construct()
    {
    }

    public function addPage(
        string $title,
        string $alias = null,
        int $pid = 0,
        bool $published = true,
        string $type = 'regular',
        bool $hide = false
    ): PageModel {
        if (!$alias) {
            $alias= StringUtil::generateAlias($title);
        }
        $objPage = PageModel::findOneByAlias($alias);

        if (!$objPage) {
            $objPage = new PageModel();
            $objPage->tstamp = time();
            $objPage->alias = $alias;
            if (!$objPage instanceof PageModel) {
                throw new \RuntimeException('PageModel creation failed');
            }
        }

        $objPage->title = $title;
        $objPage->pid = $pid;

        $objPage->published=$published;
        $objPage->type = $type;

        $objPage->hide = $hide;
        $objPage->save();
        return $objPage;
    }


    public function addArticle(PageModel $pageModel): ArticleModel
    {

        $objArticle = ArticleModel::findOneByAlias($pageModel->alias);

        if (!$objArticle) {
            $objArticle = new ArticleModel();
            $objArticle->tstamp = time();
            $objArticle->title = $pageModel->title;
            $objArticle->alias = $pageModel->alias;

            $objArticle->published = true;

        }

        $objArticle->pid = $pageModel->id;
        $objArticle->save();
        return $objArticle;
    }
}