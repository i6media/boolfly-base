<?php
/***********************************************************************
 * *
 *  *
 *  * @copyright Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  * @author    info@boolfly.com
 * *
 */
namespace Boolfly\BannerSlider\Block\Cms;

use Boolfly\BannerSlider\Model\ResourceModel\Banner\Collection;
use Boolfly\BannerSlider\Model\ResourceModel\Slider\Collection as SliderCollection;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template as Template;
use Boolfly\BannerSlider\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;
use Boolfly\BannerSlider\Model\ResourceModel\Slider\CollectionFactory as SliderCollectionFactory;
use Boolfly\BannerSlider\Model\Source\Effect;
use Magento\Cms\Helper\Page;

/**
 * Class Slider
 *
 * @package Boolfly\BannerSlider\Block
 */
class Slider extends Template
{
    /**
     * @var BannerCollectionFactory
     */
    private $bannerCollectionFactory;

    /**
     * @var SliderCollectionFactory
     */
    private $sliderCollectionFactory;

    /**
     * @var \Boolfly\BannerSlider\Model\Slider
     */
    protected $slider;

    /**
     * @var Collection
     */
    protected $bannerCollection;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * Slider constructor.
     *
     * @param Template\Context $context
     * @param BannerCollectionFactory $bannerCollectionFactory
     * @param SliderCollectionFactory $sliderCollectionFactory
     * @param PageFactory $pageFactory
     * @param Json $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        BannerCollectionFactory $bannerCollectionFactory,
        SliderCollectionFactory $sliderCollectionFactory,
        PageFactory $pageFactory,
        Json $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->sliderCollectionFactory = $sliderCollectionFactory;
        $this->serializer = $serializer;
        $this->pageFactory = $pageFactory;
    }

    /**
     * Get banner collection
     *
     * @return bool|Collection
     */
    public function getBannerCollection()
    {
        if ($this->bannerCollection === null) {
            $this->bannerCollection = false;
            if ($this->getSlider() && ($sliderId = $this->getSlider()->getId())) {
                $bannerCollection = $this->bannerCollectionFactory->create();
                $bannerCollection->addSliderToFilter($sliderId);
                $bannerCollection->addActiveStatusFilter();
                $this->bannerCollection = $bannerCollection;
            }
        }

        return $this->bannerCollection;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getBannerCollection() && $this->getBannerCollection()->getSize() > 0) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * Get Slider
     *
     * @return bool|\Boolfly\BannerSlider\Model\Slider
     */
    public function getSlider()
    {
        if ($this->slider === null) {
            $this->slider = false;
            $pageId = $this->getPageId();
            if ($pageId) {
                /** @var SliderCollection $sliderCollection */
                $sliderCollection = $this->sliderCollectionFactory->create();
                $sliderCollection->addCmsPageToFilter($pageId);
                $sliderCollection->addActiveStatusFilter();
                if ($sliderCollection->getSize() > 0) {
                    $this->slider = $sliderCollection->getFirstItem();
                }
            }
        }

        return $this->slider;
    }

    /**
     * @return $this|mixed
     */
    protected function getPageId()
    {
        if ($this->isHomepage()) {
            $pageId = $this->_scopeConfig->getValue(Page::XML_PATH_HOME_PAGE);
            $pageId = $this->pageFactory->create()->load($pageId);
        } else {
            $pageId = $this->getRequest()->getParam('page_id', false);
        }

        return $pageId;
    }

    /**
     * @return string
     */
    public function getJsonData()
    {
        $config = [
            'fade' => $this->isFadeEffect(),
            'autoplay' => $this->getSlider()->isAutoPlay(),
            'autoplaySpeed' => $this->getSlider()->getSpeed()?: 5000,
        ];

        return $this->serializer->serialize($config);
    }

    /**
     * @return bool|string
     */
    protected function isFadeEffect()
    {
        $slider = $this->getSlider();
        return $slider && $slider->getAnimationEffect() == Effect::FADE_EFFECT;
    }

    /**
     * Check is homepage
     *
     * @return bool
     */
    public function isHomepage()
    {
        if ($this->getRequest()->getFullActionName() === 'cms_index_index') {
            return true;
        }

        return false;
    }
}