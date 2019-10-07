<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Banner Slider
 */
namespace Boolfly\BannerSlider\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Boolfly\BannerSlider\Api\Data\SliderInterfaceFactory;

/**
 * Class AbstractSlider
 *
 * @package Boolfly\BannerSlider\Controller\Adminhtml
 */
abstract class AbstractSlider extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_BannerSlider::slider';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Date filter instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @var SliderInterfaceFactory
     */
    protected $sliderFactory;

    /**
     * AbstractSlider constructor.
     *
     * @param Context                $context
     * @param Registry               $coreRegistry
     * @param Date                   $dateFilter
     * @param SliderInterfaceFactory $sliderFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        SliderInterfaceFactory $sliderFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_dateFilter   = $dateFilter;
        $this->sliderFactory = $sliderFactory;
    }

    /**
     * Init action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Boolfly_BannerSlider::manager'
        )->_addBreadcrumb(
            __('Manage Slider'),
            __('Manage Slider')
        );
        return $this;
    }
}
