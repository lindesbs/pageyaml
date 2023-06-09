<?php declare(strict_types=1);

namespace lindesbs\pageyaml\Service;

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
}