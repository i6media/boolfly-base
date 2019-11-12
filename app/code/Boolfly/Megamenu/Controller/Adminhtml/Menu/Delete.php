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
 * Class Delete
 *
 * @package Boolfly\Megamenu\Controller\Adminhtml\Menu
 */
class Delete extends AbstractMenu
{
    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            /** @var \Boolfly\Megamenu\Model\Menu $model */
            $model = $this->menuFactory->create();
            try {
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The menu has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleted the menu.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
