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
        //@TODO

        //Sample Data To test
        $this->loadedData[1] = [
            'menu_tree' => [
                '9_3' => [
                    'record_id' => '9_3',
                    'menu_children' => ['10_2', '10_3'],
                    'title' => 'Fashion',
                    'position' => 2
                ],
                '10_2' => [
                    'record_id' => '10_2',
                    'menu_children' => ['11_3', '11_4'],
                    'title' => 'Men',
                    'position' => 3
                ],
                '10_3' => [
                    'record_id' => '10_3',
                    'menu_children' => ['12_1', '12_2', '12_3'],
                    'title' => 'Women',
                    'position' => 3
                ],
                '11_3' => [
                    'record_id' => '11_3',
                    'menu_children' => [],
                    'title' => 'Top',
                    'position' => 3
                ],
                '11_4' => [
                    'record_id' => '11_3',
                    'menu_children' => [],
                    'title' => 'Bottom',
                    'position' => 3
                ],
                '12_1' => [
                    'record_id' => '12_1',
                    'menu_children' => [],
                    'title' => 'Shirt',
                    'position' => 3
                ],
                '12_2' => [
                    'record_id' => '12_2',
                    'menu_children' => [],
                    'title' => 'Dress',
                    'position' => 3
                ],
                '12_3' => [
                    'record_id' => '12_3',
                    'menu_children' => [],
                    'title' => 'Jeans',
                    'position' => 3
                ]
            ]
        ];

        return $this->loadedData;
    }
}
