<?php
 /************************************************************
  * *
  *  * Copyright © Boolfly. All rights reserved.
  *  * See COPYING.txt for license details.
  *  *
  *  * @author    info@boolfly.com
  * *  @project   Megamenu
  */
namespace Boolfly\Megamenu\Model\ResourceModel;

use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Boolfly\Megamenu\Model\Menu as MenuModel;

/**
 * Class Menu
 *
 * @package Boolfly\Megamenu\Model\ResourceModel
 */
class Menu extends AbstractDb
{

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * Banner constructor.
     *
     * @param Context  $context
     * @param DateTime $dateTime
     * @param null     $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('bf_megamenu', 'menu_id');
    }

    /**
     * @param AbstractModel $object
     * @return mixed
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->getLinkData($object);
        return parent::_afterLoad($object);
    }

    /**
     * Get Link Data
     *
     * @param AbstractModel $object
     */
    private function getLinkData(AbstractModel $object)
    {
        $this->getStoreLink($object);
    }


    /**
     * Get Banner Link
     *
     * @param AbstractModel $object
     */
    private function getStoreLink(AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()
            ->from($this->getMenuStoreTable(), ['store_id'])
            ->where('menu_id = ?', $object->getId());
        $storeIds = $connection->fetchCol($select);
        $object->setData('store_id', $storeIds);
    }


    /**
     * Before save
     *
     * @param AbstractModel $object
     * @return mixed
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $gmtDate = $this->dateTime->gmtDate();
        if ($object->isObjectNew()) {
            $object->setData('created_at', $gmtDate);
        }
        $object->setData('updated_at', $gmtDate);

        return parent::_beforeSave($object);
    }

    /**
     * Get Slider CMS Page Table
     *
     * @return string
     */
    public function getMenuStoreTable()
    {
        return $this->getTable('bf_megamenu_store');
    }

    /**
     * Process data to some link tables
     *
     * @param AbstractModel $object
     * @throws \Exception
     * @return mixed
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->processLinkTable($object);
        $this->saveItemsCollection($object);

        return parent::_afterSave($object);
    }

    /**
     * @param MenuModel $object
     * @throws \Exception
     */
    protected function saveItemsCollection($object)
    {
        $itemsCollection = $object->getItemsCollection();
        $oldItemIds = $itemsCollection->getAllIds();
        $menuTree = $object->getData('menu_tree');
        $newItemIds = array_column($menuTree, 'item_id');
        $itemDeleted = array_diff($oldItemIds, $newItemIds);
        //Delete Item
        foreach ($itemDeleted as $delId) {
            /** @var AbstractModel $menuItem */
            $menuItem = $itemsCollection->getItemById($delId);
            if ($menuItem) {
                $menuItem->isDeleted(true);
            }
        }
        if (is_array($menuTree) && !empty($menuTree)) {
            foreach ($menuTree as $item) {
                /**
                 * Add New Empty Item
                 */
                if (empty($item['item_id'])) {
                    $newItem = $itemsCollection->getNewEmptyItem();
                    $item['menu_id'] = $object->getId();
                    if ($item['parent_id'] === '') {
                        $item['parent_id'] = null;
                    }
                    $newItem->addData($item);
                    $itemsCollection->addItem($newItem);
                } else {
                    /** @var \Boolfly\Megamenu\Model\Menu\Item $menuItem */
                    $menuItem = $itemsCollection->getItemById($item['item_id']);
                    if ($menuItem) {
                        $menuItem->setData($item);
                    }
                }
            }
        }
        $itemsCollection->save();
    }

    /**
     * Process data to link table
     *
     * @param AbstractModel $object
     * @return $this
     */
    private function processLinkTable(AbstractModel $object)
    {
        $this->processMenuStoreTable($object);

        return $this;
    }

    /**
     * Save data to bf_megamenu_store
     *
     * @param AbstractModel $model
     */
    private function processMenuStoreTable(AbstractModel $model)
    {
        $storeIds         = $model->getData('store_id');
        $menuStoreTable = $this->getMenuStoreTable();
        if ($model->getId() && is_array($storeIds) && !empty($storeIds)) {
            $importData = [];
            $select            = $this->getConnection()
                ->select()
                ->from($menuStoreTable, ['store_id'])
                ->where('store_id = ?', $model->getId());

            /**
             * Remove store unselected
             */
            $oldData = $this->getConnection()->fetchCol($select);
            if (!empty($oldData)) {
                $storeRemoved = array_diff($oldData, $storeIds);
                if (!empty($storeRemoved)) {
                    $this->getConnection()->delete(
                        $menuStoreTable,
                        [
                            'store_id IN(?)' => $storeRemoved,
                            'menu_id = ?' => $model->getId()
                        ]
                    );
                }
            }
            foreach ($storeIds as $storeId) {
                $importData[] = [
                    'store_id' => $storeId,
                    'menu_id' => $model->getId()
                ];
            }
            if (!empty($importData)) {
                $this->getConnection()->insertOnDuplicate(
                    $menuStoreTable,
                    $importData
                );
            }
        } else {
            $this->getConnection()->delete(
                $menuStoreTable,
                ['menu_id = ?' => $model->getId()]
            );
        }
    }
}
