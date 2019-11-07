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

/**
 * Html page top menu block
 *
 * @api
 * @since 100.0.2
 */
class Topmenu extends Template implements IdentityInterface
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
     * Topmenu constructor.
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
     *
     */
    public function getMenuItems()
    {
        /** @var \Boolfly\Megamenu\Model\Menu $menu */
        $menu = $this->menuFactory->create();
        $menu->load(8);
        //@TODO

        return $menu->getMenuTree();
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
    public function getLinkHtml($item)
    {
        return $this->getItemBlock()
            ->setItem($item)
            ->setTemplate('Boolfly_Megamenu::html/link.phtml')->toHtml();
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
            ->setTemplate('Boolfly_Megamenu::html/content.phtml')->toHtml();
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        // TODO: Implement getIdentities() method.
        return [];
    }

}