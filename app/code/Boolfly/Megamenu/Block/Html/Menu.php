<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Mega Menu
 */
namespace Boolfly\Megamenu\Block\Html;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Boolfly\Megamenu\Api\Data\MenuInterfaceFactory;
use Boolfly\Megamenu\Api\Data\MenuInterface;

/**
 * Menu block
 *
 * @api
 * @since 100.0.2
 */
class Menu extends Template implements IdentityInterface
{

    /**
     * @var MenuInterfaceFactory
     */
    private $menuFactory;

    /**
     * @var Item
     */
    private $itemBlock;

    /**
     * @var MenuInterface
     */
    protected $menuModel;

    /**
     * @var int
     */
    protected $menuId;

    /**
     * Menu constructor.
     *
     * @param Template\Context $context
     * @param MenuInterfaceFactory $menuFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        MenuInterfaceFactory $menuFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->menuFactory = $menuFactory;
    }

    /**
     * @param $menuId
     * @return $this
     */
    public function setMenuId($menuId)
    {
        $this->menuId = $menuId;
        return $this;
    }

    /**
     * @return int
     */
    public function getMenuId()
    {
        return $this->menuId;
    }

    /**
     * @param $menu
     * @return $this
     */
    public function setMenu($menu)
    {
        $this->menuModel = $menu;
        return $this;
    }

    /**
     * @return \Boolfly\Megamenu\Model\Menu
     */
    public function getMenu()
    {
        if ($this->menuModel === null) {
            /** @var \Boolfly\Megamenu\Model\Menu $menu */
            $menu = $this->menuFactory->create();
            $menu->load($this->getMenuId());
            $this->menuModel = $menu;
        }

        return $this->menuModel;
    }

    /**
     * Menu Tree
     *
     * @return array
     */
    public function getMenuTree()
    {
        return $this->getMenu()->getMenuTree();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemBlock()
    {
        if ($this->itemBlock === null) {
            $this->itemBlock = $this->getLayout()->createBlock(Item::class);
        }

        return $this->itemBlock;
    }

    /**
     * @param $item
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildContentHtml($item)
    {
        return $this->getItemBlock()
            ->setItem($item)
            ->setTemplate('Boolfly_Megamenu::html/content.phtml')
            ->toHtml();
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [];
    }
}
