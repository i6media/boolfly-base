<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Banner Slider
 */
namespace Boolfly\BannerSlider\Model\Slider;

use Boolfly\BannerSlider\Helper\Data;
use Magento\Framework\Api\Filter;
use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Registry;
use Boolfly\BannerSlider\Api\Data\BannerInterface;
use Boolfly\BannerSlider\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{

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
     * @var BannerCollectionFactory
     */
    private $bannerCollectionFactory;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * DataProvider constructor.
     *
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param Registry                $registry
     * @param Data                    $helperData
     * @param UrlInterface            $urlBuilder
     * @param BannerCollectionFactory $bannerCollectionFactory
     * @param array                   $meta
     * @param array                   $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Registry $registry,
        Data $helperData,
        UrlInterface $urlBuilder,
        BannerCollectionFactory $bannerCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->coreRegistry            = $registry;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->helperData = $helperData;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        /** @var BannerInterface | \Boolfly\BannerSlider\Model\Slider $slider */
        $slider = $this->coreRegistry->registry('current_slider');
        if ($slider->getId()) {
            $assignedBanners = $slider->getDataByPath('banners/assigned_banners');
            if (is_array($assignedBanners) && !empty($assignedBanners)) {
                $bannerIds = array_keys($assignedBanners);
                if (!empty($bannerIds)) {
                    $bannerCollection = $this->bannerCollectionFactory->create();
                    $bannerCollection->addFieldToFilter('banner_id', ['in' => $bannerIds]);
                    $newBannerData = [];
                    /** @var \Boolfly\BannerSlider\Model\Banner $banner */
                    foreach ($bannerCollection as $banner) {
                        $newBannerData[] = [
                            'banner_id' => $banner->getId(),
                            'title' => $banner->getTitle(),
                            'image_desktop' => $this->helperData->getResizeImage($banner->getData('image_desktop'), null, 50),
                            'banner_link' => $this->urlBuilder->getUrl('bannerslider/banner/edit', ['id' => $banner->getId()]),
                            'position' => $assignedBanners[$banner->getId()],
                        ];
                    }
                    usort($newBannerData, function ($a, $b) {
                        return $a['position'] <=> $b['position'];
                    });
                    $slider->setData('banners', ['assigned_banners' => $newBannerData]);
                }
            }

            $sliderData = $slider->getData();

            $this->loadedData[$slider->getId()] = $sliderData;
        } else {
            $this->loadedData = [];
        }
        return $this->loadedData;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        return [];
    }
}
