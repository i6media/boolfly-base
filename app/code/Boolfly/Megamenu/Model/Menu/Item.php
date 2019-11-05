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

use Boolfly\Megamenu\Api\Data\ItemInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Boolfly\Megamenu\Model\ResourceModel\Menu\Item as ItemResourceModel;

/**
 * Class Item
 *
 * @package Boolfly\Megamenu\Model\Menu
 */
class Item extends AbstractModel implements ItemInterface, IdentityInterface
{

    /**#@-*/
    protected $_eventPrefix = 'boolfly_megamenu_item';

    /**
     * Event Object
     *
     * @var string
     */
    protected $_eventObject = 'megamenu_item';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(ItemResourceModel::class);
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

    /**
     * Get Link Type
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getLinkType()
    {
        return $this->_getData(self::LINK_TYPE);
    }

    /**
     * Set Link Type
     *
     * @param string $linkType
     *
     * @return $this
     * @since 1.0.0
     */
    public function setLinkType($linkType)
    {
        return $this->setData(self::LINK_TYPE, $linkType);
    }

    /**
     * Set Link Target
     *
     * @param string $linkTarget
     *
     * @return string|null
     * @since 1.0.0
     */
    public function setLinkTarget($linkTarget)
    {
        return $this->setData(self::LINK_TARGET, $linkTarget);
    }

    /**
     * Get Link Target
     *
     * @return boolean|null
     * @since 1.0.0
     */
    public function getLinkTarget()
    {
        return $this->_getData(self::LINK_TARGET);
    }

    /**
     * Set Category Id
     *
     * @param string $catId
     *
     * @return $this
     * @since 1.0.0
     */
    public function setCategoryId($catId)
    {
        return $this->setData(self::CATEGORY_ID, $catId);
    }

    /**
     * Set Category Id
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getCategoryId()
    {
        return $this->_getData(self::CATEGORY_ID);
    }

    /**
     * Set Cms Page Id
     *
     * @param string $cmsPageId
     *
     * @return $this
     * @since 1.0.0
     */
    public function setCmsPageId($cmsPageId)
    {
        return $this->setData(self::CMS_PAGE_ID, $cmsPageId);
    }

    /**
     * Get Cms Page Id
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getCmsPageId()
    {
        return $this->_getData(self::CMS_PAGE_ID);
    }

    /**
     * Set Custom Link
     *
     * @param string $customLink
     *
     * @return $this
     * @since 1.0.0
     */
    public function setCustomLink($customLink)
    {
        return $this->setData(self::CUSTOM_LINK, $customLink);
    }

    /**
     * Get Custom Link
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getCustomLink()
    {
        return $this->_getData(self::CUSTOM_LINK);
    }

    /**
     * @return $this
     */
    public function loadItemContent()
    {
        $this->getResource()->loadContentData($this);

        return $this;
    }
}
