<?php
 /************************************************************
  * *
  *  * Copyright © 2019 Boolfly. All rights reserved.
  *  * See COPYING.txt for license details.
  *  *
  *  * @author    info@boolfly.com
  * *  @project   Banner Slider
  */
namespace Boolfly\BannerSlider\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Boolfly\BannerSlider\Setup\InstallSchema;

/**
 * Class Slider
 *
 * @package Boolfly\BannerSlider\Model\ResourceModel
 */
class Slider extends AbstractDb
{

    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(InstallSchema::SLIDER_TABLE_NAME, 'slider_id');
    }
}
