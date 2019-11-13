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
use Magento\Framework\DataObject;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Validate
 *
 * @package Boolfly\Megamenu\Controller\Adminhtml\Menu
 */
class Validate extends AbstractMenu
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($this->validateData());

        return $resultJson;
    }

    /**
     * Validate Data
     *
     * @return DataObject
     */
    private function validateData()
    {
        $error    = false;
        $messages = [];
        $response = new DataObject();
        try {
            $menuTree = $this->getRequest()->getParam('menu_tree', false);
            if (!is_array($menuTree) || empty($menuTree)) {
                throw new LocalizedException(__('You should add least a menu item.'));
            }
            $menuId     = $this->getRequest()->getParam('menu_id', false);
            $identifier = $this->getRequest()->getParam('identifier', false);
            /** @var \Boolfly\Megamenu\Model\Menu $menu */
            $menu = $this->menuFactory->create();
            if ($menuId) {
                $menu->load($menuId);
                if ($menu->checkIdentifier($identifier) && $menuId != $menu->checkIdentifier($identifier)) {
                    throw new LocalizedException(
                        __('The identifier of menu must be unique.')
                    );
                }
            } else {
                if ($menu->checkIdentifier($identifier)) {
                    throw new LocalizedException(
                        __('The identifier of menu must be unique.')
                    );
                }
            }
        } catch (LocalizedException $e) {
            $error      = true;
            $messages[] = $e->getMessage();
        }
        $result = [
            'error' => $error,
            'messages' => $messages
        ];
        $response->setData($result);

        return $response;
    }
}
