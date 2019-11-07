<?php
/************************************************************
 * *
 *  * Copyright ©  Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Megamenu
 */
namespace Boolfly\Megamenu\Model\ResourceModel\Menu\Item;

use Boolfly\Megamenu\Model\Menu;
use Boolfly\Megamenu\Model\ResourceModel\Menu\Item as MenuItemResourceModel;
use Boolfly\Megamenu\Model\Menu\Item;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Boolfly\Megamenu\Model\Source\Status;
use Boolfly\Megamenu\Model\Source\LayoutType;

/**
 * Class Collection
 *
 * @package Boolfly\Megamenu\Model\ResourceModel\Menu\Item
 */
class Collection extends AbstractCollection
{
    /**
     * Primary column
     *
     * @var string
     */
    protected $_idFieldName = 'item_id';

    /**
     * @var Menu
     */
    protected $menu;

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Item::class, MenuItemResourceModel::class);
    }


    /**
     * @return mixed
     */
    protected function _afterLoad()
    {
        $this->joinContentTable();
        return parent::_afterLoad();
    }

    /**
     * @return $this
     */
    private function joinContentTable()
    {
        /** @var MenuItemResourceModel $resource */
        $resource = $this->getResource();
        $layoutType = $resource->getLayoutType();

        $select = $this->getSelect();
        foreach ($layoutType as $type => $typeId) {
            $alias = $type . '_content';
            $cols = $this->getContentColumnByAlias($type, $alias);
            $condition = $this->getConditionToJoinContent($typeId, $alias);
            $select->joinLeft([
                $alias => $this->getTable('bf_megamenu_item_content')
            ], $condition, $cols);
        }

        return $this;
    }

    /**
     * @param $type
     * @param $alias
     * @return array
     */
    private function getContentColumnByAlias($type, $alias)
    {
        $cols = [
            $type .'_status' => $alias. '.status',
            $type .'_width' => $alias. '.width',
            $type .'_content' => $alias. '.content',
        ];
        if ($type == 'main_content') {
            $cols['main_content_child_columns'] = $alias. '.child_columns';
            $cols['main_content_content_type'] = $alias. '.content_type';
            $cols['main_content_category_id'] = $alias. '.category_id';
        }

        return $cols;
    }

    /**
     * @param $typeId
     * @param $alias
     * @return string
     */
    private function getConditionToJoinContent($typeId, $alias)
    {
        $connection = $this->getConnection();
        $conditionArray = [
            $connection->quoteIdentifier('main_table.item_id') . ' = '. $connection->quoteIdentifier($alias .'.item_id'),
            $this->getConnection()->quoteInto($alias . '.type_id = ?', $typeId)
        ];
        return join(' AND ', $conditionArray);
    }

    /**
     * Save Item Collection
     *
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        /** @var \Boolfly\Megamenu\Model\Menu\Item[] $items */
        $items = $this->getItems();
        foreach ($items as $item) {
            if ($item->isDeleted()) {
                $item->delete();
            } else if ($item->hasDataChanges()) {
                $item->loadItemContent();
                $item->save();
            }
        }

        return $this;
    }

    /**
     * Add Menu To Filter
     *
     * @param $menu
     * @return $this
     */
    public function setMenu(Menu $menu)
    {
        $this->menu = $menu;
        $this->addFieldToFilter('menu_id', $menu->getId());

        return $this;
    }

    /**
     * @return $this
     */
    public function sortAllItems()
    {
        $this->addOrder('level','ASC');
        $this->addOrder('position','ASC');
        $this->addOrder('parent_id','ASC');
        return $this;
    }

    /**
     * Get menu
     *
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Only get enable banner
     *
     * @return Collection
     */
    public function addActiveStatusFilter()
    {
        return $this->addFieldToFilter('main_table.status', Status::STATUS_ENABLED);
    }
}
