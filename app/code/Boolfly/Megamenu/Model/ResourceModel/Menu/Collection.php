<?php
/************************************************************
 * *
 *  * Copyright ©  Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Megamenu
 */
namespace Boolfly\Megamenu\Model\ResourceModel\Menu;

use Boolfly\Megamenu\Model\ResourceModel\Menu as MenuResourceModel;
use Boolfly\Megamenu\Model\Menu;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Boolfly\Megamenu\Model\Source\Status;

/**
 * Class Collection
 *
 * @package Boolfly\Megamenu\Model\ResourceModel\Menu
 */
class Collection extends AbstractCollection
{
    /**
     * Primary column
     *
     * @var string
     */
    protected $_idFieldName = 'menu_id';

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Menu::class, MenuResourceModel::class);
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
