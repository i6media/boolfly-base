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
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 *
 * @package Boolfly\Megamenu\Controller\Adminhtml\Menu
 */
class Save extends AbstractMenu
{

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Boolfly\Megamenu\Model\Menu $model */
            $model = $this->menuFactory->create();
            if (!empty($data['menu_id'])) {
                $model->load($data['menu_id']);
                if ($data['menu_id'] != $model->getId()) {
                    throw new LocalizedException(__('Wrong Menu ID: %1.', $data['menu_id']));
                }
            }
            unset($data['menu_id']);
            $model->addData($data);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('The menu has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the menu.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
