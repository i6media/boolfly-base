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
        $this->addOrder('parent_id','ASC');
        $this->addOrder('position','ASC');
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
        return $this->addFieldToFilter('status', Status::STATUS_ENABLED);
    }
}
