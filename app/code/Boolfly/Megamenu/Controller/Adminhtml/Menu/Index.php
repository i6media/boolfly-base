<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Megamenu
 */
namespace Boolfly\Megamenu\Controller\Adminhtml\Menu;

use Boolfly\Megamenu\Controller\Adminhtml\AbstractMenu;

/**
 * Class Edit
 *
 * @package Boolfly\Megamenu\Controller\Adminhtml\Menu
 */
class Index extends AbstractMenu
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Content'), __('Content'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Megamenu'));
        $this->_view->renderLayout();
    }
}
