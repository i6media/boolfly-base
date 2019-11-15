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
use Magento\Store\Model\Store;

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
        $this->_map['fields']['menu_id'] = 'main_table.menu_id';
        $this->_map['fields']['store']   = 'megamenu_store.store_id';
    }

    /**
     * @return mixed
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('boolfly_megamenu_store', $this->getIdFieldName());
        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('boolfly_megamenu_store', $this->getIdFieldName());
    }

    /**
     * Join store relation table if there is store filter
     *
     * @param string      $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinStoreRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['megamenu_store' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = megamenu_store.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Add filter by store
     *
     * @param integer|array|\Magento\Store\Model\Store $store
     * @param boolean                                  $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     * Perform adding filter by store
     *
     * @param integer|array|Store $store
     * @param boolean             $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => $store], 'public');
    }


    /**
     * Add field filter to collection
     *
     * @param array|string              $field
     * @param string|integer|array|null $condition
     * @return mixed
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
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

    /**
     * Perform operations after collection load
     *
     * @param string      $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function performAfterLoad($tableName, $linkField)
    {
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select     = $connection->select()->from(['megamenu_store' => $this->getTable($tableName)])
                ->where('megamenu_store.' . $linkField . ' IN (?)', $linkedIds);
            $result     = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }
                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
    }
}
