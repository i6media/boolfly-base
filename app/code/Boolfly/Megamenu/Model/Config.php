<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Megamenu
 */
namespace Boolfly\Megamenu\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**@#%
     * @const
     */
    const XML_PATH_MEGAMENU_ENABLE = 'megamenu/settings/enable';

    const XML_PATH_DEFAULT_MEGAMENU = 'megamenu/settings/default_menu';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get Configuration
     *
     * @param $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORES);
    }

    /**
     * Enable Mega menu
     *
     * @return boolean
     */
    public function isEnable()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_MEGAMENU_ENABLE, ScopeInterface::SCOPE_STORES);
    }

    /**
     * Get Default Banner Id
     *
     * @return mixed
     */
    public function getDefaultBannerId()
    {
        return $this->getConfig(self::XML_PATH_DEFAULT_MEGAMENU);
    }
}
