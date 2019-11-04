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
        /** @var \Boolfly\Megamenu\Model\Menu $model */
        $model = $this->menuFactory->create();
        $this->coreRegistry->register('current_megamenu', $model);
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This menu no longer exists.'));
                $this->_redirect('megamenu/menu/*');
                return;
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }


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
