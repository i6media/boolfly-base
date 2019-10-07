<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Banner Slider
 */
namespace Boolfly\BannerSlider\Block\Adminhtml\Edit;

use Magento\Backend\Block\Template;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 *
 * @package Boolfly\BannerSlider\Block\Adminhtml\Edit
 */
class DeleteButton extends Template implements ButtonProviderInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * DeleteButton constructor.
     *
     * @param Template\Context $context
     * @param Registry         $registry
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    /**
     * Delete button
     *
     * @return array
     */
    public function getButtonData()
    {
        if ($this->getBanner() && $this->getBanner()->getId()) {
            return [
                'id' => 'delete',
                'label' => __('Delete'),
                'on_click' => "deleteConfirm('" .__('Are you sure you want to delete this banner?') ."', '"
                    . $this->getDeleteUrl() . "', {data: {}})",
                'class' => 'delete',
                'sort_order' => 10
            ];
        }

        return [];
    }

    /**
     * Get Banner
     *
     * @return null|\Boolfly\BannerSlider\Api\Data\BannerInterface
     */
    public function getBanner()
    {
        return $this->registry->registry('current_banner');
    }

    /**
     * @param array $args
     * @return string
     */
    public function getDeleteUrl(array $args = [])
    {
        $params = array_merge($this->getDefaultUrlParams(), $args);
        return $this->getUrl('catalog/*/delete', $params);
    }

    /**
     * @return array
     */
    protected function getDefaultUrlParams()
    {
        return ['_current' => true, '_query' => ['isAjax' => null]];
    }
}
