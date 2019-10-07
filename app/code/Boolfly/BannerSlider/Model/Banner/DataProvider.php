<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Banner Slider
 */
namespace Boolfly\BannerSlider\Model\Banner;

use Boolfly\BannerSlider\Model\ResourceModel\Banner\CollectionFactory;
use Boolfly\BannerSlider\Model\ResourceModel\Banner\Collection;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Registry;
use Boolfly\BannerSlider\Api\Data\BannerInterface;
use Boolfly\BannerSlider\Model\ImageField;

/**
 * Class DataProvider
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
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Initialize dependencies.
     *
     * @param string                      $name
     * @param string                      $primaryFieldName
     * @param string                      $requestFieldName
     * @param CollectionFactory           $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param array                       $meta
     * @param array                       $data
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
        /** @var BannerInterface | \Boolfly\BannerSlider\Model\Banner $banner */
        $banner = $this->coreRegistry->registry('current_banner');
        if ($banner->getId()) {
            $bannerData = $banner->getData();
            foreach (ImageField::getField() as $field) {
                unset($bannerData[$field]);
            }

            if ($banner->getImageDesktopUrl()) {
                $bannerData['image_desktop'][0]['name'] = $banner->getData('image_desktop');
                $bannerData['image_desktop'][0]['url']  = $banner->getImageDesktopUrl();
            }

            if ($banner->getImageTabletUrl()) {
                $bannerData['image_tablet'][0]['name'] = $banner->getData('image_tablet');
                $bannerData['image_tablet'][0]['url']  = $banner->getImageTabletUrl();
            }
            if ($banner->getImageDesktopUrl()) {
                $bannerData['image_mobile'][0]['name'] = $banner->getData('image_mobile');
                $bannerData['image_mobile'][0]['url']  = $banner->getImageMobileUrl();
            }
            $this->loadedData[$banner->getId()] = $bannerData;
        } else {
            $this->loadedData = [];
        }

        return $this->loadedData;
    }
}
