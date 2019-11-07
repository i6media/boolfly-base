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
use Boolfly\Megamenu\Model\Source\LinkType;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Cms\Model\ResourceModel\Page\Collection as PageCollection;
use Magento\Framework\UrlInterface;

/**
 * Class ItemUrl
 *
 * @package Boolfly\Megamenu\Model\Menu
 */
class ItemUrl
{

    /**
     * @var ItemInterface
     */
    protected $item;

    /**
     * @var CategoryCollection
     */
    protected $categoryCollection;

    /**
     * @var PageCollection
     */
    protected $pageCollection;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var PageCollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ItemUrl constructor.
     *
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param PageCollectionFactory $pageCollectionFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        PageCollectionFactory $pageCollectionFactory,
        UrlInterface $urlBuilder
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get Category Collection
     *
     * @return CategoryCollection
     */
    public function getCategoryCollection()
    {
        if ($this->categoryCollection === null) {
            $this->categoryCollection = $this->categoryCollectionFactory->create();
            $this->categoryCollection->addIsActiveFilter()
                ->addFieldToSelect('entity_id')
                ->addUrlRewriteToResult();
        }

        return $this->categoryCollection;
    }

    /**
     * Get Cms page Collection
     *
     * @return PageCollection
     */
    public function getPageCollection()
    {
        if ($this->pageCollection === null) {
            $this->pageCollection = $this->pageCollectionFactory->create();
        }

        return $this->pageCollection;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function setItem(Item $item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Get Item
     *
     * @return ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Get Item Url
     *
     * @return string
     */
    public function getItemUrl()
    {
        if ($item = $this->getItem()) {
            $linkType = $item->getLinkType();
            if ($linkType == LinkType::CATEGORY_LINK && $item->getCategoryId()) {
                /** @var \Magento\Catalog\Model\Category $category */
                $category = $this->getCategoryCollection()->getItemById((int)$item->getCategoryId());
                if ($category && $category->getId()) {
                    return $category->getUrl();
                }
            } else if ($linkType == LinkType::CUSTOM_LINK && $item->getCustomLink()) {
                return $item->getCustomLink();
            } else if ($linkType == LinkType::CMS_PAGE_LINK && $item->getCmsPageId()) {
                /** @var \Magento\Cms\Model\Page $cmsPage */
                $cmsPage = $this->getPageCollection()->getItemById((int)$item->getCmsPageId());
                if ($cmsPage && $cmsPage->getIdentifier()) {
                    return $this->urlBuilder->getUrl(null, ['_direct' => $cmsPage->getIdentifier()]);
                }
            }
        }

        return '#';
    }
}
