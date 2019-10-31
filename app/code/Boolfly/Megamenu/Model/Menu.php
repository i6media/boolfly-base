<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Megamenu
 */
namespace Boolfly\Megamenu\Model;

use Boolfly\Megamenu\Api\Data\MenuInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Boolfly\Megamenu\Model\ResourceModel\Menu as MenuResourceModel;

/**
 * Class Menu
 *
 * @package Boolfly\Megamenu\Model
 */
class Menu extends AbstractModel implements MenuInterface, IdentityInterface
{

    /**#@-*/
    protected $_eventPrefix = 'boolfly_megamenu';

    /**
     * Event Object
     *
     * @var string
     */
    protected $_eventObject = 'megamenu';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(MenuResourceModel::class);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        $identities = [
            self::CACHE_TAG . '_' . $this->getId(),
        ];

        if (!$this->getId() || $this->isDeleted()) {
            $identities[] = self::CACHE_TAG;
        }

        return array_unique($identities);
    }

    /**
     * Get Title
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getTitle()
    {
        return $this->_getData(self::TITLE);
    }

    /**
     * Set Title
     *
     * @param string $title
     *
     * @return $this
     * @since 1.0.0
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get Status
     *
     * @return boolean
     * @since 1.0.0
     */
    public function getStatus()
    {
        return (bool)$this->_getData(self::STATUS);
    }

    /**
     * Set Status
     *
     * @param integer|boolean $status
     *
     * @return $this
     * @since 1.0.0
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, (bool)$status);
    }

    /**
     * Get Identifier
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getIdentifier()
    {
        return $this->_getData(self::IDENTIFIER);
    }

    /**
     * Set Identifier
     *
     * @param string $identifier
     *
     * @return $this
     * @since 1.0.0
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * Set Scroll To Fixed
     *
     * @param string $scrollToFixed
     *
     * @return string|null
     * @since 1.0.0
     */
    public function setScrollToFixed($scrollToFixed)
    {
        return $this->setData(self::SCROLL_TO_FIXED, $scrollToFixed);
    }

    /**
     * Get Scroll To Fixed
     *
     * @return boolean|null
     * @since 1.0.0
     */
    public function getScrollToFixed()
    {
        return $this->_getData(self::SCROLL_TO_FIXED);
    }

    /**
     * Set Desktop Template
     *
     * @param string $template
     *
     * @return $this
     * @since 1.0.0
     */
    public function setDesktopTemplate($template)
    {
        return $this->setData(self::DESKTOP_TEMPLATE, $template);
    }

    /**
     * Set Desktop Template
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getDesktopTemplate()
    {
        return $this->_getData(self::DESKTOP_TEMPLATE);
    }

    /**
     * Set Additional Class
     *
     * @param string $class
     *
     * @return $this
     * @since 1.0.0
     */
    public function setAdditionalClass($class)
    {
        return $this->setData(self::ADDITIONAL_CLASS, $class);
    }

    /**
     * Get Additional Class
     *
     * @return string|boolean
     * @since 1.0.0
     */
    public function getAdditionalClass()
    {
        return $this->_getData(self::ADDITIONAL_CLASS);
    }
}
