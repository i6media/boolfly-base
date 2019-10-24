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
class Edit extends AbstractMenu
{

    /**
     * New Menu action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $this->_initAction();
        $this->_addBreadcrumb(
            $id ? __('Edit Menu') : __('New Menu'),
            $id ? __('Edit Menu') : __('New Menu')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $id ? __('Edit Menu') : __('New Menu')
        );
        $this->_view->renderLayout();
    }
}
