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
 * Interface MenuInterface
 *
 * @package Boolfly\Megamenu\Api\Data
 */
interface MenuInterface
{

    /**#@+
     * Constants Cache Tag
     */
    const CACHE_TAG = 'boolfly_megamenu';

    /**#@+
     * Constants defined for keys of data array
     */
    const MENU_ID = 'menu_id';

    const TITLE = 'title';

    const IDENTIFIER = 'identifier';

    const ADDITIONAL_CLASS = 'additional_class';

    const SCROLL_TO_FIXED = 'scroll_fixed';

    const DESKTOP_TEMPLATE = 'desktop_template';

    const STATUS = 'status';

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
     * Get Identifier
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getIdentifier();

    /**
     * Set Identifier
     *
     * @param string $identifier
     *
     * @return $this
     * @since 1.0.0
     */
    public function setIdentifier($identifier);

    /**
     * Set Scroll To Fixed
     *
     * @param string $scrollToFixed
     *
     * @return string|null
     * @since 1.0.0
     */
    public function setScrollToFixed($scrollToFixed);


    /**
     * Get Scroll To Fixed
     *
     * @return boolean|null
     * @since 1.0.0
     */
    public function getScrollToFixed();

    /**
     * Set Desktop Template
     *
     * @param string $template
     *
     * @return $this
     * @since 1.0.0
     */
    public function setDesktopTemplate($template);

    /**
     * Set Desktop Template
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getDesktopTemplate();

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
