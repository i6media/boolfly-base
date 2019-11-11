<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Megamenu
 */
namespace Boolfly\Megamenu\Model\Menu;

use Boolfly\Megamenu\Model\ResourceModel\Menu\CollectionFactory;
use Boolfly\Megamenu\Model\ResourceModel\Menu\Collection;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Registry;

/**
 * Class DataProvider
 *
 * @package Boolfly\Megamenu\Model\Menu
 */
class DataProvider extends AbstractDataProvider
{

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * DataProvider constructor.
     *
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Registry          $registry
     * @param array             $meta
     * @param array             $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        $this->collection   = $collectionFactory->create();
        $this->coreRegistry = $registry;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        /** @var \Boolfly\Megamenu\Model\Menu $model */
        $model = $this->coreRegistry->registry('current_megamenu');
        if ($id = $model->getId()) {
            $data            = $model->getData();
            $itemsCollection = $model->getItemsCollection();
            $itemsCollection->load();
            if ($itemsCollection->getSize() > 0) {
                /** @var \Boolfly\Megamenu\Model\Menu\Item $item */
                foreach ($itemsCollection as $item) {
                    $item->loadItemContent();
                    $data['menu_tree'][] = $item->getData();
                }
            }
            $this->loadedData[$id] = $data;
        }

        return $this->loadedData;
    }
}
