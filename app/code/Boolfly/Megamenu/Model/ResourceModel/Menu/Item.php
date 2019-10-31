<?php
 /************************************************************
  * *
  *  * Copyright © Boolfly. All rights reserved.
  *  * See COPYING.txt for license details.
  *  *
  *  * @author    info@boolfly.com
  * *  @project   Megamenu
  */
namespace Boolfly\Megamenu\Model\ResourceModel\Menu;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Item
 *
 * @package Boolfly\Megamenu\Model\ResourceModel\Menu
 */
class Item extends AbstractDb
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
        $this->_init('bf_megamenu_item', 'item_id');
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
}
