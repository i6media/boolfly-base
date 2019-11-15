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

use Boolfly\Megamenu\Model\Menu as MenuModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Boolfly\Megamenu\Api\Data\MenuInterfaceFactory;
use Magento\Theme\Block\Html\Topmenu as TopmenuDefault;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Boolfly\Megamenu\Model\Config as MenuConfig;

/**
 * Html page top menu block
 *
 * @api
 * @since 100.0.2
 */
class Topmenu extends TopmenuDefault implements IdentityInterface
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
     * @var MenuConfig
     */
    private $menuConfig;

    /**
     * @var MenuModel
     */
    protected $menuModel;

    /**
     * Topmenu constructor.
     *
     * @param Template\Context     $context
     * @param MenuInterfaceFactory $menuFactory
     * @param NodeFactory          $nodeFactory
     * @param MenuConfig           $menuConfig
     * @param TreeFactory          $treeFactory
     * @param array                $data
     */
    public function __construct(
        Template\Context $context,
        MenuInterfaceFactory $menuFactory,
        NodeFactory $nodeFactory,
        MenuConfig $menuConfig,
        TreeFactory $treeFactory,
        array $data = []
    ) {
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
        $this->menuFactory = $menuFactory;
        $this->menuConfig  = $menuConfig;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->menuConfig->isEnable() && $this->getMenuModel()->getId()) {
            $this->setTemplate('Boolfly_Megamenu::topmenu.phtml');
            /** @var Menu $childBlock */
            $childBlock = $this->getChildBlock('megamenu_topnav');
            if ($childBlock) {
                $childBlock->setMenu($this->getMenuModel());
            }
        } else {
            $this->setTemplate('Magento_Theme::html/topmenu.phtml');
        }
        return parent::_toHtml();
    }

    /**
     * @return \Boolfly\Megamenu\Api\Data\MenuInterface|MenuModel
     */
    public function getMenuModel()
    {
        if ($this->menuModel === null) {
            $this->menuModel = $this->menuFactory->create();
            $this->menuModel->load($this->menuConfig->getDefaultBannerId());
        }

        return $this->menuModel;
    }

    /**
     * Get Menu Items
     *
     * @return array
     */
    public function getMenuItems()
    {
        return $this->getMenuModel()->getMenuTree();
    }

    /**
     * Get Item Block
     *
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
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        if ($this->menuConfig->isEnable()) {
            $identities   = $this->getMenuModel()->getIdentities();
            $identities[] = 'TOP_MENU';
            return $identities;
        }

        return parent::getIdentities();
    }
}
