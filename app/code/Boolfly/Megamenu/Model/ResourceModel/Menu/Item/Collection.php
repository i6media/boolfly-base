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
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Item::class, MenuItemResourceModel::class);
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
