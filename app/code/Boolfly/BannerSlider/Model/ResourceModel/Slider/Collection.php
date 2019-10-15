<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Banner Slider
 */
namespace Boolfly\BannerSlider\Model\ResourceModel\Slider;

use Boolfly\BannerSlider\Model\ResourceModel\Slider as SliderResourceModel;
use Boolfly\BannerSlider\Model\Slider;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Boolfly\BannerSlider\Model\ResourceModel\Slider
 */
class Collection extends AbstractCollection
{

    /**
     * Primary column
     *
     * @var string
     */
    protected $_idFieldName = 'slider_id';

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Slider::class, SliderResourceModel::class);
    }

    /**
     * Get Resource
     *
     * @return SliderResourceModel|mixed
     */
    public function getResource()
    {
        return parent::getResource();
    }

    /**
     * @param $pageId
     * @return $this
     */
    public function addCmsPageToFilter($pageId)
    {
        if ($pageId && is_numeric($pageId)) {
            $conditions = $this->getConnection()->quoteInto(
                'main_table.slider_id = cms_page.slider_id AND cms_page.page_id = ?', $pageId
            );
            $this->getSelect()->joinInner(
                ['cms_page' => $this->getResource()->getSliderCmsPageTable()],
                $conditions,
                'page_id'
            );
        }

        return $this;
    }
}
