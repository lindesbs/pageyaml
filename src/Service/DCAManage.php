<?php declare(strict_types=1);

namespace lindesbs\pageyaml\Service;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\PageModel;
use Contao\StringUtil;

class DCAManage
{
    public function addPage(
        string $title,
        string $alias = null,
        int $pid = 0,
        bool $published = true,
        string $type = 'regular',
        bool $hide = false

    ): PageModel
    {
        if (!$alias)
        {
            $alias= StringUtil::generateAlias($title);
        }

        $objPage = PageModel::findOneByAlias($alias);

        if (!$objPage) {
            $objPage = new PageModel();
            $objPage->tstamp = time();
            $objPage->alias = $alias;
        }

        $objPage->title = $title;
        $objPage->pid = $pid;

        $objPage->published=$published;
        $objPage->type = $type;

        $objPage->hide = $hide;

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