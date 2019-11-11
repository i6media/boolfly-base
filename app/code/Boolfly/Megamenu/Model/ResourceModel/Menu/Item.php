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
use Boolfly\Megamenu\Model\Source\LayoutType;

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
     * @var array
     */
    protected $layoutType;

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
     * @return string
     */
    public function getMenuItemContentTable()
    {
        return $this->getTable('bf_megamenu_item_content');
    }

    /**
     * @param AbstractModel $object
     * @return mixed
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->loadContentData($object);
        return parent::_afterLoad($object);
    }

    /**
     * Get Link Data
     *
     * @param AbstractModel $object
     */
    public function loadContentData(AbstractModel $object)
    {
        $connection  = $this->getConnection();
        $select      = $connection->select()
            ->from($this->getMenuItemContentTable())
            ->where('item_id = ?', $object->getId());
        $contentData = $connection->fetchAll($select);
        if (!empty($contentData)) {
            $data       = [];
            $layoutType = array_flip($this->getLayoutType());
            foreach ($contentData as $row) {
                $typeId = $row['type_id'];
                if (!empty($layoutType[$typeId])) {
                    unset($row['updated_at']);
                    $data[$layoutType[$typeId]] = array_filter($row, [$this, 'filterNullValue']);
                }
            }
            $object->setData('content', $data);
            $object->setOrigData('content', $data);
        }
    }

    /**
     * Remove all empty strings to null values, as
     *
     * @param $value
     * @return boolean
     */
    protected function filterNullValue($value)
    {
        return $value !== null && $value !== '';
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
     * @param AbstractModel $object
     * @return mixed
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveItemContent($object);
        return parent::_afterSave($object);
    }

    /**
     * @return array|null
     */
    public function getLayoutType()
    {
        if ($this->layoutType === null) {
            $this->layoutType = [];
            $layoutType       = LayoutType::getOptionArray();
            $layoutType       = array_map('strtolower', $layoutType);
            $layoutType       = array_flip(array_map([$this, 'replaceSpace'], $layoutType));
            $this->layoutType = $layoutType;
        }

        return $this->layoutType;
    }

    /**
     * Save Item Content
     *
     * @param AbstractModel $object
     */
    private function saveItemContent(AbstractModel $object)
    {
        $content     = $object->getData('content');
        $dataChanges = [];
        if (is_array($content) && !empty($content)) {
            $gmtDate    = $this->dateTime->gmtDate();
            $layoutType = $this->getLayoutType();
            foreach ($content as $key => $value) {
                if (!empty($layoutType[$key]) && is_array($value)) {
                    $value['item_id']    = $object->getId();
                    $value['type_id']    = $layoutType[$key];
                    $value['updated_at'] = $gmtDate;
                    $dataChanges[]       = array_replace_recursive($this->getDefaultData(), $value);
                }
            }
        }

        if (!empty($dataChanges)) {
            $this->getConnection()->insertOnDuplicate($this->getMenuItemContentTable(), $dataChanges);
        }
    }

    /**
     * @return array
     */
    private function getDefaultData()
    {
        return [
            'item_id' => null,
            'type_id' => null,
            'width' => null,
            'content_type' => null,
            'content' => null,
            'child_columns' => null,
            'category_id' => null,
            'created_at' => $this->dateTime->gmtDate(),
            'status' => 1,
            'updated_at' => null,
        ];
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function replaceSpace($value)
    {
        return str_replace(' ', '_', $value);
    }
}
