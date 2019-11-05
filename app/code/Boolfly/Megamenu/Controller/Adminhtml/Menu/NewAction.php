<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Banner Slider
 */
namespace Boolfly\Megamenu\Controller\Adminhtml\Menu;

use Boolfly\Megamenu\Controller\Adminhtml\AbstractMenu;

/**
 * Class NewAction
 *
 * @package Boolfly\Megamenu\Controller\Adminhtml\Menu
 */
class NewAction extends AbstractMenu
{

    /**
     * New Banner action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
