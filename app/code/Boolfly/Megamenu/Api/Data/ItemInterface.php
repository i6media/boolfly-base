<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Mega Menu
 */
namespace Boolfly\Megamenu\Api\Data;

/**
 * Interface ItemInterface
 *
 * @package Boolfly\Megamenu\Api\Data
 */
interface ItemInterface
{

    /**#@+
     * Constants Cache Tag
     */
    const CACHE_TAG = 'boolfly_megamenu_item';

    /**#@+
     * Constants defined for keys of data array
     */
    const ITEM = 'item_id';

    const TITLE = 'title';

    const ADDITIONAL_CLASS = 'additional_class';

    const LINK_TYPE = 'link_type';

    const CUSTOM_LINK = 'custom_link';

    const CATEGORY_ID = 'category_id';

    const CMS_PAGE_ID = 'cms_page_id';

    const LINK_TARGET = 'link_target';

    const STATUS = 'status';

    const RECORD_ID = 'record_id';

    const PARENT_ID = 'parent_id';

    const LEVEL = 'level';

    /**
     * Get Id
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getId();

    /**
     * Get Title
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getTitle();

    /**
     * Set Title
     *
     * @param string $title
     *
     * @return $this
     * @since 1.0.0
     */
    public function setTitle($title);

    /**
     * Get Status
     *
     * @return boolean
     * @since 1.0.0
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param integer|boolean $status
     *
     * @return $this
     * @since 1.0.0
     */
    public function setStatus($status);

    /**
     * Get Link Type
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getLinkType();

    /**
     * Set Link Type
     *
     * @param string $linkType
     *
     * @return $this
     * @since 1.0.0
     */
    public function setLinkType($linkType);

    /**
     * Set Link Target
     *
     * @param string $linkTarget
     *
     * @return string|null
     * @since 1.0.0
     */
    public function setLinkTarget($linkTarget);


    /**
     * Get Link Target
     *
     * @return boolean|null
     * @since 1.0.0
     */
    public function getLinkTarget();

    /**
     * Set Category Id
     *
     * @param string $catId
     *
     * @return $this
     * @since 1.0.0
     */
    public function setCategoryId($catId);

    /**
     * Set Category Id
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getCategoryId();

    /**
     * Set Cms Page Id
     *
     * @param string $cmsPageId
     *
     * @return $this
     * @since 1.0.0
     */
    public function setCmsPageId($cmsPageId);

    /**
     * Get Cms Page Id
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getCmsPageId();

    /**
     * Set Custom Link
     *
     * @param string $customLink
     *
     * @return $this
     * @since 1.0.0
     */
    public function setCustomLink($customLink);

    /**
     * Get Custom Link
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getCustomLink();

    /**
     * Set Additional Class
     *
     * @param string $class
     *
     * @return $this
     * @since 1.0.0
     */
    public function setAdditionalClass($class);

    /**
     * Get Additional Class
     *
     * @return string|boolean
     * @since 1.0.0
     */
    public function getAdditionalClass();
}
