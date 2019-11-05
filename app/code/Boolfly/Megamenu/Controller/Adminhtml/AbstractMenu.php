<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Megamenu
 */
namespace Boolfly\Megamenu\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Boolfly\Megamenu\Api\Data\MenuInterfaceFactory;

/**
 * Class AbstractMenu
 *
 * @package Boolfly\Megamenu\Controller\Adminhtml
 */
abstract class AbstractMenu extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Megamenu::manager';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var MenuInterfaceFactory
     */
    protected $menuFactory;

    /**
     * AbstractMenu constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param MenuInterfaceFactory $menuFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        MenuInterfaceFactory $menuFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry  = $coreRegistry;
        $this->menuFactory = $menuFactory;
    }


    /**
     * Init action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Boolfly_Megamenu::manager'
        )->_addBreadcrumb(
            __('Manage Menu'),
            __('Manage Menu')
        );
        return $this;
    }
}
