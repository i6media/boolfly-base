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

use Magento\Framework\DataObject;
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
     * @var DataObject
     */
    protected $dataObject;

    /**
     * Menu constructor.
     *
     * @param Template\Context     $context
     * @param DataObject           $dataObject
     * @param MenuInterfaceFactory $menuFactory
     * @param array                $data
     */
    public function __construct(
        Template\Context $context,
        DataObject $dataObject,
        MenuInterfaceFactory $menuFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataObject  = $dataObject;
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
     * @return integer
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
     * Get Menu Template: vertical or horizontal
     *
     * @return null|string
     */
    public function getMenuTemplate()
    {
        return $this->getMenu()->getDesktopTemplate();
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
     */
    public function getAdditionalClasses($item)
    {
        /** @var \Boolfly\Megamenu\Model\Menu\Item $item */
        $item  = $this->getItemObject($item);
        $class = [];
        if ($item->getData('first')) {
            $class[] = 'first';
        }
        if ($item->getData('last')) {
            $class[] = 'last';
        }
        if ($item->getChildren()) {
            $class[] = 'has-children';
        }

        return join(' ', $class);
    }

    /**
     * @param $item
     * @return DataObject
     */
    private function getItemObject($item)
    {
        $this->dataObject->setData($item);

        return $this->dataObject;
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
        return $this->getMenu()->getIdentities();
        ;
    }
}
